<?php
/**
 * Tests: Configuration Tests.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Wordlift_Configuration_Test class.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 * @group api
 */
class Wordlift_Configuration_Test extends Wordlift_Unit_Test_Case {

	private $http_request_count = 0;

	function test_wl_configuration_key() {

		$value = uniqid();
		$this->configuration_service->set_key( $value );

		$this->assertEquals( $value, $this->configuration_service->get_key() );
	}

	function test_wl_configuration_site_language() {

		$value = uniqid();
		$this->configuration_service->set_language_code( $value );

		$this->assertEquals( $value, $this->configuration_service->get_language_code() );
	}

	function test_wl_configuration_analyzer_url() {

		// Set the WordLift Key.
		$wordlift_key = uniqid();
		$this->configuration_service->set_key( $wordlift_key );

		$this->assertEquals( WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE . "analyses?key=$wordlift_key", wl_configuration_get_analyzer_url() );

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

	/**
	 */
	function test_maybe_update_dataset_uri_new_key_empty() {

		add_filter( 'pre_http_request', array(
			$this,
			'pre_http_request',
		), 10, 3 );

		$this->configuration_service->maybe_update_dataset_uri(
			array( 'key' => uniqid() ),
			array( 'key' => '' )
		);

		$this->assertEquals( 0, $this->http_request_count );

	}

	/**
	 */
	function test_maybe_update_dataset_uri_new_key_not_equal_to_old_key() {

		add_filter( 'pre_http_request', array(
			$this,
			'pre_http_request',
		), 10, 3 );

		$old_key = uniqid( true );
		$new_key = uniqid( true );

		$this->configuration_service->maybe_update_dataset_uri(
			array( 'key' => $old_key ),
			array( 'key' => $new_key )
		);

		$this->assertEquals( 0, $this->http_request_count );

	}

	/**
	 */
	function test_maybe_update_dataset_uri_dataset_uri_not_empty() {

		add_filter( 'pre_http_request', array(
			$this,
			'pre_http_request',
		), 10, 3 );

		$old_key = $new_key = uniqid( true );

		$this->configuration_service->maybe_update_dataset_uri(
			array( 'key' => $old_key ),
			array( 'key' => $new_key )
		);

		$this->assertNotEmpty( $this->configuration_service->get_dataset_uri() );

		$this->assertEquals( 0, $this->http_request_count );

	}

	/**
	 */
	function test_maybe_update_dataset_uri() {

		add_filter( 'pre_http_request', array(
			$this,
			'pre_http_request',
		), 10, 3 );

		$old_dataset_uri = $this->configuration_service->get_dataset_uri();
		$this->configuration_service->set_dataset_uri( '' );

		$old_key = $new_key = uniqid( true );

		$this->configuration_service->maybe_update_dataset_uri(
			array( 'key' => $old_key ),
			array( 'key' => $new_key )
		);

		$this->assertEquals( 1, $this->http_request_count );

		$this->configuration_service->set_dataset_uri( $old_dataset_uri );

	}

	/**
	 * Test setting/getting the country code.
	 *
	 * @since 3.20.0
	 */
	public function test_country_code() {

		$country_code = $this->configuration_service->get_country_code();

		$this->configuration_service->set_country_code( 'test' );
		$this->assertEquals( 'test', $this->configuration_service->get_country_code(), 'Value must match.' );

		$this->configuration_service->set_country_code( $country_code );

	}

	/**
	 * Test setting/getting the country code.
	 *
	 * @since 3.20.0
	 */
	public function test_package_type() {

		$package_type = $this->configuration_service->get_package_type();

		$this->configuration_service->set_package_type( 'test' );
		$this->assertEquals( 'test', $this->configuration_service->get_package_type(), 'Value must match.' );

		$this->configuration_service->set_package_type( $package_type );

	}

	/**
	 * @param bool $preempt Whether to preempt an HTTP request return. Default false.
	 * @param array $r HTTP request arguments.
	 * @param string $url The request URL.
	 *
	 * @return bool
	 */
	function pre_http_request( $preempt, $r, $url ) {

		$this->http_request_count ++;

		return true;
	}

}
