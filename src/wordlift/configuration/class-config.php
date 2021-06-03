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

	public function config() {

		$image_string = (string) $_POST['image'];

		$image_decoded_string = base64_decode( $image_string );

		$attachment_id = 0;

		$params = array(
			'send_diagnostic' => $_POST['diagnostic'],
			'key'             => $_POST['license'],
			'vocabulary'      => $_POST['vocabulary'],
			'language'        => $_POST['language'],
			'name'            => $_POST['publisherName'],
			'user_type'       => $_POST['publisher'],
			'logo'            => $attachment_id
		);


	}

}