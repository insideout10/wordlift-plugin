<?php
/**
 * @since ?.??.??
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

use Wordlift\Widgets\Async_Template_Decorator;

/**
 * Class Navigator_Widget_Test
 * @group widget
 */
class Navigator_Widget_Test extends Wordlift_Unit_Test_Case {

	private $template_route = '/wordlift/v1/navigator/template';

	public function setUp() {
		parent::setUp();
		global $wp_rest_server, $wp_filter;
		// Resetting global filters, since we want our test
		// to run independently without global state.
		$wp_filter = array();
		new Async_Template_Decorator( new Wordlift_Navigator_Shortcode() );
		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;
		do_action( 'rest_api_init' );
		// navigator query triggers a warning due to placeholder.
		add_filter( 'doing_it_wrong_trigger_error', '__return_false' );
	}


	public function test_template_end_point_should_return_200() {
		$request = new WP_REST_Request( 'POST', $this->template_route );
		$request->set_header( 'content-type', 'application/json' );
		$json_data = json_encode( array( 'template_id' => 'foo' ) );
		$request->set_body( $json_data );
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status(), 'Navigator template endpoint not registered' );
	}

	public function test_when_registered_template_via_filter_should_get_the_filter_on_the_endpoint() {

		$template_id = 'foo';

		$template = 'my-template';

		add_filter( 'wordlift_navigator_templates', function ( $templates ) use ( $template, $template_id ) {
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
		$this->assertEquals( $template, $data['template'], 'Navigator template not received' );

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
		$this->assertEquals( '', $data['template'], 'Navigator template should be empty' );

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
		$post_id      = $this->factory()->post->create();
		$post         = get_post( $post_id );
		$result       = do_shortcode( "[wl_navigator template_id='foo' post_id=$post_id]" );
		$template_url = "?rest_route=/wordlift/v1/navigator/template";
		$this->assertTrue( strpos( $result, $template_url ) !== false, "Template url should be present in the navigator, but got $result " );
	}

	public function test_block_type_should_have_post_types_attribute() {
		$shortcode  = new Wordlift_Navigator_Shortcode();
		$block_atts = $shortcode->get_navigator_block_attributes();
		$this->assertArrayHasKey( 'post_types', $block_atts );
		$this->assertTrue( is_array( $block_atts['post_types'] ) );
		$attribute_data = $block_atts['post_types'];
		$this->assertArrayHasKey( 'type', $attribute_data );
		$this->assertArrayHasKey( 'default', $attribute_data );
	}


	public function create_navigator_post( $linked_entity, $post_type = 'post' ) {
		$post_id = $this->factory()->post->create( array( 'post_type' => $post_type) );

		wl_core_add_relation_instance( $post_id, WL_WHO_RELATION, $linked_entity );
		if ( ! category_exists( 'navigator_test_category' ) ) {
			wp_create_category( 'navigator_test_category' );
		}
		/**
		 * @var $category WP_Term
		 */
		$category = get_category_by_slug( 'navigator_test_category' );

		wp_set_post_categories( $post_id, array( $category->term_id ) );


		// set the entity type as article.
		$entity_type_service = Wordlift_Entity_Type_Service::get_instance();

		$entity_type_service->set( $post_id, 'http://schema.org/Article' );

		update_post_meta( $post_id, '_thumbnail_id', 'https://some-url-from-test.com' );

		return $post_id;
	}


	public function test_when_post_type_not_supplied_in_navigator_shortcode_should_return_correctly() {
		// Create an entity and link all the posts to post_1.
		$entity = $this->factory()->post->create( array( 'post_type' => 'entity' ) );


		// Lets create 2 posts and 2 pages.
		$post_1 = $this->create_navigator_post( $entity );
		$post_2 = $this->create_navigator_post( $entity );
		$post_3 = $this->create_navigator_post( $entity );
		$page_1 = $this->create_navigator_post( $entity, 'page' );
		$page_2 = $this->create_navigator_post( $entity, 'page' );
		$page_3 = $this->create_navigator_post( $entity, 'page' );


		// Get navigator data.
		$_GET['post_id'] = $post_1;
		$_GET['uniqid']  = "random_id";
		$data            = _wl_navigator_get_data();
		$this->assertEquals( 4, count( $data ) );

	}

	public function test_when_the_post_type_supplied_should_restrict_by_post_type() {
		// Create an entity and link all the posts to post_1.
		$entity = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		// Lets create 2 posts and 2 pages.
		$post_1 = $this->create_navigator_post( $entity );
		$post_2 = $this->create_navigator_post( $entity );
		$post_3 = $this->create_navigator_post( $entity );
		$page_1 = $this->create_navigator_post( $entity, 'page' );
		$page_2 = $this->create_navigator_post( $entity, 'page' );
		$page_3 = $this->create_navigator_post( $entity, 'page' );
		// But we will restrict by post type.
		$_GET['post_id']    = $post_1;
		$_GET['uniqid']     = "random_id";
		$_GET['post_types'] = 'post,some-random-post-type';
		$data               = _wl_navigator_get_data();
		$this->assertEquals( 2, count( $data ) );

	}


	public function test_when_post_type_not_supplied_in_navigator_shortcode_should_return_correctly_for_entities() {
		// Create an entity and link all the posts to post_1.
		$entity = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		// Lets create 2 posts and 2 pages.
		$post_3 = $this->create_navigator_post( $entity );
		$page_1 = $this->create_navigator_post( $entity );
		$page_2 = $this->create_navigator_post( $entity );
		$page_3 = $this->create_navigator_post( $entity );
		// Get navigator data.
		$_GET['post_id'] = $entity;
		$_GET['uniqid']  = "random_id";
		$data            = _wl_navigator_get_data();
		$this->assertEquals( 4, count( $data ) );

	}

	public function test_when_post_type_is_supplied_in_navigator_should_filter_correctly() {
		// Create an entity and link all the posts to post_1.
		$entity = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		// Lets create 2 posts and 3 pages.
		$post_1 = $this->create_navigator_post( $entity );
		$post_2 = $this->create_navigator_post( $entity );
		$page_1 = $this->create_navigator_post( $entity, 'page' );
		$page_2 = $this->create_navigator_post( $entity, 'page' );
		$page_3 = $this->create_navigator_post( $entity, 'page' );
		// Get navigator data.
		$_GET['post_id'] = $entity;
		$_GET['uniqid']  = "random_id";
		$_GET['post_types'] = 'post,some-random-post-type';
		$data            = _wl_navigator_get_data();
		// we expect to get only 2 posts with post type post.
		$this->assertEquals( 2, count( $data ) );
	}


}