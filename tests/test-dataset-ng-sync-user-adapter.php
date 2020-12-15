<?php

use Wordlift\Dataset\Sync_User_Adapter;

/**
 * Class Test_Dataset_Ng_Sync_User_Adapter
 *
 * @group dataset-ng
 */
class Test_Dataset_Ng_Sync_User_Adapter extends Wordlift_Unit_Test_Case {

	function setUp() {
		parent::setUp();

		// These tests make sense only if `WL_FEATURES__DATASET_NG` is enabled.
		if ( ! wp_validate_boolean( getenv( 'WL_FEATURES__DATASET_NG' ) ) ) {
			$this->markTestSkipped( '`WL_FEATURES__DATASET_NG` not enabled.' );
		}
	}

	function test_update_meta() {
		$user_id = $this->factory()->user->create();

		$adapter = new Sync_User_Adapter( $user_id );
		$adapter->update_meta( '_tmp_test_key', 'value123' );
		$this->assertEquals( 'value123', get_user_meta( $user_id, '_tmp_test_key', true ) );
	}

	function test_get_meta() {
		$user_id = $this->factory()->user->create();

		$adapter = new Sync_User_Adapter( $user_id );
		update_user_meta( $user_id, '_tmp_test_key_2', 'value456' );
		$this->assertEquals( 'value456', $adapter->get_meta( '_tmp_test_key_2', true ) );
	}

	function test_is_published() {
		$user_id = $this->factory()->user->create();

		$adapter = new Sync_User_Adapter( $user_id );
		$this->assertTrue( $adapter->is_published() );
	}

	function test_is_public() {
		$user_id = $this->factory()->user->create();

		$adapter = new Sync_User_Adapter( $user_id );
		$this->assertTrue( $adapter->is_public() );
	}

}
