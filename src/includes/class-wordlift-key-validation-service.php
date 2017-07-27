<?php
/**
 * Wordlift_Key_Validation_Service class
 *
 * The {@link Wordlift_Key_Validation_Service} class provides WordLift's key validation services.
 *
 * @link    https://wordlift.io
 *
 * @package Wordlift
 * @since   3.9.0
 */

/**
 * Define the {@link Wordlift_Key_Validation_Service} class.
 *
 * @since 3.9.0
 */
class Wordlift_Key_Validation_Service {

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

	/**
	 * Create a {@link Wordlift_Key_Validation_Service} instance.
	 *
	 * @since 3.14.0
	 *
	 * @param \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	public function __construct( $configuration_service ) {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Key_Validation_Service' );

		$this->configuration_service = $configuration_service;

	}

	/**
	 * Validate the provided key.
	 *
	 * @since 3.9.0
	 *
	 * @param string $key WordLift's key to validate.
	 *
	 * @return bool True if the key is valid, otherwise false.
	 */
	public function is_valid( $key ) {

		$this->log->debug( 'Validating key...' );

		// Request the dataset URI as a way to validate the key
		$response = wp_remote_get( $this->configuration_service->get_accounts_by_key_dataset_uri( $key ), unserialize( WL_REDLINK_API_HTTP_OPTIONS ) );

		// If the response is valid, the key is valid.
		return ! is_wp_error( $response ) && 200 === (int) $response['response']['code'];
	}

	/**
	 * This function is hooked to the `wl_validate_key` AJAX call.
	 *
	 * @since 3.9.0
	 */
	public function validate_key() {

		// Ensure we don't have garbage before us.
		ob_clean();

		// Check if we have a key.
		if ( ! isset( $_POST['key'] ) ) {
			wp_send_json_error( 'The key parameter is required.' );
		}

		// Set a response with valid set to true or false according to the key validity.
		wp_send_json_success( array( 'valid' => $this->is_valid( $_POST['key'] ) ) );

	}

}
