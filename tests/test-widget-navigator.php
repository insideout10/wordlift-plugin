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

		// Check whether the `article` term exists.
		$article = get_term_by( 'slug', 'article', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		// The `article` term doesn't exists, so create it.
		if ( ! $article ) {
			$data    = wp_insert_term(
				'Article',
				Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
				array(
					'slug'        => 'article',
					'description' => 'An Article.',
				)
			);
			$term_id = $data['term_id'];
			update_term_meta( $term_id, '_wl_uri', 'http://schema.org/Article' );
		}

		do_action( 'rest_api_init' );
		// navigator query triggers a warning due to placeholder.
		add_filter( 'doing_it_wrong_trigger_error', '__return_false' );
	}

	public function test_when_the_post_type_supplied_should_restrict_by_post_type() {
		// Create an entity and link all the posts to post_1.
		$entity = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		// Lets create 2 posts and 2 pages.
		$post_1 = $this->create_navigator_post( $entity );
		$post_2 = $this->create_navigator_post( $entity );
		$post_3 = $this->create_navigator_post( $entity );
		// But we will restrict by post type.
		$_GET['post_id']    = $post_1;
		$_GET['uniqid']     = 'random_id';
		$_GET['post_types'] = 'post,some-random-post-type';
		$_REQUEST['_wpnonce']   = wp_create_nonce( 'wl_navigator' );
		$posts              = _wl_navigator_get_data();
		$expected_post_ids  = array( $post_2, $post_3 );

		$returned_post_ids = array(
			$posts[0]['post']['id'],
			$posts[1]['post']['id'],
		);

		sort( $expected_post_ids );
		sort( $returned_post_ids );
		// the first 2 returned posts should have post type post
		$this->assertEquals( $expected_post_ids, $returned_post_ids );

		// we expect 2 posts.
		$this->assertEquals( 2, count( $posts ) );

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

		add_filter(
			'wordlift_navigator_templates',
			function ( $templates ) use ( $template, $template_id ) {
				$templates[ $template_id ] = $template;

				return $templates;
			}
		);

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
		$template_url = '?rest_route=/wordlift/v1/navigator/template';
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
		$post_id = $this->factory()->post->create( array( 'post_type' => $post_type ) );

		wl_core_add_relation_instance( $post_id, WL_WHO_RELATION, $linked_entity );
		if ( ! category_exists( 'navigator_test_category' ) ) {
			wp_create_category( 'navigator_test_category' );
		}
		/**
		 * @var $category WP_Term
		 */
		$this->set_navigator_test_category( $post_id );

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
		$_GET['uniqid']  = 'random_id';
		$data            = _wl_navigator_get_data();
		$this->assertEquals( 4, count( $data ) );

	}


	public function test_when_post_has_html_entity_on_title_should_be_decoded() {
		// Create an entity and link all the posts to post_1.
		$entity = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		$post_1 = $this->create_navigator_post( $entity );
		// Get navigator data.
		$_GET['post_id'] = $post_1;
		$_GET['uniqid']  = 'random_id';
		$post_2          = $this->create_navigator_post( $entity );
		$title           = 'You Can&#8217;t Contribute Beyond Yourself Until You&#8217;re Willing to Let Go';
		wp_update_post(
			array(
				'ID'         => $post_2,
				'post_title' => $title,
			)
		);
		$posts     = _wl_navigator_get_data();
		$post      = array_pop( $posts );
		$post_data = $post['post'];
		$this->assertEquals( $post_data['title'], html_entity_decode( $title ) );
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
		$_GET['uniqid']  = 'random_id';
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
		$_GET['post_id']    = $entity;
		$_GET['uniqid']     = 'random_id';
		$_GET['post_types'] = 'post,some-random-post-type';
		$data               = _wl_navigator_get_data();
		// we expect to get only 4 posts along with 2 filler posts.
		$this->assertEquals( 4, count( $data ) );
	}


	public function test_when_post_id_given_filler_posts_should_return_posts_from_same_category() {
		$entity = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		$post_1 = $this->create_navigator_post( $entity );

		/**
		 * Create posts on the same category
		 */
		$post_2 = $this->create_filler_post_in_same_category();
		$post_3 = $this->create_filler_post_in_same_category();
		$post_4 = $this->create_filler_post_in_same_category();
		$post_5 = $this->create_filler_post_in_same_category();
		/**
		 * we expect the posts to be fetched by the function.
		 */
		$_GET['post_id'] = $post_1;
		$_GET['uniqid']  = 'random_id';
		$data            = _wl_navigator_get_data();
		$this->assertCount( 4, $data, '4 posts which are not linked to entity but present in same category as target post should be returned' );
	}


	public function test_when_the_posts_are_not_available_in_same_category_should_fetch_from_any_category() {
		$entity = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		$post_1 = $this->create_navigator_post( $entity );

		$post_2 = $this->create_post_with_thumbnail();
		$post_3 = $this->create_post_with_thumbnail();
		$post_4 = $this->create_post_with_thumbnail();
		$post_5 = $this->create_post_with_thumbnail();
		/**
		 * we expect the posts to be fetched by the function.
		 */
		$_GET['post_id'] = $post_1;
		$_GET['uniqid']  = 'random_id';
		$data            = _wl_navigator_get_data();
		$this->assertCount( 4, $data, '4 posts which are not linked to entity, also not present in same category as target post should be returned' );
	}


	public function test_when_post_id_given_filler_posts_should_return_posts_from_same_category_and_also_filter_based_on_same_post_type() {
		$entity = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		$post_1 = $this->create_navigator_post( $entity, 'page' );

		/**
		 * Create posts on the same category
		 */
		$post_2 = $this->create_filler_post_in_same_category( 'page' );
		$post_3 = $this->create_filler_post_in_same_category( 'page' );
		$post_4 = $this->create_filler_post_in_same_category( 'page' );
		$post_5 = $this->create_filler_post_in_same_category( 'page' );
		/**
		 * we expect the posts to be fetched by the function.
		 */
		$_GET['post_id']    = $post_1;
		$_GET['uniqid']     = 'random_id';
		$_GET['post_types'] = 'post,some-random-post-type';
		$data               = _wl_navigator_get_data();
		$this->assertCount( 4, $data, '4 posts should be returned, because there wont be enough posts when we filter by post type post' );
	}

	public function test_when_the_posts_are_not_available_in_same_category_should_fetch_from_any_category_and_should_not_filter_by_post_type() {
		$entity = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		$post_1 = $this->create_navigator_post( $entity );

		$post_2 = $this->create_post_with_thumbnail();
		$post_3 = $this->create_post_with_thumbnail();
		$post_4 = $this->create_post_with_thumbnail( 'page' );
		$post_5 = $this->create_post_with_thumbnail( 'page' );
		$post_5 = $this->create_post_with_thumbnail( 'page' );
		/**
		 * we expect the posts to be fetched by the function.
		 */
		$_GET['post_id']    = $post_1;
		$_GET['uniqid']     = 'random_id';
		$_GET['post_types'] = 'page,some-random-post-type';
		$data               = _wl_navigator_get_data();
		$this->assertCount( 2, $data, '2 posts should be returned, filler posts should not pick the post type page' );
	}

	private function create_filler_post_in_same_category( $post_type = 'post' ) {
		$post_id = $this->create_post_with_thumbnail();
		$this->set_navigator_test_category( $post_id );
		set_post_type( $post_id, $post_type );

		return $post_id;
	}

	/**
	 * @param $post_id
	 */
	private function set_navigator_test_category( $post_id ) {
		$category = get_category_by_slug( 'navigator_test_category' );

		wp_set_post_categories( $post_id, array( $category->term_id ) );
	}

	/**
	 * @return mixed
	 */
	private function create_post_with_thumbnail( $post_type = 'post' ) {
		$post_id = $this->factory()->post->create( array( 'post_type' => $post_type ) );
		update_post_meta( $post_id, '_thumbnail_id', 'https://some-url-from-test.com' );

		return $post_id;
	}

	public function test_navigator_rest_url_should_have_post_types_attribute() {
		$post_id = $this->factory()->post->create();
		$html    = do_shortcode( "[wl_navigator post_types='post,page' post_id=$post_id]" );
		$this->assertTrue( strpos( $html, 'post_types=post,page' ) !== false );
	}

	public function test_navigator_rest_url_should_NOT_have_post_types_attribute_if_not_supplied() {
		$post_id = $this->factory()->post->create();
		$html    = do_shortcode( "[wl_navigator post_id=$post_id]" );
		$this->assertFalse( strpos( $html, 'post_types=post,page' ) !== false );
	}

	public function test_shortcode_should_have_src_set_attribute_in_amp_version() {
		$post_id     = $this->factory()->post->create();
		$_GET['amp'] = true;
		$result      = do_shortcode( "[wl_navigator post_id='$post_id']" );
		$this->assertTrue( strpos( $result, '{{post.srcset}}' ) !== false );
	}


	public function test_post_with_three_images_sizes_should_have_the_urls_in_filler_posts_srcset() {
		$post_id       = $this->factory()->post->create();
		$post_2        = $this->create_post_with_thumbnail();
		$attachment_id = $this->factory()->attachment->create_upload_object( __DIR__ . '/assets/cat-1200x1200.jpg', $post_2 );
		set_post_thumbnail( $post_2, $attachment_id );
		$medium          = get_the_post_thumbnail_url( $post_2, 'medium' );
		$large           = get_the_post_thumbnail_url( $post_2, 'large' );
		$_GET['amp']     = true;
		$_GET['post_id'] = $post_id;
		$_GET['uniqid']  = 'uniqid';
		$data            = _wl_navigator_get_data();
		$result          = $data[0]['post'];
		$this->assertArrayHasKey( 'srcset', $result );
		$srcset = $result['srcset'];
		$this->assertTrue( strpos( $srcset, $medium ) !== false );
		$this->assertTrue( strpos( $srcset, $large ) !== false );
	}


	public function test_post_with_three_images_sizes_should_have_the_urls_in_referencing_posts_srcset() {
		$entity        = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		$post_1        = $this->create_navigator_post( $entity );
		$post_2        = $this->create_navigator_post( $entity );
		$attachment_id = $this->factory()->attachment->create_upload_object( __DIR__ . '/assets/cat-1200x1200.jpg', $post_2 );
		set_post_thumbnail( $post_2, $attachment_id );
		$medium          = get_the_post_thumbnail_url( $post_2, 'medium' );
		$large           = get_the_post_thumbnail_url( $post_2, 'large' );
		$_GET['amp']     = true;
		$_GET['post_id'] = $post_1;
		$_GET['uniqid']  = 'uniqid';
		$data            = _wl_navigator_get_data();
		$result          = $data[0]['post'];
		$this->assertArrayHasKey( 'srcset', $result );
		$srcset = $result['srcset'];
		$this->assertTrue( strpos( $srcset, $medium ) !== false );
		$this->assertTrue( strpos( $srcset, $large ) !== false );
	}


}
