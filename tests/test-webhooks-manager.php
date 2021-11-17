<?php

/**
 * Unit test cases for the code added to incorporate feature request 1496
 * @since 3.34.0
 * @group webhooks
 * @author
 */

use Wordlift\Webhooks\Webhooks_Manager;

class Webhooks_Manager_Test extends Wordlift_Unit_Test_Case {

	public function setUp() {
		parent::setUp();
	}

	function tearDown() {
		parent::tearDown();
	}

	/**
	 * Use mock test to check register_sync_many and register_sync_delete methods that are invoked on
	 * addition or deletion of posts, terms etc.
	 */

	function test_register_sync_remote_call() {

		$payload         = array( "Test Content" );
		$mul_payload     = array( "Test Content 1", "Test Content 2" );
		$type            = 0;
		$object_id       = 3;
		$uri             = 'http://data-dev.wordlift.io/wl040/page/privacy_policy';
		$expected_object = array( "name" => "John", "age" => 30, "car" => null );

		// Create a stub for the Rest_Controller class.
		$mock_rest_controller = $this->getMockBuilder( 'Rest_Controller' )
		                             ->setMethods( array( 'register_sync_many', 'register_sync_delete' ) )
		                             ->disableOriginalConstructor()
		                             ->getMock();

		// Configure the mock_rest_controller.
		$mock_rest_controller->expects( $this->any() )
		                     ->method( 'register_sync_many' )
		                     ->willReturn( $expected_object );

		// Configure the mock_rest_controller.
		$mock_rest_controller->expects( $this->any() )
		                     ->method( 'register_sync_delete' )
		                     ->willReturn( $expected_object );

		// Calling $mock_rest_controller->register_sync_many and register_sync_delete will now return
		// value stored in expected_object.
		$this->assertEquals( $expected_object, $mock_rest_controller->register_sync_many( $payload ) );
		$this->assertEquals( $expected_object, $mock_rest_controller->register_sync_many( $mul_payload ) );
		$this->assertEquals(
			$expected_object,
			$mock_rest_controller->register_sync_delete( $type, $object_id, $uri )
		);
	}
}
