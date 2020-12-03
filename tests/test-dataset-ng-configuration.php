<?php

/**
 * Class Test_Dataset_Ng
 *
 * @group dataset-ng
 */
class Test_Dataset_Ng_Configuration extends Wordlift_Unit_Test_Case {

	/**
	 * Test that the features configuration is set accordingly when dataset-ng is enabled.
	 */
	function test_configuration_with_dataset_ng_enabled() {

		if ( ! wp_validate_boolean( getenv( 'WL_FEATURES__DATASET_NG' ) ) ) {
			$this->markTestSkipped( '`WL_FEATURES__DATASET_NG` not enabled.' );
		}

		$features = get_option( Wordlift\Features\Response_Adapter::WL_FEATURES );
		$this->assertTrue( $features['analysis-ng'] );
		$this->assertTrue( $features['dataset-ng'] );

		$this->assertTrue( apply_filters( 'wl_feature__enable__dataset-ng', false ) );
		$this->assertTrue( apply_filters( 'wl_feature__enable__analysis-ng', false ) );

	}

	/**
	 * Test that the features configuration is set accordingly when dataset-ng is disabled.
	 */
	function test_configuration_with_dataset_ng_disabled() {

		if ( wp_validate_boolean( getenv( 'WL_FEATURES__DATASET_NG' ) ) ) {
			$this->markTestSkipped( '`WL_FEATURES__DATASET_NG` enabled.' );
		}

		$features = get_option( Wordlift\Features\Response_Adapter::WL_FEATURES );
		$this->assertFalse( $features );

		$this->assertFalse( apply_filters( 'wl_feature__enable__dataset-ng', false ) );
		$this->assertFalse( apply_filters( 'wl_feature__enable__analysis-ng', false ) );

	}

}
