<?php
require_once( 'functions.php' );

/**
 * Test the methods defined in the configuration module.
 */
class ConfigurationTest extends WP_UnitTestCase {

	/**
	 * Set up the test.
	 */
	function setUp() {
		parent::setUp();
	}


	function test_wl_configuration_key() {

		$value = uniqid();
		wl_configuration_set_key( $value );

		$this->assertEquals( $value, wl_configuration_get_key() );
	}


	function test_wl_configuration_enable_color_coding() {

		wl_configuration_set_enable_color_coding( false );
		$this->assertFalse( wl_configuration_get_enable_color_coding() );

		wl_configuration_set_enable_color_coding( true );
		$this->assertTrue( wl_configuration_get_enable_color_coding() );
	}


	function test_wl_configuration_site_language() {

		$value = uniqid();
		wl_configuration_set_site_language( $value );

		$this->assertEquals( $value, wl_configuration_get_site_language() );
	}


	function test_wl_configuration_get_api_url() {

		$value = uniqid();
		wl_configuration_set_api_url( $value );

		$this->assertEquals( $value, wl_configuration_get_api_url() );
	}


	function test_wl_configuration_get_redlink_key() {

		$value = uniqid();
		wl_configuration_set_redlink_key( $value );

		$this->assertEquals( $value, wl_configuration_get_redlink_key() );
	}


	function test_wl_configuration_redlink_user_id() {

		$value = uniqid();
		wl_configuration_set_redlink_user_id( $value );

		$this->assertEquals( $value, wl_configuration_get_redlink_user_id() );
	}


	function test_wl_configuration_redlink_dataset_name() {

		$value = uniqid();
		wl_configuration_set_redlink_dataset_name( $value );

		$this->assertEquals( $value, wl_configuration_get_redlink_dataset_name() );
	}


	function test_wl_configuration_redlink_dataset_uri() {

		$value = uniqid();
		wl_configuration_set_redlink_dataset_uri( $value );

		$this->assertEquals( $value, wl_configuration_get_redlink_dataset_uri() );
	}


	function test_wl_configuration_redlink_application_name() {

		$value = uniqid();
		wl_configuration_set_redlink_application_name( $value );

		$this->assertEquals( $value, wl_configuration_get_redlink_application_name() );
	}

	function test_wl_configuration_analyzer_url() {

		// Set the WordLift Key.
		$wordlift_key = uniqid();
		wl_configuration_set_key( $wordlift_key );

		$this->assertEquals( WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE . "analyses?key=$wordlift_key", wl_configuration_get_analyzer_url() );

		// Set the Redlink Key.
		$redlink_key              = uniqid();
		$redlink_application_name = uniqid();
		$redlink_api_url          = uniqid();
		wl_configuration_set_key( '' );
		wl_configuration_set_redlink_key( $redlink_key );
		wl_configuration_set_redlink_application_name( $redlink_application_name );
		wl_configuration_set_api_url( $redlink_api_url );

		$this->assertStringStartsWith( "$redlink_api_url/analysis/$redlink_application_name/enhance?key=$redlink_key", wl_configuration_get_analyzer_url() );
	}
}