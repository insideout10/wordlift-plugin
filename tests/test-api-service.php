<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 2018-11-30
 * Time: 12:03
 */


class Wordlift_Api_Service_Test extends Wordlift_Unit_Test_Case {

	public function test_get_instance() {

		$this->assertTrue( Wordlift_Api_Service::get_instance() instanceof Wordlift_Api_Service, 'Singleton instance must be a `Wordlift_Api_Service`.' );

	}

	public function test_get_json() {

		add_filter( 'pre_http_request', array( $this, 'pre_http_request__test_get_json' ), 10, 3 );

		$response = Wordlift_Api_Service::get_instance()
		                                ->get( 'mock_path' );
		$this->assertTrue( isset( $response->example ), 'Response must have an `example` property.' );
		$this->assertEquals( 'json', $response->example, 'The `example` property must be `json`.' );

		remove_filter( 'pre_http_request', array( $this, 'pre_http_request__test_get_json' ) );

	}

	public function test_get_plain_text() {

		add_filter( 'pre_http_request', array( $this, 'pre_http_request__test_get_text' ), 10, 3 );

		$response = Wordlift_Api_Service::get_instance()
		                                ->get( 'mock_path' );
		$this->assertEquals( 'lorem ipsum', $response, 'Response must be `lorem ipsum`.' );

		remove_filter( 'pre_http_request', array( $this, 'pre_http_request__test_get_text' ) );

	}


	public function test_get_invalid_http_status_code() {

		add_filter( 'pre_http_request', array( $this, 'pre_http_request__test_get_invalid_http_status_code' ), 10, 3 );

		$response = Wordlift_Api_Service::get_instance()
		                                ->get( 'mock_path' );
		$this->assertInstanceOf( 'WP_Error', $response, 'Response must be a `WP_Error`.' );

		remove_filter( 'pre_http_request', array( $this, 'pre_http_request__test_get_invalid_http_status_code' ) );

	}

	public function test_get_error_response() {

		add_filter( 'pre_http_request', array( $this, 'pre_http_request__test_get_error_response' ), 10, 3 );

		$response = Wordlift_Api_Service::get_instance()
		                                ->get( 'mock_path' );
		$this->assertInstanceOf( 'WP_Error', $response, 'Response must be a `WP_Error`.' );

		remove_filter( 'pre_http_request', array( $this, 'pre_http_request__test_get_error_response' ) );

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
	public function pre_http_request__test_get_json( $preempt, $r, $url ) {

		$this->common_assertions( 'GET', $url, $r );

		return array(
			'response' => array( 'code' => 200 ),
			'headers'  => new Requests_Response_Headers( array( 'content-type' => 'application/json' ) ),
			'body'     => '{ "example": "json" }',
		);
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
	public function pre_http_request__test_get_text( $preempt, $r, $url ) {

		$this->common_assertions( 'GET', $url, $r );

		return array(
			'response' => array( 'code' => 200 ),
			'headers'  => new Requests_Response_Headers( array( 'content-type' => 'text/plain' ) ),
			'body'     => 'lorem ipsum',
		);
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
	public function pre_http_request__test_get_invalid_http_status_code( $preempt, $r, $url ) {

		$this->common_assertions( 'GET', $url, $r );

		return array(
			'response' => array( 'code' => 500 ),
			'headers'  => new Requests_Response_Headers( array( 'content-type' => 'text/plain' ) ),
			'body'     => 'lorem ipsum',
		);
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
	 * @return WP_Error A WP_Error instance.
	 */
	public function pre_http_request__test_get_error_response( $preempt, $r, $url ) {

		$this->common_assertions( 'GET', $url, $r );

		return new WP_Error();
	}

	/**
	 * Common assertions.
	 *
	 * @since 3.20.0
	 *
	 * @param string $method The HTTP method.
	 * @param array  $r HTTP request arguments.
	 * @param string $url The request URL.
	 */
	private function common_assertions( $method, $url, $r ) {

		$this->assertEquals( 1, preg_match( '/^https:\/\/api\.wordlift\.it\/mock_path$/', $url ), 'URL pattern must match.' );
		$this->assertArraySubset( array( 'method' => $method ), $r, "Expect method to be `$$method`." );
		$this->assertEquals( 1, preg_match( '/^WordLift\/\d+\.\d+\.\d+(\S+)? WordPress\/\d+\.\d+\.\d+ \(multisite:\w+, url:https?:\/\/.+?, locale:\w{2}_\w{2}\) PHP\/\d+\.\d+\.\d+$/', $r['user-agent'] ), 'User-Agent must match.' );
		$this->assertTrue( isset( $r['headers']['X-Authorization'] ), 'The `X-Authorization` header must be set.' );

	}

}
