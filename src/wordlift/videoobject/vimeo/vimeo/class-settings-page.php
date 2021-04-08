<?php
/**
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift_Videoobject\Vimeo;


use Wordlift_Videoobject\Singleton;

class Settings_Page extends Singleton {

	const API_KEY_FIELD_NAME = 'wl_vo_vimeo_api_key';

	const CLIENT_ID_FIELD_NAME = 'wl_vo_vimeo_client_id';

	const CLIENT_SECRET_FIELD_NAME = 'wl_vo_vimeo_client_secret';


	/**
	 * @return Settings_Page
	 */
	public static function get_instance() {
		return parent::get_instance();
	}

	public function add_api_key_field() {
		register_setting( 'writing', self::API_KEY_FIELD_NAME );
		add_settings_field( self::API_KEY_FIELD_NAME,
			'Vimeo API key for VideoObject Markup',
			array( $this, 'print_api_key_field' ),
			'writing' );
	}


	public function print_api_key_field() {
		$placeholder = __( 'Vimeo API key', 'wordlift-videoobject-vimeo' );
		$field_key   = self::API_KEY_FIELD_NAME;
		$value       = esc_html( get_option( $field_key ) );
		$this->print_field( $placeholder, $field_key, $value );
	}

	public function print_client_id_field() {
		$placeholder = __( 'Vimeo Client Id', 'wordlift-videoobject-vimeo' );
		$field_key   = self::CLIENT_ID_FIELD_NAME;
		$value       = esc_html( get_option( $field_key ) );
		$this->print_field( $placeholder, $field_key, $value );
	}

	public function print_client_secret_field() {
		$placeholder = __( 'Vimeo Client Secret', 'wordlift-videoobject-vimeo' );
		$field_key   = self::CLIENT_SECRET_FIELD_NAME;
		$value       = esc_html( get_option( $field_key ) );
		$this->print_field( $placeholder, $field_key, $value );
	}


	public function add_client_id_field() {
		register_setting( 'writing', self::CLIENT_ID_FIELD_NAME );
		add_settings_field( self::CLIENT_ID_FIELD_NAME,
			'Vimeo Client Id for VideoObject Markup',
			array( $this, 'print_client_id_field' ),
			'writing' );
	}

	public function add_client_secret_field() {
		register_setting( 'writing', self::CLIENT_SECRET_FIELD_NAME );
		add_settings_field( self::CLIENT_SECRET_FIELD_NAME,
			'Vimeo Client Secret for VideoObject Markup',
			array( $this, 'print_client_secret_field' ),
			'writing' );
	}

	/**
	 * @param $placeholder
	 * @param $field_key
	 * @param $value
	 */
	private function print_field( $placeholder, $field_key, $value ) {
		echo <<<EOF
<input type='text' placeholder="$placeholder" id="$field_key" name="$field_key" value="$value">
EOF;
	}

	public function add_fields() {
		$this->add_client_id_field();
		$this->add_client_secret_field();
		$this->add_api_key_field();
	}

}
