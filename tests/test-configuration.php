<?php
/**
 * Tests: Configuration Tests.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */
class Wordlift_Configuration_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.13.0
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

	/**
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();

		$this->configuration_service = $this->get_wordlift_test()->get_configuration_service();

	}

	function test_wl_configuration_key() {

		$value = uniqid();
		wl_configuration_set_key( $value );

		$this->assertEquals( $value, wl_configuration_get_key() );
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

	/**
	 * Test the `link by default` setting.
	 *
	 * @since 3.13.0
	 */
	public function test_is_link_by_default() {

		// Check that the default setting is `true`.
		$this->assertTrue( $this->configuration_service->is_link_by_default() );

		$this->configuration_service->set_link_by_default( true );

		$this->assertTrue( $this->configuration_service->is_link_by_default() );

		$this->configuration_service->set_link_by_default( false );

		$this->assertFalse( $this->configuration_service->is_link_by_default() );

		$this->configuration_service->set_link_by_default( 1 );

		$this->assertFalse( $this->configuration_service->is_link_by_default() );

	}

}
