<?php
/**
 * Tests: Install Package Type test.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Test_Wordlift_Install_Package_Type class.
 *
 * @since 3.20.0
 * @group install
 */
class Wordlift_Install_Package_Type_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test that `must_install` returns true.
	 *
	 * @since 3.20.0
	 */
	public function test_must_install_true() {

		if ( empty( $this->configuration_service->get_key() ) ) {
			$this->markTestSkipped( 'The env WORDLIFT_KEY must be set for this test to work.' );
		}

		$this->set_current_screen( 'dashboard-user' );

		$package_type = $this->configuration_service->get_package_type();
		$this->configuration_service->set_package_type( null );

		// 3 conditions: `in_admin`, `key` and empty `package_type`.
		$this->assertNotEmpty( $this->configuration_service->get_key(), 'A `key` is required for the test.' );
		$this->assertEmpty( $this->configuration_service->get_package_type(), 'An empty `package_type` is required for the test.' );

		$install_package_type = new Wordlift_Install_Package_Type();
		$this->assertTrue( $install_package_type->must_install(), 'Expect `must_install` to be truthy.' );

		$this->configuration_service->set_package_type( $package_type );
		$this->restore_current_screen();

	}

	/**
	 * Test that `must_install` returns false when not in `admin`.
	 *
	 * @since 3.20.0
	 */
	public function test_must_install_not_in_admin() {

		if ( empty( $this->configuration_service->get_key() ) ) {
			$this->markTestSkipped( 'The env WORDLIFT_KEY must be set for this test to work.' );
		}

		$package_type = $this->configuration_service->get_package_type();
		$this->configuration_service->set_package_type( null );

		// 3 conditions: not `in_admin`, `key` and empty `package_type`.
		$this->assertNotEmpty( $this->configuration_service->get_key(), 'A `key` is required for the test.' );
		$this->assertEmpty( $this->configuration_service->get_package_type(), 'An empty `package_type` is required for the test.' );

		$install_package_type = new Wordlift_Install_Package_Type();
		$this->assertFalse( $install_package_type->must_install(), 'Expect `must_install` to be false.' );

		$this->configuration_service->set_package_type( $package_type );

	}

	/**
	 * Test that `must_install` returns false when `key` not set.
	 *
	 * @since 3.20.0
	 */
	public function test_must_install_key_empty() {

		$this->set_current_screen( 'dashboard-user' );

		$package_type = $this->configuration_service->get_package_type();
		$this->configuration_service->set_package_type( null );

		// 3 conditions: `in_admin`, `key` and empty `package_type`.
		$key = $this->configuration_service->get_key();
		$this->configuration_service->set_key( null );
		$this->assertEmpty( $this->configuration_service->get_key(), 'An empty `key` is required for the test.' );
		$this->assertEmpty( $this->configuration_service->get_package_type(), 'An empty `package_type` is required for the test.' );

		$install_package_type = new Wordlift_Install_Package_Type();
		$this->assertFalse( $install_package_type->must_install(), 'Expect `must_install` to be false.' );

		$this->configuration_service->set_key( $key );
		$this->configuration_service->set_package_type( $package_type );
		$this->restore_current_screen();

	}

	/**
	 * Test that `must_install` returns false when package type not set.
	 *
	 * @since 3.20.0
	 */
	public function test_must_install_package_type_not_empty() {

		if ( empty( $this->configuration_service->get_key() ) ) {
			$this->markTestSkipped( 'The env WORDLIFT_KEY must be set for this test to work.' );
		}

		$this->set_current_screen( 'dashboard-user' );

		$package_type = $this->configuration_service->get_package_type();
		$this->configuration_service->set_package_type( 'blogger' );

		// 3 conditions: `in_admin`, `key` and empty `package_type`.
		$this->assertNotEmpty( $this->configuration_service->get_key(), 'A `key` is required for the test.' );
		$this->assertNotEmpty( $this->configuration_service->get_package_type(), 'A `package_type` is required for the test.' );

		$install_package_type = new Wordlift_Install_Package_Type();
		$this->assertFalse( $install_package_type->must_install(), 'Expect `must_install` to be false.' );

		$this->configuration_service->set_package_type( null );

		$this->configuration_service->set_package_type( $package_type );
		$this->restore_current_screen();

	}

	public function test_install_empty_key() {

		$key = $this->configuration_service->get_key();
		$this->configuration_service->set_key( null );

		$package_type = $this->configuration_service->get_package_type();
		$this->configuration_service->set_package_type( null );

		$install_package_type = new Wordlift_Install_Package_Type();
		$install_package_type->install();

		$this->assertEmpty( $this->configuration_service->get_package_type(), '`package_type` should be empty.' );

		// Restore.
		$this->configuration_service->set_key( $key );
		$this->configuration_service->set_package_type( $package_type );

	}

	public function test_install() {

		if ( empty( $this->configuration_service->get_key() ) ) {
			$this->markTestSkipped( 'The env WORDLIFT_KEY must be set for this test to work.' );
		}

		$package_type = $this->configuration_service->get_package_type();
		$this->configuration_service->set_package_type( null );

		add_filter( 'pre_http_request', array( $this, 'pre_http_request' ), 10, 3 );

		$install_package_type = new Wordlift_Install_Package_Type();
		$install_package_type->install();

		remove_filter( 'pre_http_request', array( $this, 'pre_http_request' ) );

		$this->assertEquals( 'mock_package_type', $this->configuration_service->get_package_type(), '`package_type` should match.' );

		// Restore.
		$this->configuration_service->set_package_type( $package_type );

	}

	/**
	 * Mock the response for the `install` call.
	 *
	 * @since 3.20.0
	 *
	 * @param false|array|WP_Error $preempt Whether to preempt an HTTP request's return value. Default false.
	 * @param array                $r HTTP request arguments.
	 * @param string               $url The request URL.
	 *
	 * @return array The response array.
	 */
	public function pre_http_request( $preempt, $r, $url ) {

		$this->assertEquals( 1, preg_match( '/\/accounts\?key=\w+\&url=[\w%\.]+\&country=\w{2}\&language=\w{2}$/', $url ), "URL pattern must match, this is the URL call WLS expects. Got $url." );
		$this->assertArraySubset( array( 'method' => 'PUT' ), $r, 'Expect method to be `PUT`.' );

		$dataset_uri = $this->configuration_service->get_dataset_uri();

		return array(
			'response' => array( 'code' => 200 ),
			'headers'  => array( 'content-type' => 'application/json' ),
			'body'     => '{ "datasetURI": "' . $dataset_uri . '", "packageType": "mock_package_type" }',
		);
	}

}
