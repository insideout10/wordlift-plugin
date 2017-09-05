<?php
/**
 * Wordlift_Autocomplete_Service class.
 *
 * The {@link Wordlift_Autocomplete_Service} class handle and process all autocomplete requests.
 *
 * @link       https://wordlift.io
 *
 * @package    Wordlift
 * @since      3.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Process WordLift's autocomplete requests.
 *
 * @since 3.15.0
 */
class Wordlift_Autocomplete_Service {
	/**
	 * The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * The {@link Class_Wordlift_Autocomplete_Service} instance.
	 *
	 * @since 3.15.0
	 *
	 * @param \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	public function __construct( $configuration_service ) {
		$this->configuration_service = $configuration_service;
		$this->log                   = Wordlift_Log_Service::get_logger( 'Wordlift_Autocomplete_Service' );
	}

	/**
	 * Make request to external API and return the response.
	 *
	 * @since 3.15.0
	 *
	 * @param string $query The search string.
	 *
	 * @return array $response The API response.
	 */
	public function make_request( $query ) {
		$url = $this->build_request_url( $query );

		// Make request.
		$response = wp_remote_get( $url );

		// Return the response.
		return $response;
	}

	/**
	 * Build the autocomplete url.
	 *
	 * @since 3.15.0
	 *
	 * @param string $query The search string.
	 *
	 * @return string Builded url.
	 */
	public function build_request_url( $query ) {
		$args = array(
			'key'      => $this->configuration_service->get_key(),
			'language' => $this->configuration_service->get_language_code(),
			'query'    => $query,
			'limit'    => 50,
		);

		// Add args to URL.
		$request_url = add_query_arg(
			$args,
			$this->configuration_service->get_autocomplete_url()
		);

		// return the builded url.
		return $request_url;
	}
}
