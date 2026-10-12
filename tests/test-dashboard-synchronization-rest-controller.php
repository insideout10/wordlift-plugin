<?php
/**
 * Tests: Dashboard Synchronization REST Controller.
 *
 * @since 3.54.17
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

use Wordlift\Modules\Dashboard\Synchronization\Rest_Controller;
use Wordlift\Modules\Dashboard\Synchronization\Synchronization_Service;

require_once dirname( __DIR__ ) . '/src/modules/dashboard/vendor/autoload.php';

/**
 * Define the Dashboard_Synchronization_Rest_Controller_Test class.
 *
 * @since 3.54.17
 */
class Dashboard_Synchronization_Rest_Controller_Test extends WP_UnitTestCase {

	const ROUTE = '/wl-dashboard/v1/synchronizations';

	/**
	 * @var WP_REST_Server
	 */
	private $server;

	/**
	 * @var Synchronization_Service|\PHPUnit\Framework\MockObject\MockObject
	 */
	private $synchronization_service;

	/**
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();

		$this->synchronization_service = $this->createMock( Synchronization_Service::class );

		global $wp_rest_server;
		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;

		// Register the routes on the new server directly, the module only loads when food-kg or gardening-kg is on.
		$rest_controller = new Rest_Controller( $this->synchronization_service );
		add_action( 'rest_api_init', array( $rest_controller, 'rest_api_init' ) );
		do_action( 'rest_api_init' );
	}

	public function tearDown() {
		global $wp_rest_server;
		$wp_rest_server = null;

		parent::tearDown();
	}

	public function methods() {
		return array(
			array( 'GET' ),
			array( 'POST' ),
			array( 'DELETE' ),
		);
	}

	/**
	 * @dataProvider methods
	 */
	public function test_anonymous_is_refused( $method ) {
		wp_set_current_user( 0 );

		$this->synchronization_service->expects( $this->never() )->method( 'create' );
		$this->synchronization_service->expects( $this->never() )->method( 'delete_syncs' );

		$response = $this->server->dispatch( new WP_REST_Request( $method, self::ROUTE ) );

		$this->assertEquals( 401, $response->get_status() );
	}

	/**
	 * @dataProvider methods
	 */
	public function test_editor_is_refused( $method ) {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'editor' ) ) );

		$this->synchronization_service->expects( $this->never() )->method( 'create' );
		$this->synchronization_service->expects( $this->never() )->method( 'delete_syncs' );

		$response = $this->server->dispatch( new WP_REST_Request( $method, self::ROUTE ) );

		$this->assertEquals( 403, $response->get_status() );
	}

	public function test_admin_can_list() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );

		$response = $this->server->dispatch( new WP_REST_Request( 'GET', self::ROUTE ) );

		$this->assertEquals( 200, $response->get_status() );
		$this->assertEquals( array( 'items' => array() ), $response->get_data() );
	}

	public function test_admin_can_create() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );

		$this->synchronization_service->expects( $this->once() )->method( 'create' );

		$response = $this->server->dispatch( new WP_REST_Request( 'POST', self::ROUTE ) );

		$this->assertEquals( 200, $response->get_status() );
	}

	public function test_admin_can_delete() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );

		$this->synchronization_service->expects( $this->once() )->method( 'delete_syncs' );

		$response = $this->server->dispatch( new WP_REST_Request( 'DELETE', self::ROUTE ) );

		$this->assertEquals( 200, $response->get_status() );
	}

}
