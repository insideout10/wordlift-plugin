<?php
/**
 * Services: API Service.
 *
 * A service which handles API calls towards the remote SaaS infrastructure.
 *
 * @since 3.20.0
 */

/**
 * Define the {@link Wordlift_Api_Service} class.
 *
 * @since 3.20.0
 */
class Wordlift_Api_Service {

	/**
	 * The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

	/**
	 * The {@link Wordlift_Api_Service} singleton instance.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var \Wordlift_Api_Service $instance The singleton instance.
	 */
	private static $instance;

	/**
	 * Create a {@link Wordlift_Api_Service} instance.
	 *
	 * @param $configuration_service \Wordlift_Configuration_Service The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since 3.20.0
	 *
	 */
	public function __construct( $configuration_service ) {

		$this->configuration_service = $configuration_service;

		self::$instance = $this;
	}

	/**
	 * Get the {@link Wordlift_Api_Service} singleton instance.
	 *
	 * @return \Wordlift_Api_Service The {@link Wordlift_Api_Service} singleton instance.
	 * @since 3.20.0
	 *
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Perform a `GET` request towards the requested path.
	 *
	 * @param string $path The relative path.
	 *
	 * @return array|WP_Error
	 * @since 3.20.0
	 *
	 */
	public function get( $path ) {

		// Prepare the target URL.
		$url = $this->configuration_service->get_api_url() . $path;

		// Get the response value.
		$response = wp_remote_get( $url, array(
			'user-agent' => self::get_user_agent(),
			'headers'    => array(
				'X-Authorization' => $this->configuration_service->get_key(),
			),
			/*
			 * Increase the timeout from the default of 5 to 30 secs.
			 *
			 * @see https://github.com/insideout10/wordlift-plugin/issues/906
			 *
			 * @since 3.20.1
			 */
			'timeout'    => 30,
		) );

//		var_dump( array(
//			$url,
//			array(
//				'user-agent' => self::get_user_agent(),
//				'headers'    => array(
//					'X-Authorization' => $this->configuration_service->get_key(),
//				),
//				/*
//				 * Increase the timeout from the default of 5 to 30 secs.
//				 *
//				 * @see https://github.com/insideout10/wordlift-plugin/issues/906
//				 *
//				 * @since 3.20.1
//				 */
//				'timeout'    => 30,
//			)
//		) );
//		wp_die();

		return self::get_message_or_error( $response );
	}

	/**
	 * Perform a `POST` request towards the requested path.
	 *
	 * @param string $path The relative path.
	 * @param array|object $body The request body (will be serialized to JSON).
	 *
	 * @return array|WP_Error
	 * @since 3.20.0
	 *
	 */
	public function post( $path, $body ) {

		return $this->post_custom_content_type(
			$path, json_encode( $body ), 'application/json; ' . get_bloginfo( 'charset' )
		);
	}

	public function post_custom_content_type( $path, $body, $content_type ) {

		// Prepare the target URL.
		$url = apply_filters( 'wl_api_service_api_url_path', $this->configuration_service->get_api_url() . $path );

		// Give some time for the operation to complete, more than the time we give to the HTTP operation to complete.
		@set_time_limit(90);

		// Get the response value.
		$response = wp_remote_post( $url, array(
			'timeout'    => 60,
			'user-agent' => self::get_user_agent(),
			'headers'    => array(
				'Content-Type'    => $content_type,
				'X-Authorization' => $this->configuration_service->get_key(),
				'Authorization'   => "Key {$this->configuration_service->get_key()}",
				/*
				 * This is required to avoid CURL receiving 502 Bad Gateway errors.
				 *
				 * @see https://stackoverflow.com/questions/30601075/curl-to-google-compute-load-balancer-gets-error-502
				 */
				'Expect'          => '',
			),
			'body'       => $body,
		) );

		return self::get_message_or_error( $response );
	}

	public function delete( $path ) {

		// Prepare the target URL.
		$url = $this->configuration_service->get_api_url() . $path;

		// Get the response value.
		$response = wp_remote_request( $url, array(
			'method'     => 'DELETE',
			'user-agent' => self::get_user_agent(),
			'headers'    => array(
				'X-Authorization' => $this->configuration_service->get_key(),
			),
		) );

		return self::get_message_or_error( $response );
	}

	/**
	 * Return the {@link WP_Error} in case of error or the actual reply if successful.
	 *
	 * @param array|WP_Error $response The response of an http call.
	 *
	 * @return string|object|WP_Error A {@link WP_Error} instance or the actual response content.
	 * @since 3.20.0
	 *
	 */
	private static function get_message_or_error( $response ) {

		// Result is WP_Error.
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// `code` not set or not numeric.
		$code    = wp_remote_retrieve_response_code( $response );
		$message = @wp_remote_retrieve_response_message( $response );

		if ( empty( $code ) || ! is_numeric( $code ) ) {
			return new WP_Error( 0, $message );
		}

		// Code not 2xx.
		if ( 2 !== intval( $code / 100 ) ) {
			return new WP_Error( $code, $message );
		}

		// Everything's fine, return the message.
		return self::try_json_decode( $response );
	}

	/**
	 * Try to decode the json response
	 *
	 * @param array $response The response array.
	 *
	 * @return array|mixed|object The decoded response or the original response body.
	 * @since 3.20.0
	 *
	 */
	private static function try_json_decode( $response ) {

		// Get the headers.
		$content_type = wp_remote_retrieve_header( $response, 'content-type' );
		$body         = wp_remote_retrieve_body( $response );

		// If it's not an `application/json` return the plain response body.
		if ( 0 !== strpos( strtolower( $content_type ), 'application/json' ) ) {
			return $body;
		}

		// Decode and return the structured result.
		return json_decode( $body );
	}

	/**
	 * Get the User Agent.
	 *
	 * @return string The user-agent string.
	 * @since 3.20.0
	 */
	private static function get_user_agent() {

		// Get WL version.
		$wl_version = Wordlift::get_instance()->get_version();

		// Get the WP version.
		$wp_version = get_bloginfo( 'version' );

		// Get the home url.
		$home_url = home_url( '/' );

		// Get the locale flag.
		$locale = apply_filters( 'core_version_check_locale', get_locale() );

		// Get the multisite flag.
		$multisite = is_multisite() ? 'yes' : 'no';

		// Get the PHP version.
		$php_version = phpversion();

		/** @var string $wp_version The variable is defined in `version.php`. */
		return "WordLift/$wl_version WordPress/$wp_version (multisite:$multisite, url:$home_url, locale:$locale) PHP/$php_version";
	}

}
