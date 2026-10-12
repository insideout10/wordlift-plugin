<?php
/**
 * Tests: Ajax Batch Operation Ajax Adapter Test.
 *
 * @since   3.54.17
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Ajax_Batch_Operation_Ajax_Adapter_Test} class.
 *
 * @group   ajax
 *
 * @since   3.54.17
 * @package Wordlift
 */
class Wordlift_Ajax_Batch_Operation_Ajax_Adapter_Test extends Wordlift_Ajax_Unit_Test_Case {

	const ACTION = 'wl_test_batch_operation';

	/**
	 * @var Wordlift_Batch_Operation_Interface|\PHPUnit\Framework\MockObject\MockObject
	 */
	private $operation;

	/**
	 * The adapter under test, it hooks the AJAX actions when created.
	 *
	 * @var Wordlift_Batch_Operation_Ajax_Adapter
	 */
	private $adapter;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		// `Wordlift_Admin_Setup::admin_init` redirects admins and exits while this flag is set.
		delete_transient( '_wl_activation_redirect' );

		$this->operation = $this->createMock( Wordlift_Batch_Operation_Interface::class );
		$this->operation->method( 'process' )->willReturn( array( 'next' => 10 ) );
		$this->operation->method( 'count' )->willReturn( 42 );

		$this->adapter = new Wordlift_Batch_Operation_Ajax_Adapter( $this->operation, self::ACTION );
	}

	public function test_create_nonce_uses_the_action() {
		$this->assertTrue( (bool) wp_verify_nonce( $this->adapter->create_nonce(), self::ACTION ) );
	}

	public function actions() {
		return array(
			array( self::ACTION, 'process' ),
			array( self::ACTION . '_count', 'count' ),
		);
	}

	/**
	 * @dataProvider actions
	 */
	public function test_editor_with_nonce_is_refused( $action, $method ) {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'editor' ) ) );

		$this->operation->expects( $this->never() )->method( $method );

		$_POST['_nonce'] = wp_create_nonce( self::ACTION );

		$response = $this->call( $action );

		$this->assertFalse( $response->success );
		$this->assertEquals( 'You are not allowed to run this operation.', $response->data );
	}

	/**
	 * @dataProvider actions
	 */
	public function test_admin_without_nonce_is_refused( $action, $method ) {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );

		$this->operation->expects( $this->never() )->method( $method );

		$response = $this->call( $action );

		$this->assertFalse( $response->success );
		$this->assertEquals( 'Invalid nonce.', $response->data );
	}

	public function test_admin_with_nonce_can_process() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );

		$this->operation->expects( $this->once() )->method( 'process' )->with( 0, 10 );

		$_POST['_nonce'] = wp_create_nonce( self::ACTION );

		$response = $this->call( self::ACTION );

		$this->assertTrue( $response->success );
		$this->assertEquals( 10, $response->data->next );
	}

	public function test_admin_with_nonce_can_count() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );

		$this->operation->expects( $this->once() )->method( 'count' );

		$_POST['_nonce'] = wp_create_nonce( self::ACTION );

		$response = $this->call( self::ACTION . '_count' );

		$this->assertTrue( $response->success );
		$this->assertEquals( 42, $response->data->count );
	}

	/**
	 * Call the AJAX action and return the decoded response.
	 *
	 * @param string $action The AJAX action.
	 *
	 * @return object The decoded JSON response.
	 */
	private function call( $action ) {
		try {
			$this->_handleAjax( $action );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );

		$this->assertInternalType( 'object', $response, "Unexpected response: $this->_last_response" );
		$this->assertObjectHasAttribute( 'success', $response );

		return $response;
	}

}
