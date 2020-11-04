<?php

/**
 *
 */

namespace Wordlift\Api;

class Default_Api_Service implements Api_Service {
	/**
	 * @var Default_Api_Service
	 */
	private static $instance;

	/**
	 * @var string
	 */
	private $wordlift_key;
	/**
	 * @var int
	 */
	private $timeout;

	/**
	 * @var string
	 */
	private $user_agent;

	/**
	 * @var array
	 */
	private $headers;
	/**
	 * @var string
	 */
	private $base_url;

	/**
	 * @var \Wordlift_Log_Service
	 */
	private $log;

	/**
	 * Default_Api_Service constructor.
	 *
	 * @param string $base_url
	 * @param int $timeout
	 * @param string $user_agent
	 * @param string $wordlift_key
	 */
	public function __construct( $base_url, $timeout, $user_agent, $wordlift_key ) {

		$this->log = \Wordlift_Log_Service::get_logger( get_class() );

		$this->base_url     = $base_url;
		$this->timeout      = $timeout;
		$this->user_agent   = $user_agent;
		$this->wordlift_key = $wordlift_key;

		$this->headers = array(
			'Content-Type'  => 'application/json',
			'Authorization' => "Key $wordlift_key",
			'Expect'        => '',
		);

		self::$instance = $this;
	}

	public static function get_instance() {
		return self::$instance;
	}

	public function request( $method, $url, $headers = array(), $body = null, $timeout = null, $user_agent = null, $args = array() ) {

		// Get the timeout for this request.
		$request_timeout = isset( $timeout ) ? $timeout : $this->timeout;

		// Set the time limit if lesser than our request timeout.
		$max_execution_time = ini_get( 'max_execution_time' );
		if ( $max_execution_time < $request_timeout ) {
			@set_time_limit( $request_timeout );
		}

		$request_url = $this->base_url . $url;

		// Create the request args in the following order:
		//  1. use `$args` as base if provided.
		//  2. set the custom timeout if provided.
		//  3. set the custom user-agent if provided.
		//  4. merge the API headers to the provided headers.
		//  5. add the body.
		//
		// In this way the user can fully control the request if wanted (using `$args`) and we can add our defaults.
		$request_args = $args + array(
				'method'     => $method,
				'timeout'    => $request_timeout,
				'user-agent' => isset( $user_agent ) ? $user_agent : $this->user_agent,
				'headers'    => $headers + $this->headers,
				'body'       => $body,
			);

		error_log( var_export( array( $request_url, $request_args ), true ) );

		$response = wp_remote_request( $request_url, $request_args );

		if ( defined( 'WL_DEBUG' ) && WL_DEBUG ) {
			$this->log->trace(
				"=== REQUEST  ===========================\n"
				. var_export( $request_args, true )
				. "=== RESPONSE ===========================\n"
				. var_export( $response, true ) );
		}

		return new Response( $response );
	}

	public function get( $url, $headers = array(), $body = null, $timeout = null, $user_agent = null, $args = array() ) {

		return $this->request( 'GET', $url, $headers, $body, $timeout, $user_agent, $args );
	}

}
