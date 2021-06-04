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


	public function config() {

		$account_info = $this->key_validation_service->get_account_info( (string) $_POST['license'] );

		/**
		 * we need to check if the key is not associated with any account
		 * before setting it, we should check if the url is null.
		 */
		if ( is_wp_error( $account_info )
		     || wp_remote_retrieve_response_code( $account_info ) !== 200 ) {
			return;
		}

		$account_info_json = $account_info['body'];

		$account_info_data = json_decode( $account_info_json, true );

		if ( ! $account_info_data ) {
			// Invalid json returned by api.
			return;
		}

		if ( $account_info_data['url'] !== null ) {
			// key already associated with another account.
			return;
		}

		$image_string = (string) $_POST['image'];

		$image_decoded_string = base64_decode( $image_string );

		$upload_dir = wp_upload_dir();

		$image_ext = (string) $_POST['imageExtension'];

		$file_path = $upload_dir['path'] . DIRECTORY_SEPARATOR . md5( $image_string ) . "." . $image_ext;

		file_put_contents( $file_path, $image_decoded_string );

		$attachment_id = wp_insert_attachment( array(), $file_path );

		$params = array(
			'share-diagnostic' => $_POST['diagnostic'],
			'key'              => $_POST['license'],
			'vocabulary'       => $_POST['vocabulary'],
			'wl-site-language' => $_POST['language'],
			'wl-country-code'  => $_POST['country'],
			'name'             => $_POST['publisherName'],
			'user_type'        => $_POST['publisher'],
			'logo'             => $attachment_id
		);

		$this->admin_setup->save_configuration( $params );


	}

}