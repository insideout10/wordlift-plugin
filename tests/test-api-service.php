<?php
/**
 * Tests: Api Service.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Wordlift_Api_Service_Test class.
 *
 * @since 3.20.0
 * @group api
 */
class Wordlift_Api_Service_Test extends Wordlift_Unit_Test_Case {

	// region ## TESTS
	/**
	 * Test the singleton instance.
	 *
	 * @since 3.20.0
	 */
	public function test_get_instance() {

		$this->assertTrue( Wordlift_Api_Service::get_instance() instanceof Wordlift_Api_Service, 'Singleton instance must be a `Wordlift_Api_Service`.' );

	}

	public function test_should_post_wp_admin_and_wp_json_headers_for_get_request() {

		$callback = array( $this, 'pre_http_request__test_get_data' );

		add_filter( 'pre_http_request', $callback, 10, 3 );

		$response = Wordlift_Api_Service::get_instance()
										->get( 'random_url' );
		$this->assertTrue( isset( $response->example ), 'Response must have an `example` property.' );
		$this->assertEquals( 'json', $response->example, 'The `example` property must be `json`.' );

		remove_filter( 'pre_http_request', $callback );

	}

	public function test_should_post_wp_admin_and_wp_json_headers_for_post_request() {

		$callback = array( $this, 'pre_http_request__test_get_data' );

		add_filter( 'pre_http_request', $callback, 10, 3 );

		$response = Wordlift_Api_Service::get_instance()
										->post( 'random_url', array() );
		$this->assertTrue( isset( $response->example ), 'Response must have an `example` property.' );
		$this->assertEquals( 'json', $response->example, 'The `example` property must be `json`.' );

		remove_filter( 'pre_http_request', $callback );

	}

	public function test_should_post_wp_admin_and_wp_json_headers_for_delete_request() {

		$callback = array( $this, 'pre_http_request__test_get_data' );

		add_filter( 'pre_http_request', $callback, 10, 3 );

		$response = Wordlift_Api_Service::get_instance()
										->delete( 'random_url' );
		$this->assertTrue( isset( $response->example ), 'Response must have an `example` property.' );
		$this->assertEquals( 'json', $response->example, 'The `example` property must be `json`.' );

		remove_filter( 'pre_http_request', $callback );

	}

	/**
	 * Test getting a JSON reply.
	 *
	 * @since 3.20.0
	 */
	public function test_get_json() {

		add_filter( 'pre_http_request', array( $this, 'pre_http_request__test_get_json' ), 10, 3 );

		$response = Wordlift_Api_Service::get_instance()
										->get( 'mock_path' );
		$this->assertTrue( isset( $response->example ), 'Response must have an `example` property.' );
		$this->assertEquals( 'json', $response->example, 'The `example` property must be `json`.' );

		remove_filter( 'pre_http_request', array( $this, 'pre_http_request__test_get_json' ) );

	}

	/**
	 * Test getting a plain text reply.
	 *
	 * @since 3.20.0
	 */
	public function test_get_plain_text() {

		add_filter( 'pre_http_request', array( $this, 'pre_http_request__test_get_text' ), 10, 3 );

		$response = Wordlift_Api_Service::get_instance()
										->get( 'mock_path' );
		$this->assertEquals( 'lorem ipsum', $response, 'Response must be `lorem ipsum`.' );

		remove_filter( 'pre_http_request', array( $this, 'pre_http_request__test_get_text' ) );

	}

	/**
	 * Test an invalid http status code.
	 *
	 * @since 3.20.0
	 */
	public function test_get_invalid_http_status_code() {

		add_filter(
			'pre_http_request',
			array(
				$this,
				'pre_http_request__test_get_invalid_http_status_code',
			),
			10,
			3
		);

		$response = Wordlift_Api_Service::get_instance()
										->get( 'mock_path' );
		$this->assertInstanceOf( 'WP_Error', $response, 'Response must be a `WP_Error`.' );

		remove_filter(
			'pre_http_request',
			array(
				$this,
				'pre_http_request__test_get_invalid_http_status_code',
			)
		);

	}

	/**
	 * Test a WP_Error reply.
	 *
	 * @since 3.20.0
	 */
	public function test_get_error_response() {

		add_filter(
			'pre_http_request',
			array(
				$this,
				'pre_http_request__test_get_error_response',
			),
			10,
			3
		);

		$response = Wordlift_Api_Service::get_instance()
										->get( 'mock_path' );
		$this->assertInstanceOf( 'WP_Error', $response, 'Response must be a `WP_Error`.' );

		remove_filter(
			'pre_http_request',
			array(
				$this,
				'pre_http_request__test_get_error_response',
			)
		);

	}
	// endregion

