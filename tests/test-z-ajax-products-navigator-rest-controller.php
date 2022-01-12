<?php
/**
 * Tests: Products Navigator Shortcode Test.
 *
 * @since   3.27.0
 * @package Wordlift
 */

use Wordlift\Cache\Ttl_Cache;

/**
 * Define the ProductsNavigatorShortcodeTest class.
 *
 * @group ajax
 *
 * @since   3.27.0
 * @package Wordlift
 */
class ProductsNavigatorShortcodeTest extends WP_UnitTestCase {


	/**
	 * Our expected route for rest api.
	 */
	protected $route = '/wordlift/v1/products-navigator';
	/**
	 * Our expected cache namespace for Ttl_Cache.
	 */
	protected $cache_namespace = '/products-navigator';

	/**
	 * @var WP_REST_Server
	 */
	private $server;

	public function setUp() {
		parent::setUp();

		global $wp_rest_server;

		$wp_rest_server = new WP_REST_Server();

		$this->server = $wp_rest_server;
		do_action( 'rest_api_init' );
	}

	public function test_rest_route_for_products_navigator() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( $this->route, $routes );
	}

	public function test_missing_params() {

		$cache = new Ttl_Cache( $this->cache_namespace );
		$cache->flush();

		$request  = new WP_REST_Request( 'GET', $this->route );
		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$this->assertEquals( 400, $response->get_status(), 'We expect status 400' );
		$this->assertEquals( 'rest_missing_callback_param', $data['code'], 'We expect an `rest_missing_callback_param` code.' );

	}

	public function test_invalid_post_id() {

		$cache = new Ttl_Cache( $this->cache_namespace );
		$cache->flush();

		$request = new WP_REST_Request( 'GET', $this->route );
		$request->set_param( 'post_id', 10000 );
		$request->set_param( 'uniqid', 'unique' );
		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$this->assertEquals( 404, $response->get_status(), 'We expect status 404' );
		$this->assertEquals( 'rest_invalid_post_id', $data['code'], 'We expect an `rest_invalid_post_id` code.' );

	}

	// Need to add more tests here...

}
