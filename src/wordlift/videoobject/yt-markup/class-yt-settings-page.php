<?php
/**
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift_Videoobject\Yt_Markup;


class Yt_Settings_Page extends Singleton {

	const API_FIELD_NAME = 'wr-companion-yt-markup-api-key';


	/**
	 * @return Yt_Settings_Page
	 */
	public static function get_instance() {
		return parent::get_instance();
	}

	public function add_api_key_field() {
		register_setting( 'writing', self::API_FIELD_NAME );
		add_settings_field( self::API_FIELD_NAME,
			'Youtube API key for VideoObject Markup',
			array( $this, 'print_yt_api_key_field' ),
			'writing' );
	}


	public function print_yt_api_key_field() {

		$placeholder = __( 'Youtube data API key', 'wordlift-videoobject' );
		$id          = self::API_FIELD_NAME;
		$value       = esc_html( get_option( self::API_FIELD_NAME ) );
		echo <<<EOF
<input type='text' placeholder="$placeholder" id="$id" name="$id" value="$value">
EOF;

	}

}
