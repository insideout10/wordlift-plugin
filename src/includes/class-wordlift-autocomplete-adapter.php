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

		check_ajax_referer('wl_autocomplete');

		// Return error if the query param is empty.
		if ( ! empty( $_REQUEST['query'] ) ) { // Input var okay.
			$query = sanitize_text_field( wp_unslash( $_REQUEST['query'] ) ); // Input var okay.
		} else {
			wp_send_json_error( array(
				'message' => __( 'The query param is empty.', 'wordlift' ),
			) );
		}

		// Get the exclude parameter.
		$exclude = ! empty( $_REQUEST['exclude'] )
			? sanitize_text_field( wp_unslash( $_REQUEST['exclude'] ) ) : '';

		$scope = ! empty( $_REQUEST['scope'] )
			? sanitize_text_field( wp_unslash( $_REQUEST['scope'] ) ) : WL_AUTOCOMPLETE_SCOPE;

		// Make request.
		$response = $this->autocomplete_service->make_request( $query, $exclude, $scope );

		// Clear any buffer.
		ob_clean();

		// If the response is valid, then send the suggestions.
		if ( ! is_wp_error( $response ) && 200 === (int) $response['response']['code'] ) {
			// Echo the response.
			wp_send_json_success( json_decode( wp_remote_retrieve_body( $response ), true ) );
		} else {
			// Default error message.
			$error_message = 'Something went wrong.';

			// Get the real error message if there is WP_Error.
			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
			}

			// There is an error, so send error message.
			wp_send_json_error( array(
				/* translators: Placeholders: %s - the error message that will be returned. */
				'message' => sprintf( esc_html__( 'Error: %s', 'wordlift' ), $error_message ),
			) );
		}
	}
}
