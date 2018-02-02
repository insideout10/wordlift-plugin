<?php
/**
 * Adapters: WordLift Keyword.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_Keyword_Adapter} class.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_Keyword_Adapter {

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * The {@link Wordlift_Http_Client} instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Http_Client $http_client The {@link Wordlift_Http_Client} instance.
	 */
	private $http_client;

	/**
	 * The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

	/**
	 * Wordlift_Keyword_Adapter constructor.
	 *
	 * @since 3.18.0
	 *
	 * @param \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 * @param \Wordlift_Http_Client           $http_client           The {@link Wordlift_Http_Client} instance.
	 */
	public function __construct( $configuration_service, $http_client ) {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		$this->http_client = $http_client;

		$this->configuration_service = $configuration_service;

	}

	/**
	 * Get the keywords from the server.
	 *
	 * @since 3.18.0
	 */
	public function get_keyword_rows() {
		// Submit the request.
		$response = $this->http_client
			->do_request( $this->configuration_service->get_keyword_rows_url() );

		$this->handle_response( $response );

	}

	/**
	 * Add new keyword.
	 *
	 * @since 3.18.0
	 */
	public function create() {

		// Bail if the required params are empty.
		if ( empty( $_REQUEST['keyword'] ) ) {
			wp_send_json_error( 'The `keyword` parameter is required.' );
		}

		// Submit the request.
		$response = $this->http_client->do_request(
			$this->configuration_service->get_keywords_url(), // Request url.
			'POST', // Request method.
			array(
				'value' => sanitize_text_field( wp_unslash( $_REQUEST['keyword'] ) ),
				// Request methods.
			)
		);

		$this->handle_response( $response );

	}

	/**
	 * Delete keyword.
	 *
	 * @since 3.18.0
	 */
	public function delete() {

		// Bail if the required params are empty.
		if ( empty( $_REQUEST['keyword'] ) ) {
			wp_send_json_error( 'The `keyword` parameter is required.' );
		}

		$keyword = sanitize_text_field( wp_unslash( $_REQUEST['keyword'] ) );

		// Submit the request.
		$response = $this->http_client->do_request(
			$this->configuration_service->get_keywords_url( $keyword ),
			'DELETE'
		);

		$this->handle_response( $response );

	}

	/**
	 * Send success/error message
	 *
	 * @since 3.18.0
	 *
	 * @param array $response Server response.
	 */
	public function handle_response( $response ) {
		// Clear any buffer.
		ob_clean();

		// If the response is valid, then send the suggestions.
		if ( ! is_wp_error( $response ) && 2 === (int) ( $response['response']['code'] / 100 ) ) {
			// Echo the response.
			wp_send_json_success( json_decode( wp_remote_retrieve_body( $response ), true ) );

			return;
		}

		// Get the real error message if there is WP_Error.
		if ( isset( $response['response']['message'] ) ) {
			$error_message = $response['response']['message'];
		} elseif ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
		} else {
			$error_message = 'Unknown Error';
		}

		// There is an error, so send error message.
		wp_send_json_error( array(
			/* translators: Placeholders: %s - the error message that will be returned. */
			'message' => sprintf( esc_html__( 'Error: %s', 'wordlift' ), $error_message ),
		) );

	}

}
