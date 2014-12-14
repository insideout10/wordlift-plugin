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


	function test_wl_configuration_entity_display_as() {

		$value = uniqid();
		wl_configuration_set_entity_display_as( $value );

		$this->assertEquals( $value, wl_configuration_get_entity_display_as() );
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

}