<?php

use Wordlift\Faq\Faq_Rest_Controller;

/**
 * Tests: FAQ REST routes check that the user can edit the requested post.
 *
 * @since 3.54.18
 * @package wordlift
 * @subpackage wordlift/tests
 * @group backend
 */
class Faq_Rest_Permissions_Test extends Wordlift_Unit_Test_Case {

	/**
	 * @var WP_REST_Server
	 */
	private $server;

	public function setUp() {
		parent::setUp();

		global $wp_rest_server;
		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;

		// The FAQ routes aren't registered by the plugin, register them here.
		add_action( 'rest_api_init', array( Faq_Rest_Controller::class, 'register_route_callback' ) );
		do_action( 'rest_api_init' );
	}

	public function tearDown() {
		remove_action( 'rest_api_init', array( Faq_Rest_Controller::class, 'register_route_callback' ) );

		parent::tearDown();
	}

	private function create_faq_request( $method, $post_id ) {
		$request = new WP_REST_Request( $method, '/wordlift/v1/faq' );
		$request->set_header( 'content-type', 'application/json' );
		$request->set_body(
			wp_json_encode(
				array(
					'post_id'   => $post_id,
					'faq_items' => array(
						array(
							'question' => 'Question?',
							'answer'   => 'Answer.',
						),
					),
				)
			)
		);

		return $request;
	}

	public function test_author_cannot_add_faq_items_to_another_users_post() {
		$owner_id = $this->factory()->user->create( array( 'role' => 'author' ) );
		$post_id  = $this->factory()->post->create( array( 'post_author' => $owner_id ) );

		wp_set_current_user( $this->factory()->user->create( array( 'role' => 'author' ) ) );

		$response = $this->server->dispatch( $this->create_faq_request( 'POST', $post_id ) );

		$this->assertEquals( 403, $response->get_status() );
		$this->assertEmpty( get_post_meta( $post_id, Faq_Rest_Controller::FAQ_META_KEY ) );
	}

	public function test_author_cannot_read_faq_items_of_another_users_draft() {
		$owner_id = $this->factory()->user->create( array( 'role' => 'author' ) );
		$post_id  = $this->factory()->post->create(
			array(
				'post_author' => $owner_id,
				'post_status' => 'draft',
			)
		);

		wp_set_current_user( $this->factory()->user->create( array( 'role' => 'author' ) ) );

		$response = $this->server->dispatch( new WP_REST_Request( 'GET', "/wordlift/v1/faq/$post_id" ) );

		$this->assertEquals( 403, $response->get_status() );
	}

	public function test_author_can_add_faq_items_to_own_post() {
		$author_id = $this->factory()->user->create( array( 'role' => 'author' ) );
		$post_id   = $this->factory()->post->create( array( 'post_author' => $author_id ) );

		wp_set_current_user( $author_id );

		$response = $this->server->dispatch( $this->create_faq_request( 'POST', $post_id ) );

		$this->assertEquals( 200, $response->get_status() );
		$this->assertCount( 1, get_post_meta( $post_id, Faq_Rest_Controller::FAQ_META_KEY ) );
	}

	public function test_editor_can_add_faq_items_to_another_users_post() {
		$owner_id = $this->factory()->user->create( array( 'role' => 'author' ) );
		$post_id  = $this->factory()->post->create( array( 'post_author' => $owner_id ) );

		wp_set_current_user( $this->factory()->user->create( array( 'role' => 'editor' ) ) );

		$response = $this->server->dispatch( $this->create_faq_request( 'POST', $post_id ) );

		$this->assertEquals( 200, $response->get_status() );
	}

}
