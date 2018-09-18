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
	 * @param string       $query The search string.
	 * @param array|string $exclude The exclude parameter string.
	 * @param string       $scope The search scope: "local" will search only in the local dataset; "cloud" will search also
	 *                      in Wikipedia. By default is "cloud".
	 *
	 * @return array $response The API response.
	 */
	public function make_request( $query, $exclude = '', $scope = 'cloud' ) {
		$url = $this->build_request_url( $query, $exclude, $scope );

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
	 * @param string       $query The search string.
	 * @param array|string $exclude The exclude parameter.
	 * @param string       $scope The search scope: "local" will search only in the local dataset; "cloud" will search also
	 *                      in Wikipedia. By default is "cloud".
	 *
	 * @return string Built url.
	 */
	private function build_request_url( $query, $exclude, $scope ) {
		$args = array(
			'key'      => $this->configuration_service->get_key(),
			'language' => $this->configuration_service->get_language_code(),
			'query'    => $query,
			'scope'    => $scope,
			'limit'    => 100,
		);

		// Add args to URL.
		$request_url = add_query_arg(
			urlencode_deep( $args ),
			$this->configuration_service->get_autocomplete_url()
		);

		// Add the exclude parameter.
		if ( ! empty( $exclude ) ) {
			foreach ( (array) $exclude as $item ) {
				$request_url .= "&exclude=" . urlencode( $item );
			}
		}

		// return the built url.
		return $request_url;
	}
}
