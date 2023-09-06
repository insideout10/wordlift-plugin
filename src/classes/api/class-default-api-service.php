<?php

namespace Wordlift\Api;

use Exception;
use Wordlift_Configuration_Service;

class Default_Api_Service implements Api_Service, Api_Service_Ext {

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
	 * @param int    $timeout
	 * @param string $user_agent
	 * @param string $wordlift_key
	 */
	protected function __construct( $base_url, $timeout, $user_agent, $wordlift_key ) {

		$this->log = \Wordlift_Log_Service::get_logger( get_class() );

		$this->base_url     = untrailingslashit( $base_url );
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

	private static $instance;

	/**
	 * @return Default_Api_Service
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self(
				apply_filters( 'wl_api_base_url', WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE ),
				60,
				User_Agent::get_user_agent(),
				Wordlift_Configuration_Service::get_instance()->get_key()
			);
		}

		return self::$instance;
	}

	public function request( $method, $path, $headers = array(), $body = null, $timeout = null, $user_agent = null, $args = array() ) {

		// Get the timeout for this request.
		$request_timeout = isset( $timeout ) ? $timeout : $this->timeout;

		// Set the time limit if lesser than our request timeout.
		$max_execution_time = ini_get( 'max_execution_time' );
		if ( ! ( defined( 'WP_CLI' ) && WP_CLI )
			 && ( 0 !== intval( $max_execution_time ) )
			 && ( $max_execution_time < $request_timeout ) ) {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			@set_time_limit( $request_timeout );
		}

		$request_url = $this->base_url . $path;
		// Create the request args in the following order:
		// 1. use `$args` as base if provided.
		// 2. set the custom timeout if provided.
		// 3. set the custom user-agent if provided.
		// 4. merge the API headers to the provided headers.
		// 5. add the body.
		//
		// In this way the user can fully control the request if wanted (using `$args`) and we can add our defaults.
		$request_args = apply_filters(
			'wl_api_service__request',
			$args + array(
				'method'     => $method,
				'timeout'    => $request_timeout,
				'user-agent' => isset( $user_agent ) ? $user_agent : $this->user_agent,
				'headers'    => $headers + $this->headers + Api_Headers_Service::get_instance()->get_wp_headers(),
				'body'       => $body,
			)
		);

		/**
		 * Allow 3rd parties to process the response.
		 */
		$response = apply_filters(
			'wl_api_service__response',
			wp_remote_request( $request_url, $request_args ),
			$request_url,
			$request_args
		);

		if ( defined( 'WL_DEBUG' ) && WL_DEBUG ) {
			$this->log->trace(
				"=== REQUEST  ===========================\n"
				. "=== URL: $request_url ===========================\n"
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export
				. var_export( $request_args, true )
				. "=== RESPONSE ===========================\n"
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export
				. var_export( $response, true )
			);
		}

		return new Response( $response );
	}

	public function get( $path, $headers = array(), $body = null, $timeout = null, $user_agent = null, $args = array() ) {

		return $this->request( 'GET', $path, $headers, $body, $timeout, $user_agent, $args );
	}

	public function get_base_url() {
		return $this->base_url;
	}

	/**
	 * @return Me_Response
	 * @throws Exception when an error occurs.
	 */
	public function me() {
		return json_decode( $this->get( '/accounts/me' )->get_body() );
	}

}
