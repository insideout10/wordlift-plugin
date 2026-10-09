<?php
/**
 * Ajax Adapters: Sample Data Ajax Adapter.
 *
 * Provides end-points to call the {@link Wordlift_Sample_Data_Service} instance,
 * thus enabling loading sample data with a http call.
 *
 * @since   3.12.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Sample_Data_Ajax_Adapter} class.
 *
 * @since   3.12.0
 * @package Wordlift
 */
class Wordlift_Sample_Data_Ajax_Adapter {

	/**
	 * The {@link Wordlift_Sample_Data_Service} instance.
	 *
	 * @since  3.12.0
	 * @access private
	 * @var \Wordlift_Sample_Data_Service $sample_data_service The {@link Wordlift_Sample_Data_Service} instance.
	 */
	private $sample_data_service;

	/**
	 * Create a {@link Wordlift_Sample_Data_Ajax_Adapter} instance.
	 *
	 * @since 3.12.0
	 *
	 * @param \Wordlift_Sample_Data_Service $sample_data_service The {@link Wordlift_Sample_Data_Service} instance.
	 */
	public function __construct( $sample_data_service ) {

		$this->sample_data_service = $sample_data_service;
	}

	/**
	 * Handle the `wl_sample_data_create` ajax action.
	 *
	 * @since 3.12.0
	 */
	public function create() {

		// Clean any potential garbage before us.
		ob_clean();

		$this->check_permissions();

		// Create the sample data.
		$this->sample_data_service->create();

		// Send success.
		wp_send_json_success();
	}

	public function delete() {

		// Clean any potential garbage before us.
		ob_clean();

		$this->check_permissions();

		// Create the sample data.
		$this->sample_data_service->delete();

		// Send success.
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		@header( 'Content-Disposition: inline' );
		wp_send_json_success();
	}

	/**
	 * Bail out unless the request carries a valid `wl_sample_data` nonce and the user can manage options.
	 *
	 * @since 3.54.16
	 */
	private function check_permissions() {

		check_ajax_referer( 'wl_sample_data' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( null, 403 );
		}
	}
}
