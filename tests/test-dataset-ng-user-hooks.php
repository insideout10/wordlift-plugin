<?php

use Wordlift\Dataset\Sync_User_Hooks;
use Wordlift\Object_Type_Enum;

/**
 * Test Hooks.
 *
 * @group dataset-ng
 */
class Test_Dataset_Ng_User_Hooks extends Wordlift_Unit_Test_Case {

	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	private $sync_service_mock;

	function setUp() {
		parent::setUp();

		// These tests make sense only if `WL_FEATURES__DATASET_NG` is enabled.
		if ( ! wp_validate_boolean( getenv( 'WL_FEATURES__DATASET_NG' ) ) ) {
			$this->markTestSkipped( '`WL_FEATURES__DATASET_NG` not enabled.' );
		}

		// Remove the global filters, since we're going to call `shutdown`, we don't want that to have side effects.
		global $wp_filter;
		$wp_filter = array();

		$this->sync_service_mock = $this->getMockBuilder( 'Wordlift\Dataset\Sync_Service' )
		                                ->disableOriginalConstructor()
		                                ->getMock();

		new Sync_User_Hooks( $this->sync_service_mock );

	}

	function test_create_user() {

		$this->sync_service_mock->method( 'sync_one' )
		                        ->willReturn( true );

		// 5 times = 1 user & 4 meta created.
		$this->sync_service_mock->expects( $this->exactly( 5 ) )
		                        ->method( 'sync_one' )
		                        ->with(
			                        $this->equalTo( Object_Type_Enum::USER ),
			                        $this->isType( 'int' )
		                        );

		$this->factory()->user->create();

	}

	function test_added_user_meta() {

		$this->sync_service_mock->method( 'sync_one' )
		                        ->willReturn( true );

		// 6 times = 1 user & 4 meta created + 1 user meta created + 1 user meta deleted.
		$this->sync_service_mock->expects( $this->exactly( 6 ) )
		                        ->method( 'sync_one' )
		                        ->with(
			                        $this->equalTo( Object_Type_Enum::USER ),
			                        $this->isType( 'int' )
		                        );

		$user_id = $this->factory()->user->create();
		add_user_meta( $user_id, '_tmp_test_added_user_meta', 'tmp' );

	}

	function test_updated_user_meta() {

		$this->sync_service_mock->method( 'sync_one' )
		                        ->willReturn( true );

		// 7 times = 1 user & 4 meta created + 1 user meta created + 1 user meta updated.
		$this->sync_service_mock->expects( $this->exactly( 7 ) )
		                        ->method( 'sync_one' )
		                        ->with(
			                        $this->equalTo( Object_Type_Enum::USER ),
			                        $this->isType( 'int' )
		                        );

		$user_id = $this->factory()->user->create();
		add_user_meta( $user_id, '_tmp_test_added_user_meta', 'tmp_1' );
		update_user_meta( $user_id, '_tmp_test_added_user_meta', 'tmp_2' );

	}

	function test_deleted_user_meta() {

		$this->sync_service_mock->method( 'sync_one' )
		                        ->willReturn( true );

		// 7 times = 1 user & 4 meta created + 1 user meta created + 1 user meta deleted.
		$this->sync_service_mock->expects( $this->exactly( 7 ) )
		                        ->method( 'sync_one' )
		                        ->with(
			                        $this->equalTo( Object_Type_Enum::USER ),
			                        $this->isType( 'int' )
		                        );

		$user_id = $this->factory()->user->create();
		add_user_meta( $user_id, '_tmp_test_added_user_meta', 'tmp_1' );
		delete_user_meta( $user_id, '_tmp_test_added_user_meta' );

	}

	function test_ignored_meta() {

		$this->sync_service_mock->method( 'sync_one' )
		                        ->willReturn( true );

		// 5 times = 1 user & 4 meta created.
		$this->sync_service_mock->expects( $this->exactly( 5 ) )
		                        ->method( 'sync_one' )
		                        ->with(
			                        $this->equalTo( Object_Type_Enum::USER ),
			                        $this->isType( 'int' )
		                        );

		add_filter( 'wl_dataset__sync_user_hooks__ignored_meta_keys', function ( $args ) {
			$args[] = '_my_custom_field';

			return $args;
		} );

		$user_id = $this->factory()->user->create();
		add_user_meta( $user_id, 'entity_url', 'tmp' );
		add_user_meta( $user_id, '_my_custom_field', 'tmp' );

	}

	function test_create_and_delete_user() {

		$this->sync_service_mock->method( 'sync_one' )
		                        ->willReturn( true );

		$this->sync_service_mock->method( 'delete_one' )
		                        ->willReturn( true );

		// 9 times = 1 user created & 4 meta created + 4 times meta deleted.
		$this->sync_service_mock->expects( $this->exactly( 9 ) )
		                        ->method( 'sync_one' )
		                        ->with(
			                        $this->equalTo( Object_Type_Enum::USER ),
			                        $this->isType( 'int' )
		                        );

		$this->sync_service_mock->expects( $this->once() )
		                        ->method( 'delete_one' )
		                        ->with(
			                        $this->equalTo( Object_Type_Enum::USER ),
			                        $this->isType( 'int' )
		                        );

		$user_id = $this->factory()->user->create();

		wp_delete_user( $user_id );

	}

}
