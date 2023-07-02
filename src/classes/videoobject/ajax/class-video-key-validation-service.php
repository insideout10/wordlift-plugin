<?php
namespace Wordlift\Videoobject\Ajax;

/**
 * Video_Key_Validation_Service class
 *
 * The {@link Video_Key_Validation_Service} class provides WordLift Video Settings key validation services.
 *
 * @link    https://wordlift.io
 *
 * @package Wordlift
 * @since   3.40.2
 * @author Mahbub Hasan Imon <mahbub@wordlift.io>
 */

/**
 * Define the {@link Video_Key_Validation_Service} class.
 *
 * @since 3.40.2
 */
class Video_Key_Validation_Service {

	public function __construct() {
		// create ajax request to handle youtube and vimeo api key validation.
		add_action( 'wp_ajax_wl_validate_video_api_key', array( $this, 'validate_video_api_key' ) );
	}

	/**
	 * Validate video api key.
	 *
	 * @since 3.40.2
	 */
	public function validate_video_api_key() {
		// check nonce.
		check_ajax_referer( 'wl_video_api_nonce' );

		// Check if we have an API key and Type.
		if ( ! isset( $_POST['api_key'] ) || empty( $_POST['api_key'] ) || ! isset( $_POST['type'] ) ) {
			wp_send_json_error(
				array(
					'valid' => false,
				)
			);
		}

		$api_key = sanitize_text_field( wp_unslash( $_POST['api_key'] ) );
		$type    = sanitize_text_field( wp_unslash( $_POST['type'] ) );

		// Check if we have a valid type.
		if ( ! in_array( $type, array( 'youtube', 'vimeo' ), true ) ) {
			wp_send_json_error(
				array(
					'valid' => false,
				)
			);
		}

		if ( 'youtube' === $type ) {
			$this->validate_youtube_api_key( $api_key );
		} else {
			$this->validate_vimeo_api_key( $api_key );
		}
	}

	/**
	 * Validate youtube api key.
	 *
	 * @param string $api_key Youtube api key.
	 *
	 * @return void
	 */
	private function validate_youtube_api_key( $api_key ) {
		$url = add_query_arg(
			array(
				'part' => 'snippet',
				'q'    => 'wordlift',
				'type' => 'video',
				'key'  => $api_key,
			),
			'https://www.googleapis.com/youtube/v3/search'
		);

		$response = wp_remote_get( $url );

		// Get response code.
		$response_code = wp_remote_retrieve_response_code( $response );

		if ( 200 === $response_code ) {
			wp_send_json_success(
				array(
					'valid' => true,
				)
			);
		} else {
			wp_send_json_error(
				array(
					'valid' => false,
				)
			);
		}
	}

	/**
	 * Validate vimeo api key.
	 *
	 * @param string $api_key Vimeo api key.
	 *
	 * @return void
	 */
	private function validate_vimeo_api_key( $api_key ) {
		$url = add_query_arg(
			array(
				'query'    => 'wordlift',
				'page'     => 1,
				'per_page' => 1,
			),
			'https://api.vimeo.com/videos'
		);

		$response = wp_remote_get(
			$url,
			array(
				'headers' => array(
					'Authorization' => 'bearer ' . $api_key,
				),
			)
		);

		// Get response code.
		$response_code = wp_remote_retrieve_response_code( $response );

		if ( 200 === $response_code ) {
			wp_send_json_success(
				array(
					'valid' => true,
				)
			);
		} else {
			wp_send_json_error(
				array(
					'valid' => false,
				)
			);
		}
	}
}
