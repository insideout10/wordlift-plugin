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

	public function get_video_ids( $video_urls ) {

		return array_filter( $video_urls, function ( $item ) {
			return strpos( $item, 'https://cdn.jwplayer.com/v2/media/', 0 );
		} );

	}

	public static function get_api_key() {
		// Method not implemented, since we dont need api key
	}

	public static function get_api_key_option_name() {
		// Method not implemented, since we dont need api key
	}

	public function get_api_url() {
		// Method not implemented, since we dont need api key
	}


}