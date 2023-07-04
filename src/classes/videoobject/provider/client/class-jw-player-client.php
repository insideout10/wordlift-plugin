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

		$videos_data = array();

		foreach ( $video_urls as $video_url ) {
			$data = wp_remote_get( $video_url );
			if ( is_wp_error( $data ) ) {
				continue;
			}
			$json_body = wp_remote_retrieve_body( $data );

			$video_data = json_decode( $json_body, true );
			if ( ! $video_data ) {
				continue;
			}
			$video_data['id'] = $video_url;
			$videos_data[]    = $video_data;
		}

		return $videos_data;

	}

	public function get_video_ids( $video_urls ) {

		return array_filter(
			$video_urls,
			function ( $item ) {
				return strpos( $item, 'https://cdn.jwplayer.com/v2/media/', 0 ) !== false;
			}
		);

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
