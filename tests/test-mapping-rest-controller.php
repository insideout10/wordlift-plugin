<?php
/**
 * Tests: Mappings Test.
 *
 * @since 3.25.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Wordlift_Mapping_REST_Controller_Test class.
 *
 * @since 3.25.0
 */
class Wordlift_Mapping_REST_Controller_Test extends WP_UnitTestCase {

	/**
	 * The {@link Wordlift_Mapping_REST_Controller} instance to test.
	 *
	 * @since  3.25.0
	 * @access private
	 * @var \Wordlift_Mapping_REST_Controller $rest_instance The {@link Wordlift_Mapping_REST_Controller} instance to test.
	 */
	private $rest_instance;

	/**
	 * Our expected route for rest api.
	 */
	protected $mapping_route = '/wordlift/v1/sync-mappings/mapping';
	/**
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();	

		$this->rest_instance = new Wordlift_Mapping_REST_Controller();
		$this->rest_instance->register_routes();

		global $wp_rest_server;
		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;
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
	 * Test is rest route exists for inserting/updating mapping item.
	 */
	public function test_rest_route_for_inserting_mapping_item() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( $this->mapping_route, $routes );
	}

	/**
	 * Test post mapping item to rest api endpoint returns 200 status code.
	 */
	public function test_post_to_insert_mapping_item_endpoint_returns_200() {
		$request  = new WP_REST_Request( 'POST', $this->mapping_route );
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status() );
	}
}
