<?php

namespace Wordlift\Videoobject\Provider\Client;

use Wordlift\Common\Singleton;

/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class acts an api client for youtube.
 */
class Youtube_Client extends Singleton implements Client {

	const YOUTUBE_REGEX = '/(?:https?:\/\/)?(?:youtu\.be\/|(?:www\.|m\.)?youtube\.com\/(?:watch|v|embed)(?:\.php)?(?:\?.*v=|\/))([a-zA-Z0-9\_-]+)/m';

	public static $requests_sent = 0;

	public static function get_api_key_option_name() {
		return '__wl_video_object_youtube_api_key';
	}

	public static function get_api_key() {
		return get_option( self::get_api_key_option_name(), false );
	}

	public function get_api_url() {
		return 'https://www.googleapis.com/youtube/v3/videos';
	}

	public function get_data( $video_urls ) {
		$video_ids = $this->get_video_ids_as_string( $video_urls );
		$url       = add_query_arg(
			array(
				'part' => 'snippet,contentDetails,statistics,liveStreamingDetails',
				'id'   => $video_ids,
				'key'  => $this->get_api_key(),
			),
			$this->get_api_url()
		);

		$response = wp_remote_get( $url );

		++ self::$requests_sent;

		return wp_remote_retrieve_body( $response );
	}

	private function get_video_ids_as_string( $video_urls ) {
		// validate the urls.
		$video_urls = array_filter(
			$video_urls,
			function ( $url ) {
				return filter_var( $url, FILTER_VALIDATE_URL );
			}
		);

		// extract the video ids.
		return join( ',', $this->get_video_ids( $video_urls ) );
	}

	public function get_video_ids( $video_urls ) {

		$regex = self::YOUTUBE_REGEX;

		$video_ids = array_map(
			function ( $url ) use ( $regex ) {
				$matches = array();
				preg_match( $regex, $url, $matches );

				// Return video id or return false.
				if ( isset( $matches[1] ) && is_string( $matches[1] ) ) {
					  return $matches[1];
				}

				return false;

			},
			$video_urls
		);

		return array_values( array_filter( $video_ids ) );

	}

}