	// region ## FILTERS.
	/**
	 * Mock the response for the `install` call.
	 *
	 * @param false|array|WP_Error $preempt Whether to preempt an HTTP request's return value. Default false.
	 * @param array                $r HTTP request arguments.
	 * @param string               $url The request URL.
	 *
	 * @return array The response array.
	 * @since 3.20.0
	 */
	public function pre_http_request__test_get_json( $preempt, $r, $url ) {

		$this->common_assertions( 'GET', $url, $r );

		return array(
			'response' => array( 'code' => 200 ),
			'headers'  => array( 'content-type' => 'application/json' ),
			'body'     => '{ "example": "json" }',
		);
	}

	public function pre_http_request__test_get_data( $preempt, $r ) {

		// Return this value only if header has wp json and wp admin urls.
		$headers = $r['headers'];

		if ( ! array_key_exists( 'X-Wordlift-Plugin-Wp-Admin', $headers ) ||
			 ! array_key_exists( 'X-Wordlift-Plugin-Wp-Json', $headers ) ) {
			return array(
				'response' => array( 'code' => 200 ),
				'headers'  => array( 'content-type' => 'application/json' ),
				'body'     => '{}',
			);
		}

		return array(
			'response' => array( 'code' => 200 ),
			'headers'  => array( 'content-type' => 'application/json' ),
			'body'     => '{ "example": "json" }',
		);
	}

	/**
	 * Mock the response for the `install` call.
	 *
	 * @param false|array|WP_Error $preempt Whether to preempt an HTTP request's return value. Default false.
	 * @param array                $r HTTP request arguments.
	 * @param string               $url The request URL.
	 *
	 * @return array The response array.
	 * @since 3.20.0
	 */
	public function pre_http_request__test_get_text( $preempt, $r, $url ) {

		$this->common_assertions( 'GET', $url, $r );

		return array(
			'response' => array( 'code' => 200 ),
			'headers'  => array( 'content-type' => 'text/plain' ),
			'body'     => 'lorem ipsum',
		);
	}

	/**
	 * Mock the response for the `install` call.
	 *
	 * @param false|array|WP_Error $preempt Whether to preempt an HTTP request's return value. Default false.
	 * @param array                $r HTTP request arguments.
	 * @param string               $url The request URL.
	 *
	 * @return array The response array.
	 * @since 3.20.0
	 */
	public function pre_http_request__test_get_invalid_http_status_code( $preempt, $r, $url ) {

		$this->common_assertions( 'GET', $url, $r );

		return array(
			'response' => array( 'code' => 500 ),
			'headers'  => array( 'content-type' => 'text/plain' ),
			'body'     => 'lorem ipsum',
		);
	}

	/**
	 * Mock the response for the `install` call.
	 *
	 * @param false|array|WP_Error $preempt Whether to preempt an HTTP request's return value. Default false.
	 * @param array                $r HTTP request arguments.
	 * @param string               $url The request URL.
	 *
	 * @return WP_Error A WP_Error instance.
	 * @since 3.20.0
	 */
	public function pre_http_request__test_get_error_response( $preempt, $r, $url ) {

		$this->common_assertions( 'GET', $url, $r );

		return new WP_Error();
	}

	/**
	 * Common assertions.
	 *
	 * @param string $method The HTTP method.
	 * @param array  $r HTTP request arguments.
	 * @param string $url The request URL.
	 *
	 * @since 3.20.0
	 */
	private function common_assertions( $method, $url, $r ) {

		$this->assertSame( 1, preg_match( '/\/mock_path$/', $url ), "URL pattern must match, got $url." );
		$this->assertArraySubset( array( 'method' => $method ), $r, "Expect method to be `$$method`." );
		$this->assertSame( 1, preg_match( '/^WordLift\/\d+\.\d+\.\d+(\.\d+)?(-.+)? WordPress\/\d+\.\d+(\.\d+)?(-\S+)? \(multisite:\w+, url:https?:\/\/.+?, locale:\w{2}_\w{2}\) PHP\/\d+\.\d+\.\d+$/', $r['user-agent'] ), "User-Agent must match, got {$r['user-agent']}." );
		$this->assertTrue( isset( $r['headers']['X-Authorization'] ), 'The `X-Authorization` header must be set.' );

	}
	// endregion

}
