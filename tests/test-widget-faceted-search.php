<?php
/**
 * @since 3.27.7.3
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

use Wordlift\Widgets\Faceted_Search\Faceted_Search_Template_Endpoint;

/**
 * Class Faceted_Search_Widget_Test
 * @group widget
 */
class Faceted_Search_Widget_Test extends Wordlift_Unit_Test_Case {

	private $template_route = '/wordlift/v1/faceted-search/template';

	public function setUp() {
		parent::setUp();
		global $wp_rest_server, $wp_filter;
		// Resetting global filters, since we want our test
		// to run independently without global state.
		$wp_filter      = array();
		$instance       = new \Wordlift\Widgets\Async_Template_Decorator( new Wordlift_Faceted_Search_Shortcode() );
		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;
		do_action( 'rest_api_init' );
	}


	public function test_template_end_point_should_return_200() {
		$request = new WP_REST_Request( 'POST', $this->template_route );
		$request->set_header( 'content-type', 'application/json' );
		$json_data = json_encode( array( 'template_id' => 'foo' ) );
		$request->set_body( $json_data );
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status(), 'Faceted search template endpoint not registered' );
	}

	public function test_when_registered_template_via_filter_should_get_the_filter_on_the_endpoint() {

		$template_id = 'foo';

		$template = 'my-template';

		add_filter( 'wordlift_faceted_search_templates', function ( $templates ) use ( $template, $template_id ) {
			$templates[ $template_id ] = $template;

			return $templates;
		} );

		$request = $this->create_template_request( $template_id );
		/**
		 * @var $response WP_REST_Response
		 */
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status() );
		$data = $response->get_data();
		$this->assertArrayHasKey( 'template', $data );
		/**
		 * Now that we posted the template id we should have the template
		 * in the response.
		 */
		$this->assertEquals( $template, $data['template'], 'Faceted search template not received' );

	}

	/**
	 * when we post a non registered template id, then we expect it to return
	 * a empty string.
	 */
	public function test_when_non_registered_template_should_receive_empty_string() {

		$template_id = 'foo';

		$request = $this->create_template_request( $template_id );
		/**
		 * @var $response WP_REST_Response
		 */
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );
		$data = $response->get_data();
		$this->assertArrayHasKey( 'template', $data );
		$this->assertEquals( '', $data['template'], 'Faceted search template should be empty' );

	}

	/**
	 * @param $template_id
	 *
	 * @return WP_REST_Request
	 */
	private function create_template_request( $template_id ) {
		$request = new WP_REST_Request( 'POST', $this->template_route );
		$request->set_header( 'content-type', 'application/json' );
		$json_data = json_encode( array( 'template_id' => $template_id ) );
		$request->set_body( $json_data );

		return $request;
	}


	public function test_on_do_shortcode_should_have_template_url() {
		new Wordlift_Faceted_Search_Shortcode();
		$post_id      = $this->factory()->post->create();
		$post         = get_post( $post_id );
		$result       = do_shortcode( "[wl_faceted_search template_id='foo' post_id=$post_id]" );
		$template_url = "?rest_route=/wordlift/v1/faceted-search/template";
		$this->assertTrue( strpos( $result, $template_url ) !== false, "Template url should be present in the faceted search, but got $result " );
	}


	public function create_faceted_search_post( $linked_entity, $post_type = 'post' ) {
		$post_id = $this->factory()->post->create( array( 'post_type' => $post_type ) );

		wl_core_add_relation_instance( $post_id, WL_WHO_RELATION, $linked_entity );
		if ( ! category_exists( 'faceted_search_category' ) ) {
			wp_create_category( 'faceted_search_category' );
		}
		/**
		 * @var $category WP_Term
		 */
		$this->set_faceted_search_category( $post_id );


		// set the entity type as article.
		$entity_type_service = Wordlift_Entity_Type_Service::get_instance();

		$entity_type_service->set( $post_id, 'http://schema.org/Article' );

		update_post_meta( $post_id, '_thumbnail_id', 'https://some-url-from-test.com' );

		return $post_id;
	}

	/**
	 * @param $post_id
	 */
	private function set_faceted_search_category( $post_id ) {
		$category = get_category_by_slug( 'faceted_search_category' );

		wp_set_post_categories( $post_id, array( $category->term_id ) );
	}

	public function test_faceted_search_should_return_posts_correctly_for_post() {

		$entity          = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		$post_1          = $this->create_faceted_search_post( $entity );
		$post_2          = $this->create_faceted_search_post( $entity );
		$post_3          = $this->create_faceted_search_post( $entity );
		$post_4          = $this->create_faceted_search_post( $entity );
		$post_5          = $this->create_faceted_search_post( $entity );
		$_GET['post_id'] = $post_1;
		$_GET['uniqid']  = 'random_id';
		$result          = wl_shortcode_faceted_search_origin( null );
		$this->assertCount( 4, $result['posts'] );
		$this->assertCount( 1, $result['entities'] );
	}

	public function test_faceted_search_should_return_results_correctly_for_entity() {
		$entity_1 = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		$entity_2 = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		$post_1   = $this->create_faceted_search_post( $entity_1 );
		$post_2   = $this->create_faceted_search_post( $entity_2 );
		$post_3   = $this->create_faceted_search_post( $entity_1 );
		$post_4   = $this->create_faceted_search_post( $entity_1 );
		$post_5   = $this->create_faceted_search_post( $entity_1 );
		$_GET['post_id'] = $entity_1;
		$_GET['uniqid']  = 'random_id';
		$result          = wl_shortcode_faceted_search_origin( null );
		$this->assertCount( 4, $result['posts'] );
		$this->assertCount( 1, $result['entities'] );
	}




}