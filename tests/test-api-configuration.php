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

	function setUp() {
		parent::setUp();

		add_filter( 'pre_http_request', array( $this, '_mock_api', ), 10, 3 );

	}

	function tearDown() {
		remove_filter( 'pre_http_request', array( $this, '_mock_api' ) );

		parent::tearDown();
	}


	function _mock_api( $response, $request, $url ) {
		$method = $request['method'];
		if ( 'PUT' === $method && preg_match( '@/accounts\?key=test_wl_configuration_key&url=http%3A%2F%2Fexample.org&country=us&language=en$@', $url ) ) {
			return array(
				'body'     => '{ "datasetURI": "https://data.localdomain.localhost/dataset", "packageType": "unknown" }',
				'response' => array( 'code' => 200, )
			);
		}

		if ( 'PUT' === $method && preg_match( '@/accounts\?key=key123&url=http%3A%2F%2Fexample.org&country=us&language=kl$@', $url ) ) {
			return array(
				'body'     => '{ "datasetURI": "https://data.localdomain.localhost/dataset", "packageType": "unknown" }',
				'response' => array( 'code' => 200, )
			);
		}

		if ( 'PUT' === $method && preg_match( '@/accounts\?key=key123&url=http%3A%2F%2Fexample.org&country=test&language=en$@', $url ) ) {
			return array(
				'body'     => '{ "datasetURI": "https://data.localdomain.localhost/dataset", "packageType": "unknown" }',
				'response' => array( 'code' => 200, )
			);
		}

		return $response;
	}

	/**
	 * Test setting a new key, that the key is saved in the configuration.
	 */
	function test_wl_configuration_key() {

		$value = 'test_wl_configuration_key';
		Wordlift_Configuration_Service::get_instance()->set_key( $value );

		$this->assertEquals( $value, Wordlift_Configuration_Service::get_instance()->get_key() );

	}


	/**
	 * Test the `link by default` setting.
	 *
	 * @since 3.13.0
	 */
	public function test_is_link_by_default() {

		// Check that the default setting is `true`.
		$this->assertTrue( Wordlift_Configuration_Service::get_instance()->is_link_by_default() );

		Wordlift_Configuration_Service::get_instance()->set_link_by_default( true );

		$this->assertTrue( Wordlift_Configuration_Service::get_instance()->is_link_by_default() );

		Wordlift_Configuration_Service::get_instance()->set_link_by_default( false );

		$this->assertFalse( Wordlift_Configuration_Service::get_instance()->is_link_by_default() );

		Wordlift_Configuration_Service::get_instance()->set_link_by_default( 1 );

		$this->assertFalse( Wordlift_Configuration_Service::get_instance()->is_link_by_default() );

	}

	/**
	 */
	function test_maybe_update_dataset_uri_new_key_empty() {

		add_filter( 'pre_http_request', array(
			$this,
			'pre_http_request',
		), 10, 3 );

		Wordlift_Configuration_Service::get_instance()->maybe_update_dataset_uri(
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

		Wordlift_Configuration_Service::get_instance()->maybe_update_dataset_uri(
			array( 'key' => $old_key ),
			array( 'key' => $new_key )
		);

		$this->assertEquals( 0, $this->http_request_count );

	}

	/**
	 */
	function test_maybe_update_dataset_uri_dataset_uri_not_empty() {

		\Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		add_filter( 'pre_http_request', array( $this, 'pre_http_request', ), 10, 3 );

		$old_key = $new_key = uniqid( true );

		Wordlift_Configuration_Service::get_instance()->maybe_update_dataset_uri(
			array( 'key' => $old_key ),
			array( 'key' => $new_key )
		);

		$this->assertNotEmpty( Wordlift_Configuration_Service::get_instance()->get_dataset_uri() );

		$this->assertEquals( 0, $this->http_request_count );

	}

	/**
	 */
	function test_maybe_update_dataset_uri() {

		add_filter( 'pre_http_request', array(
			$this,
			'pre_http_request',
		), 10, 3 );

		$old_dataset_uri = Wordlift_Configuration_Service::get_instance()->get_dataset_uri();
		Wordlift_Configuration_Service::get_instance()->set_dataset_uri( '' );

		$old_key = $new_key = 'test_wl_configuration_key';

		Wordlift_Configuration_Service::get_instance()->maybe_update_dataset_uri(
			array( 'key' => $old_key ),
			array( 'key' => $new_key )
		);

		$this->assertEquals( 1, $this->http_request_count );

		Wordlift_Configuration_Service::get_instance()->set_dataset_uri( $old_dataset_uri );

	}

	/**
	 * Test setting/getting the country code.
	 *
	 * @since 3.20.0
	 */
	public function test_country_code() {

		$country_code = Wordlift_Configuration_Service::get_instance()->get_country_code();

		Wordlift_Configuration_Service::get_instance()->set_country_code( 'test' );
		$this->assertEquals( 'test', Wordlift_Configuration_Service::get_instance()->get_country_code(), 'Value must match.' );

		Wordlift_Configuration_Service::get_instance()->set_country_code( $country_code );

	}

	/**
	 * Test setting/getting the country code.
	 *
	 * @since 3.20.0
	 */
	public function test_package_type() {

		$package_type = Wordlift_Configuration_Service::get_instance()->get_package_type();

		Wordlift_Configuration_Service::get_instance()->set_package_type( 'test' );
		$this->assertEquals( 'test', Wordlift_Configuration_Service::get_instance()->get_package_type(), 'Value must match.' );

		Wordlift_Configuration_Service::get_instance()->set_package_type( $package_type );

	}


	public function test_when_the_language_code_not_set_by_wordlift_should_use_system_language_code() {
		global $wp_filter;
		// Dont trigger any filters.
		$wp_filter_copy = $wp_filter;
		$wp_filter      = array();

		// we set a custom language code in configuration, but we should not get this value.
		Wordlift_Configuration_Service::get_instance()->set_language_code( 'ta' );

		// set the filters back.
		$wp_filter = $wp_filter_copy;

		//substr( get_bloginfo('language'), 0, 2)
		$this->assertSame( 'en', Wordlift_Configuration_Service::get_instance()->get_language_code(), 'Language code should not be from wordlift settings.' );
	}


	public function test_when_linked_datasets_not_found_should_return_empty_array() {
		$this->assertCount( 0, Wordlift_Configuration_Service::get_instance()->get_network_dataset_ids(), "Should return empty array with zero dataset ids" );
	}

	public function test_should_be_able_to_set_network_dataset_ids() {
		$network_dataset_ids = array( 'one', 'two', 'three' );
		Wordlift_Configuration_Service::get_instance()->set_network_dataset_ids( $network_dataset_ids );
		$this->assertEquals(
			$network_dataset_ids,
			Wordlift_Configuration_Service::get_instance()->get_network_dataset_ids(),
			"Should return correct dataset ids"
		);
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
