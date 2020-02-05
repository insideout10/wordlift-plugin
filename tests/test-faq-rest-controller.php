<?php

use Wordlift\FAQ\FAQ_Rest_Controller;

/**
 * Tests: Tests the FAQ Rest Controller
 * @since 3.26.0
 * @package wordlift
 * @subpackage wordlift/tests
 *
 */

class FAQ_REST_Controller_Test extends Wordlift_Unit_Test_Case {
	private $faq_route = '/wordlift/v1/faq';
	/**
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();

		$this->rest_instance = new FAQ_Rest_Controller();
		$this->rest_instance->register_routes();
		global $wp_rest_server;

		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;

		do_action( 'rest_api_init' );
	}
	public function test_rest_instance_not_null() {
		$this->assertNotNull( $this->rest_instance );
	}
	public function test_given_question_and_answer_should_save_it_for_post() {
		$post_id = $this->factory()->post->create( array('post_title' => 'foo'));
		$data = array (
			'post_id'  => $post_id,
			'faq_items' => array(
				array(
					'question' => 'foo question',
					'answer' => 'foo answer'
				)
			)
		);
		// Create user with 'publish_posts' capability.
		$user_id   = $this->factory->user->create( array( 'role' => 'author' ) );
		wp_set_current_user( $user_id );
		// insert the data for this post, prepare POST request to FAQ.
		$request   = new WP_REST_Request( 'POST', $this->faq_route );
		$request->set_header( 'content-type', 'application/json' );
		$request->set_body( wp_json_encode( $data ) );
		$response  = $this->server->dispatch( $request );
		// Should return 200 response
		$this->assertEquals( 200, $response->get_status() );
		$faq_items = get_post_meta($post_id, FAQ_Rest_Controller::FAQ_META_KEY);
		$this->assertCount( 1, $faq_items );
		$response_data = $response->get_data();
		$this->assertEquals( 'success', $response_data['status'] );
	}
//	public function test_whether_rest_server_has_faq_route() {
//		$routes = $this->server->get_routes();
//		$this->assertArrayHasKey( FAQ_Rest_Controller::FAQ_ROUTE, $routes);
//	}


}