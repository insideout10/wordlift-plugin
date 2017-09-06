<?php
/**
 * Wordlift_Autocomplete_Adapter class.
 *
 * The {@link Wordlift_Autocomplete_Adapter} class create requests to external API's.
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
 * Create autocomplete request to external API's and return the result if there is such.
 *
 * @since 3.15.0
 */
class Wordlift_Autocomplete_Adapter {

	/**
	 * The {@link Wordlift_Autocomplete_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Autocomplete_Service $configuration_service The {@link Wordlift_Autocomplete_Service} instance.
	 */
	private $autocomplete_service;


	/**
	 * Wordlift_Autocomplete_Adapter constructor.
	 *
	 * @since 3.14.2
	 *
	 * @param \Wordlift_Autocomplete_Service $autocomplete_service The {@link Wordlift_Autocomplete_Service} instance.
	 */
	public function __construct( $autocomplete_service ) {
		$this->autocomplete_service = $autocomplete_service;
	}

	/**
	 * Handle the autocomplete ajax request.
	 *
	 * @since 3.15.0
	 */
	public function wl_autocomplete() {
		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'wordlift_autocomplete' ) ) {
			wp_send_json_error( array(
				'message' => 'Nonce field doens\'t match',
			) );
		}

		// Return error if the query param is empty.
		if ( ! empty( $_REQUEST['query'] ) ) {
			$query = wp_unslash( $_REQUEST['query'] );
		} else {
			wp_send_json_error( array(
				'message' => 'The query param is empty!',
			) );
		}

		// Make request.
		$response = $this->autocomplete_service->make_request( $query );
		// Decode response body.
		$suggestions = json_decode( wp_remote_retrieve_body( $response ), true );

		// Clear any buffer.
		ob_clean();

		// If the response is valid, then send the suggestions.
		if ( ! is_wp_error( $response ) && 200 === (int) $response['response']['code'] ) {
			// Echo the response.
			wp_send_json_success( array(
				'suggestions' => $suggestions,
			) );
		} else {
			// There is an error, so send error message.
			wp_send_json_error( array(
				'message' => __( 'Something went wrong.' ),
			) );
		}
	}
}
