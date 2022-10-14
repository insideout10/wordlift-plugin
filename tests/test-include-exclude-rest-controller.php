<?php
/**
 * Tests: Include Exclude API.
 *
 * @since 3.38.4
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

use WordLift\Modules\Include_Exclude\API;

/**
 * Define the Include_Exclude_REST_Controller_Test class.
 *
 * @group mappings
 *
 * @since 3.38.4
 */
class Include_Exclude_REST_Controller_Test extends WP_UnitTestCase {

	/**
	 * The {@link API} instance to test.
	 *
	 * @since  3.38.4
	 * @access private
	 * @var API $rest_instance The {@link API} instance to test.
	 */
	private $rest_instance;

	private $include_exclude_data;

	/**
	 * Our expected route for rest api.
	 */
	protected $include_exclude_route = '/wordlift/v1/include-exclude/config';
	/**
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();

			// $features = get_option( '_wl_features', array() );
			// $features['include_exclude'] = true;
			// update_option( '_wl_features', $features );

		$this->rest_instance = new API();
		$this->rest_instance->register_hooks();
		global $wp_rest_server;

		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;
		do_action( 'rest_api_init' );

		$this->include_exclude_data = array(
			'type' => 'INCLUDE',
			'urls' => "https://wordlift.io/hello-world \n https://wordlift.io/ \n https://wordlift.io/3",
		);

		update_option(
			'wl_exclude_include_urls_settings',
			$this->include_exclude_data
		);
	}

	/**
	 * Testing if instance is not null, check to determine this class is
	 * included.
	 */
	public function test_instance_not_null() {
		$this->assertNotNull( $this->rest_instance );
	}

	/**
	 * Test if rest route exists for getting/updating wl_exclude_include_urls_settings option.
	 */
	public function test_rest_route_for_updating_include_exclude_settings_option() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( $this->include_exclude_route, $routes );
	}

	/**
	 * Test if rest route for getting wl_exclude_include_urls_settings option returns status code 200.
	 */
	public function test_rest_route_for_getting_include_exclude_settings_option_returns_200() {
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		$request = new WP_REST_Request(
			'GET',
			$this->include_exclude_route
		);

		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );
	}

	/**
	 * Test if rest route for getting wl_exclude_include_urls_settings option without manage_options permission returns status code 401.
	 */
	public function test_rest_route_for_getting_include_exclude_settings_option_without_permission_returns_401() {

		$request = new WP_REST_Request(
			'GET',
			$this->include_exclude_route
		);

		$response = $this->server->dispatch( $request );

		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test if rest route exists for getting wl_exclude_include_urls_settings option returns array.
	 */
	public function test_rest_route_for_getting_include_exclude_settings_option_returns_array_data() {
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		$request = new WP_REST_Request(
			'GET',
			$this->include_exclude_route
		);

		$response = $this->server->dispatch( $request );

		$this->assertEquals( $this->include_exclude_data, $response->get_data() );
	}

	/**
	 * Test if rest route for getting wl_exclude_include_urls_settings option without urls returns 404.
	 */
	public function test_rest_route_for_getting_include_exclude_settings_option_without_urls_should_returns_404() {
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		update_option(
			'wl_exclude_include_urls_settings',
			array(
				'type' => 'INCLUDE',
				'urls' => '',
			)
		);

		$request = new WP_REST_Request(
			'GET',
			$this->include_exclude_route
		);

		$response = $this->server->dispatch( $request );

		$this->assertEquals( 404, $response->get_status() );
	}

	// Updating wl_exclude_include_urls_settings option tests.

	/**
	 * Test if rest route for Updating wl_exclude_include_urls_settings option without manage_options permission and wrong content type returns status code 401.
	 */
	public function test_rest_route_for_updating_include_exclude_settings_option_without_permission_and_wrong_content_type_should_returns_401() {

		$request = new WP_REST_Request(
			'PUT',
			$this->include_exclude_route
		);

		$json_data = wp_json_encode(
			array(
				'type' => 'INCLUDE',
				'urls' => "https://wordlift.io/hello-world \n https://wordlift.io/ \n https://wordlift.io/3",
			)
		);
		$request->set_header( 'content-type', 'application/json' );
		$request->set_body( $json_data );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * Test if rest route for Updating wl_exclude_include_urls_settings option without args returns status code 404.
	 */
	public function test_rest_route_for_updating_include_exclude_settings_option_without_args_should_returns_404() {

		$request = new WP_REST_Request(
			'PUT',
			$this->include_exclude_route
		);

		$request->set_header( 'content-type', 'application/json' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 400, $response->get_status() );
	}

	/**
	 * Test if rest route for Updating wl_exclude_include_urls_settings option with args returns status code 204.
	 */
	public function test_rest_route_for_updating_include_exclude_settings_option_with_args_should_returns_204() {
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		$request = new WP_REST_Request(
			'PUT',
			$this->include_exclude_route
		);

		$json_data = wp_json_encode(
			array(
				'type' => 'EXCLUDE',
				'urls' => "https://wordlift.io/hello-world \n https://wordlift.io/ \n https://wordlift.io/3",
			)
		);

		$request->set_header( 'Content-Type', 'application/json' );
		$request->set_body( $json_data );
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 204, $response->get_status() );
	}
}
