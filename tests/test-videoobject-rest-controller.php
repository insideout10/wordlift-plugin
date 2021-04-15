<?php
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
}