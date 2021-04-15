<?php

use Wordlift\Videoobject\Data\Video_Storage\Video_Storage_Factory;

/**
 * Class Videoobject_Block_Parser_Test
 * @group videoobject
 */
class Videoobject_Rest_Controller_Test extends \Wordlift_Videoobject_Unit_Test_Case {

	/**
	 * @var WP_REST_Server
	 */
	private $server;

	private $get_all_videos_route = '/wordlift/v1/videos';
	private $save_all_videos_route = '/wordlift/v1/videos/save';

	public function setUp() {
		parent::setUp();
		global $wp_rest_server, $wpdb;

		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;
		do_action( 'rest_api_init' );
	}


	/**
	 * Test is rest route exists for inserting/updating mapping item.
	 */
	public function test_rest_route_for_getting_all_videos() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( $this->get_all_videos_route, $routes );
	}

	public function test_when_given_post_id_should_return_all_videos() {
		$post_id = $this->factory()->post->create();
		$video   = new Wordlift\Videoobject\Data\Video\Video();
		Video_Storage_Factory::get_storage()->add_video( $post_id, $video );
		// send the post request.
		$user_id = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		$user    = wp_set_current_user( $user_id );

		$request   = new WP_REST_Request( 'POST', $this->get_all_videos_route );
		$json_data = wp_json_encode(
			array(
				'post_id' => $post_id
			)
		);
		$request->set_header( 'content-type', 'application/json' );
		$request->set_body( $json_data );
		$response = $this->server->dispatch( $request );
		$this->assertSame( 200, $response->get_status() );
		$this->assertCount( 1, $response->get_data() );
	}

	public function test_should_save_videos_posted_to_endpoint() {
		$post_id = $this->factory()->post->create();
		$video   = new Wordlift\Videoobject\Data\Video\Video();
		$user_id = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		$user    = wp_set_current_user( $user_id );

		$request   = new WP_REST_Request( 'POST', $this->save_all_videos_route );
		$json_data = wp_json_encode(
			array(
				'post_id' => $post_id,
				'videos' => array( $video )
			)
		);
		$request->set_header( 'content-type', 'application/json' );
		$request->set_body( $json_data );
		$response = $this->server->dispatch( $request );
		$this->assertSame( 200, $response->get_status() );
	}
}