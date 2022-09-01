<?php
/**
 * Ajax Adapters: Publisher Ajax Adapter.
 *
 * An Ajax adapter to the {@link Wordlift_Publisher_Service}.
 *
 * @since   3.11.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Publisher_Ajax_Adapter} instance.
 *
 * @since   3.11.0
 * @package Wordlift
 */
class Wordlift_Publisher_Ajax_Adapter {

	/**
	 * The {@link Wordlift_Publisher_Service} instance.
	 *
	 * @since  3.11.0
	 * @access private
	 * @var Wordlift_Publisher_Service $publisher_service The {@link Wordlift_Publisher_Service} instance.
	 */
	private $publisher_service;

	/**
	 * Create a {@link Wordlift_Publisher_Ajax_Adapter} instance.
	 *
	 * @param \Wordlift_Publisher_Service $publisher_service The {@link Wordlift_Publisher_Service} instance.
	 *
	 * @since 3.11.0
	 */
	public function __construct( $publisher_service ) {

		$this->publisher_service = $publisher_service;

	}

	/**
	 * The publisher AJAX action. This function is hook to the `wl_publisher`
	 * action.
	 *
	 * @since 3.11.0
	 */
	public function publisher() {

		// Ensure we don't have garbage before us.
		ob_clean();

		// Check if the current user can `manage_options`.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Access denied.' );
		}

		// No actual search parameter was passed, bail out.
		if ( ! isset( $_POST['q'] ) || empty( $_POST['q'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing
			wp_send_json_error( 'The q parameter is required.' );
		}

		// Get the response.
		$response = $this->publisher_service->query( sanitize_text_field( wp_unslash( (string) $_POST['q'] ) ) ); //phpcs:ignore WordPress.Security.NonceVerification.Missing

		// Finally output the response.
		wp_send_json_success( $response );

	}

}
