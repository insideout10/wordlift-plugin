<?php

namespace Wordlift\Videoobject\Provider\Client;

use Wordlift\Common\Singleton;

/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class acts an api client for youtube.
 */
class Youtube_Client extends Singleton implements Client {


	public function get_api_key_option_name() {
		return "__wl_video_object_youtube_api_key";
	}

	public function get_api_key() {
		return get_option( $this->get_api_key_option_name(), false );
	}

	public function get_api_url() {
		return 'https://www.googleapis.com/youtube/v3/videos';
	}


	public function get_data( $video_urls ) {
		$video_ids = $this->get_video_ids_as_string( $video_urls );
		$url       = add_query_arg( array(
			'part' => 'snippet,contentDetails,statistics',
			'id'   => $video_ids,
			'key'  => $this->get_api_key()
		), $this->get_api_url() );

		$response = wp_remote_get( $url );

		return wp_remote_retrieve_body( $response );
	}


	private function get_video_ids_as_string( $video_urls ) {
		// validate the urls.
		$video_urls = array_filter( $video_urls, function ( $url ) {
			return filter_var( $url, FILTER_VALIDATE_URL );
		} );

		// extract the video ids.
		return join( ",", self::get_video_ids( $video_urls ) );
	}


	private static function get_video_ids( $video_urls ) {

		$video_ids = array_map( function ( $url ) {

			$parsed_url_data = parse_url( $url );

			if ( ( ! is_array( $parsed_url_data ) || ! array_key_exists( 'query', $parsed_url_data ) )
			     && strpos( $url, "youtu.be" ) === false ) {
				return false;
			}
			// For youtu.be urls, the video id is present in path.
			if ( strpos( $url, "youtu.be" ) !== false ) {
				$parsed_url_data['query'] = "v=" . substr( $parsed_url_data['path'], 1 );
			}

			$query_data_result = array();
			parse_str( $parsed_url_data['query'], $query_data_result );

			if ( ! array_key_exists( 'v', $query_data_result ) ) {
				return false;
			}

			return $query_data_result['v'];

		}, $video_urls );

		return array_values( array_filter( $video_ids ) );

	}


}