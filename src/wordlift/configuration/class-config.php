<?php

namespace Wordlift\Configuration;


class Config {
	/**
	 * @var \Wordlift_Admin_Setup
	 */
	private $admin_setup;
	/**
	 * @var \Wordlift_Key_Validation_Service
	 */
	private $key_validation_service;

	/**
	 * Config constructor.
	 *
	 * @param $admin_setup \Wordlift_Admin_Setup
	 * @param $key_validation_service \Wordlift_Key_Validation_Service
	 */
	public function __construct( $admin_setup, $key_validation_service ) {

		$this->admin_setup            = $admin_setup;
		$this->key_validation_service = $key_validation_service;
		add_action( 'wp_ajax_nopriv_wl_config_plugin', array( $this, 'config' ) );

	}

	/**
	 * Check if the key is valid and also not bound to any domain.
	 *
	 * @param $key string
	 *
	 * @return bool
	 */
	private function is_key_valid_and_not_bound_to_any_domain( $key ) {
		$account_info = $this->key_validation_service->get_account_info( $key );

		/**
		 * we need to check if the key is not associated with any account
		 * before setting it, we should check if the url is null.
		 */
		if ( is_wp_error( $account_info )
		     || wp_remote_retrieve_response_code( $account_info ) !== 200 ) {
			return false;
		}

		$account_info_json = $account_info['body'];

		$account_info_data = json_decode( $account_info_json, true );

		if ( ! $account_info_data ) {
			// Invalid json returned by api.
			return false;
		}

		if ( $account_info_data['url'] !== null ) {
			// key already associated with another account.
			return false;
		}

		return true;
	}


	public function config() {

		// Perform validation check for all the parameters.
		$required_fields = array(
			'diagnostic',
			'vocabulary',
			'language',
			'country',
			'publisherName',
			'publisher',
			'license'
		);

		// validate all the fields before processing
		foreach ( $required_fields as $field ) {
			if ( ! array_key_exists( $field, $_POST ) ) {
				wp_send_json_error( sprintf( __( 'Field %s is required', 'wordlift' ), $field ) );

				return;
			}
		}

		$key = (string) $_POST['license'];

		if ( ! $this->is_key_valid_and_not_bound_to_any_domain( $key ) ) {
			wp_send_json_error( __( 'Key is not valid or associated with other domain', 'wordlift' ) );

			// exit if not valid.
			return;
		}

		$this->admin_setup->save_configuration( $this->get_params() );


	}

	/**
	 *
	 * @return array
	 */
	private function get_params() {

		$attachment_id = $this->may_be_get_attachment_id();

		$params = array(
			'key'              => (string) $_POST['license'],
			'vocabulary'       => (string) $_POST['vocabulary'],
			'wl-site-language' => (string) $_POST['language'],
			'wl-country-code'  => (string) $_POST['country'],
			'name'             => (string) $_POST['publisherName'],
			'user_type'        => (string) $_POST['publisher'],
			'logo'             => $attachment_id
		);

		if ( (bool) $_POST['diagnostic'] ) {
			$params['share-diagnostic'] = 'yes';
		}

		return $params;
	}

	/**
	 * @return int | bool
	 */
	private function may_be_get_attachment_id() {
		// if image or image extension not posted then return false.
		if ( ! isset( $_POST['image'] ) || ! isset( $_POST['imageExtension'] ) ) {
			return false;
		}

		$allowed_extensions = array( 'png', 'jpeg', 'jpg' );
		$image_string       = (string) $_POST['image'];
		$image_ext          = (string) $_POST['imageExtension'];

		if ( ! in_array( $image_ext, $allowed_extensions ) ) {
			return false;
		}

		$image_decoded_string = base64_decode( $image_string );

		$upload_dir = wp_upload_dir();

		$file_path = $upload_dir['path'] . DIRECTORY_SEPARATOR . md5( $image_string ) . "." . $image_ext;

		file_put_contents( $file_path, $image_decoded_string );

		return wp_insert_attachment( array(), $file_path );
	}

}