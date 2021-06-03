<?php

namespace Wordlift\Configuration;

class Config {
	/**
	 * @var \Wordlift_Admin_Setup
	 */
	private $admin_setup;

	/**
	 * Config constructor.
	 *
	 * @param $admin_setup \Wordlift_Admin_Setup
	 */
	public function __construct( $admin_setup ) {

		$this->admin_setup = $admin_setup;
		add_action( 'wp_ajax_nopriv_wl_config_plugin', array( $this, 'config' ) );

	}

	/**
	 * Raw byte array from image
	 *
	 * @param $image_string
	 *
	 * Returns png or jpeg based on header, or else returns false.
	 *
	 * @return bool|int|string
	 */
	public function get_mime_type_from_string( $image_string ) {
		$mime_types = array(
			'jpeg' => "\xFF\xD8\xFF",
			'png'  => "\x89\x50\x4e\x47\x0d\x0a\x1a\x0a"
		);

		foreach ( $mime_types as $mime_type => $byte_value ) {
			if ( ! $byte_value === substr( $image_string, 0, strlen( $byte_value ) ) ) {
				continue;
			}

			return $mime_type;
		}

		return false;
	}

	public function config() {

		/**
		 * todo:
		 * 1. check auth
		 * 2. check image mime type error
		 */

		$image_string = (string) $_POST['image'];

		$image_decoded_string = base64_decode( $image_string );

		$upload_dir = wp_upload_dir();

		$mime_type = $this->get_mime_type_from_string( $image_decoded_string );

		if ( ! $mime_type ) {
			wp_send_json_error( "Image type not valid" );
		}

		$file_path = $upload_dir['path'] . DIRECTORY_SEPARATOR . md5( $image_string ) . "." . $mime_type;

		file_put_contents( $file_path, $image_decoded_string );

		$attachment_id = wp_insert_attachment( array(), $file_path );

		$params = array(
			'send_diagnostic' => $_POST['diagnostic'],
			'key'             => $_POST['license'],
			'vocabulary'      => $_POST['vocabulary'],
			'language'        => $_POST['language'],
			'name'            => $_POST['publisherName'],
			'user_type'       => $_POST['publisher'],
			'logo'            => $attachment_id
		);

		$this->admin_setup->save_configuration( $params );


	}

}