<?php
/**
 * Tests: Ingredients API.
 *
 * @since 3.38.5
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

use WordLift\Modules\Food_Kg\Ingredients_API;

include dirname( __FILE__ ) . '/../src/modules/food-kg/includes/Ingredients_API.php';

/**
 * Define the Ingredients_REST_Controller_Test class.
 *
 * @group module
 *
 * @since 3.38.5
 */
class Ingredients_REST_Controller_Test extends WP_UnitTestCase {

	/**
	 * The {@link Ingredients_API} instance to test.
	 *
	 * @since  3.38.5
	 * @access private
	 * @var Ingredients_API $rest_instance The {@link Ingredients_API} instance to test.
	 */
	private $rest_instance;

	/**
	 * Our expected route for rest api.
	 */
	protected $ingredients_api_route = '/wordlift/v1/ingredients';
	/**
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();

		$this->rest_instance = new Ingredients_API();
		$this->rest_instance->register_hooks();
		global $wp_rest_server;

		$wp_rest_server = new WP_REST_Server();
		$this->server = $wp_rest_server;
		do_action( 'rest_api_init' );

	}

	/**
	 * Testing if instance is not null, check to determine this class is
	 * included.
	 */
	public function test_instance_not_null() {
		$this->assertNotNull( $this->rest_instance );
	}

	/**
	 * Test if rest route exists for ingredients API.
	 */
	public function test_rest_route_for_ingredients_api() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( $this->ingredients_api_route, $routes );
	}

	/**
	 * Test if rest route for getting ingredients without permission returns rest forbidden.
	 */
	public function test_rest_route_for_getting_ingredients_without_permission_returns_rest_forbidden() {

		$request = new WP_REST_Request(
			'GET',
			$this->ingredients_api_route
		);

		$response = $this->server->dispatch( $request );

		$data = array(
			'code'    => 'rest_forbidden',
			'message' => 'Sorry, you are not allowed to do that.',
			'data'    => array(
				'status' => 401,
			),
		);

		$this->assertEquals( $data, $response->get_data() );
	}

	/**
	 * Test if rest route for getting ingredients returns empty data ( As we don't have any data ).
	 */
	public function test_rest_route_for_getting_ingredients_returns_empty_data() {
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		$request = new WP_REST_Request(
			'GET',
			$this->ingredients_api_route
		);

		$response = $this->server->dispatch( $request );

		$data = array(
			'code'    => 'no_ingredients',
			'message' => 'No ingredients found.',
			'data'    => array(
				'status' => 404,
			),
		);

		$this->assertEquals( $data, $response->get_data() );
	}
}
