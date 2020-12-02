<?php

/**
 * Class Test_Dataset_Ng
 *
 * @group dataset-ng
 */
class Test_Dataset_Ng extends Wordlift_Unit_Test_Case {

	/**
	 * Test that the features configuration is set accordingly when dataset-ng is enabled.
	 */
	function test_configuration_with_dataset_ng_enabled() {

		if ( 'yes' !== getenv( 'WL_DATASET_NG' ) ) {
			$this->markTestSkipped( '`WL_DATASET_NG` not enabled.' );
		}

		$features = get_option( Wordlift\Features\Response_Adapter::WL_FEATURES );
		$this->assertTrue( $features['analysis-ng'] );
		$this->assertTrue( $features['dataset-ng'] );

	}

	/**
	 * Test that the features configuration is set accordingly when dataset-ng is disabled.
	 */
	function test_configuration_with_dataset_ng_disabled() {

		if ( 'yes' === getenv( 'WL_DATASET_NG' ) ) {
			$this->markTestSkipped( '`WL_DATASET_NG` not enabled.' );
		}

		$features = get_option( Wordlift\Features\Response_Adapter::WL_FEATURES );
		$this->assertFalse( $features );

	}

}