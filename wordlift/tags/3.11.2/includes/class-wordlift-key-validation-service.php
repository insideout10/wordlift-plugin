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
	 * Validate the provided key.
	 *
	 * @since 3.9.0
	 *
	 * @param string $key WordLift's key to validate.
	 *
	 * @return bool True if the key is valid, otherwise false.
	 */
	public function is_valid( $key ) {

		// Request the dataset URI as a way to validate the key
		$response = wp_remote_get( wl_configuration_get_accounts_by_key_dataset_uri( $key ), unserialize( WL_REDLINK_API_HTTP_OPTIONS ) );

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
