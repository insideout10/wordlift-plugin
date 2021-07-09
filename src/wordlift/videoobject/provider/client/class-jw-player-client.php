<?php

namespace Wordlift\Videoobject\Provider\Client;

use Wordlift\Common\Singleton;

/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class acts as api client for JW Player.
 */

class Jw_Player_Client extends Singleton implements Client {

	public function get_data( $video_urls ) {

	}

	public static function get_api_key() {
		// TODO: Implement get_api_key() method.
	}

	public static function get_api_key_option_name() {
		// TODO: Implement get_api_key_option_name() method.
	}

	public function get_api_url() {
		// TODO: Implement get_api_url() method.
	}

	public function get_video_ids( $video_urls ) {
		// TODO: Implement get_video_ids() method.
	}
}