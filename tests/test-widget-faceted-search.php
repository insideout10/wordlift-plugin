<?php
/**
 * @since 3.27.7.3
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

use Wordlift\Widgets\Faceted_Search\Faceted_Search_Template_Endpoint;
use Wordlift\Widgets\Srcset_Util;

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
		run_wordlift();
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


	public function create_faceted_search_entity() {
		$post_id = $this->factory()->post->create( array( 'post_type' => 'entity' ) );

		if ( ! category_exists( 'faceted_search_category' ) ) {
			wp_create_category( 'faceted_search_category' );
		}
		/**
		 * @var $category WP_Term
		 */
		$this->set_faceted_search_category( $post_id );


		// set the entity type as article.
		$entity_type_service = Wordlift_Entity_Type_Service::get_instance();

		$entity_type_service->set( $post_id, 'http://schema.org/Thing' );

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

	public function test_faceted_search_should_return_results_correctly_for_entity() {
		$entity_1        = $this->create_faceted_search_entity();
		$entity_2        = $this->create_faceted_search_entity();
		$post_1          = $this->create_faceted_search_post( $entity_1 );
		$post_2          = $this->create_faceted_search_post( $entity_2 );
		$post_3          = $this->create_faceted_search_post( $entity_1 );
		$post_4          = $this->create_faceted_search_post( $entity_1 );
		$post_5          = $this->create_faceted_search_post( $entity_1 );
		$_GET['post_id'] = $entity_1;
		$_GET['uniqid']  = 'random_id';
		$result          = wl_shortcode_faceted_search_origin( $_GET );
		$this->assertCount( 4, $result['posts'] );
	}


	public function test_faceted_search_should_return_posts_correctly_for_post() {
		// Link multiple posts to this post.
		$entity_1        = $this->create_faceted_search_entity();
		$page_1          = $this->create_faceted_search_post( $entity_1, 'page' );
		$page_2          = $this->create_faceted_search_post( $entity_1, 'page' );
		$product_1       = $this->create_faceted_search_post( $entity_1, 'product' );
		$product_2       = $this->create_faceted_search_post( $entity_1, 'product' );
		$_GET['post_id'] = $page_1;
		$_GET['uniqid']  = 'random_id';
		$result          = wl_shortcode_faceted_search_origin( $_GET );
		$this->assertCount( 3, $result['posts'] );
		$this->assertCount( 1, $result['entities'] );
	}


	public function test_faceted_search_block_type_should_have_post_types_attribute() {
		$shortcode  = new Wordlift_Faceted_Search_Shortcode();
		$block_atts = $shortcode->get_block_attributes();
		$this->assertArrayHasKey( 'post_types', $block_atts );
		$this->assertTrue( is_array( $block_atts['post_types'] ) );
		$attribute_data = $block_atts['post_types'];
		$this->assertArrayHasKey( 'type', $attribute_data );
		$this->assertArrayHasKey( 'default', $attribute_data );
	}


	public function test_faceted_search_rest_url_should_have_post_types_attribute() {
		$post_id = $this->factory()->post->create();
		$html    = do_shortcode( "[wl_faceted_search post_types='post,page' post_id=$post_id]" );
		$this->assertTrue( strpos( $html, 'post_types=post,page' ) !== false );
	}

	public function test_faceted_search_rest_url_should_NOT_have_post_types_attribute_if_not_supplied() {
		$post_id = $this->factory()->post->create();
		$html    = do_shortcode( "[wl_faceted_search post_id=$post_id]" );
		$this->assertFalse( strpos( $html, 'post_types=post,page' ) !== false );
	}


	public function test_given_post_id_html_attributes_should_be_escaped_for_faceted_search_url() {
		$post_id             = $this->factory()->post->create();
		$html                = do_shortcode( "[wl_faceted_search limit=10 post_id=$post_id]" );
		$expected_url_output = "wordlift/v1/faceted-search&amp;post_id=$post_id&amp;limit=10";
		$this->assertTrue( strpos( $html, $expected_url_output ) !== false );
	}

	public function test_shortcode_should_have_src_set_attribute_in_amp_version() {
		$post_id     = $this->factory()->post->create();
		$_GET['amp'] = true;
		$result      = do_shortcode( "[wl_faceted_search post_id='$post_id']" );
		$this->assertTrue( strpos( $result, '{{srcset}}' ) !== false );
	}


	public function test_post_with_three_images_sizes_should_have_the_urls_in_srcset_for_referencing_posts_in_amp_version() {

		// Link multiple posts to this post.
		$entity_1 = $this->create_faceted_search_entity();
		$post_1   = $this->create_faceted_search_post( $entity_1 );
		$post_2   = $this->create_faceted_search_post( $entity_1 );
		$post_3   = $this->factory()->post->create();

		$attachment_id = $this->factory()->attachment->create_upload_object( __DIR__ . '/assets/cat-1200x1200.jpg', $post_2 );
		set_post_thumbnail( $post_2, $attachment_id );

		$medium_1 = get_the_post_thumbnail_url( $post_2, 'medium' );
		$large_1  = get_the_post_thumbnail_url( $post_2, 'large' );

		// Add thumbnails to other posts
		$attachment_id = $this->factory()->attachment->create_upload_object( __DIR__ . '/assets/cat-1200x1200.jpg', $post_3 );
		set_post_thumbnail( $post_3, $attachment_id );

		$medium_2 = get_the_post_thumbnail_url( $post_3, 'medium' );
		$large_2  = get_the_post_thumbnail_url( $post_3, 'large' );


		$_GET['post_id'] = $post_1;
		$_GET['uniqid']  = 'uniqid';
		$_GET['amp']     = true;
		$faceted_data    = wl_shortcode_faceted_search_origin( $_GET );
		$result          = $faceted_data['posts'][0]['values'];

		// Check if we have srcset in referenced posts.
		$target_post_1 = $result[0];
		$srcset_1      = $target_post_1->srcset;

		$this->assertTrue( strpos( $srcset_1, $medium_1 ) !== false );
		$this->assertTrue( strpos( $srcset_1, $large_1 ) !== false );

		// check if we have srcset in filler posts.
		$target_post_2 = $result[2];
		$srcset_2      = $target_post_2->srcset;
		$this->assertTrue( strpos( $srcset_2, $medium_2 ) !== false );
		$this->assertTrue( strpos( $srcset_2, $large_2 ) !== false );
	}


	public function test_srcset_should_have_intrinsic_width_specified() {
		$post_id = $this->factory()->post->create();

		$attachment_id = $this->factory()->attachment->create_upload_object( __DIR__ . '/assets/cat-1200x1200.jpg', $post_id );
		set_post_thumbnail( $post_id, $attachment_id );

		$medium_1 = get_the_post_thumbnail_url( $post_id, 'medium' );
		$large_1  = get_the_post_thumbnail_url( $post_id, 'large' );
		$srcset   = Srcset_Util::get_srcset( $post_id, Srcset_Util::FACETED_SEARCH_WIDGET );


		$medium_1_srcset = $medium_1 . ' ' . $this->get_image_width( $post_id, 'medium' ) . 'w';
		$this->assertTrue( strpos( $srcset, $medium_1_srcset ) !== false );

		$large_1_srcset = $large_1 . ' ' . $this->get_image_width( $post_id, 'large' ) . 'w';
		$this->assertTrue( strpos( $srcset, $large_1_srcset ) !== false );


	}


	private function get_image_width( $post_id, $size ) {
		$thumbnail_id = get_post_thumbnail_id( $post_id );
		if ( ! $thumbnail_id ) {
			return false;
		}
		$data = wp_get_attachment_image_src( $thumbnail_id, $size );
		if ( ! $data ) {
			return false;
		}

		return array_key_exists( 2, $data ) ? $data[2] : false;
	}


}