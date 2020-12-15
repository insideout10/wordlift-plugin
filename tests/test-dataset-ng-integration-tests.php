<?php

use Wordlift\Dataset\Sync_Object_Adapter_Factory;
use Wordlift\Dataset\Sync_Post_Adapter;
use Wordlift\Dataset\Sync_Post_Hooks;
use Wordlift\Dataset\Sync_User_Adapter;
use Wordlift\Object_Type_Enum;

/**
 * Test Hooks.
 *
 * @group dataset-ng
 * @group integration-tests
 */
class Test_Dataset_Ng_Integration_Tests extends Wordlift_Unit_Test_Case {

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

	}

	function test() {
		// Dummy test just so that there should be no warning / error
		$this->assertEquals( 1, 1 );
	}

}
