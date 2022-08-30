<?php
/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Videoobject\Provider;

use Wordlift\Videoobject\Data\Video\Video;

/**
 * Class Vimeo
 *
 * @package Wordlift\Videoobject\Provider
 */
class Vimeo extends Api_Provider {

	const API_FIELD_NAME = '_wl_videoobject_vimeo_api_key';

	public static function get_api_key() {
		return get_option( self::API_FIELD_NAME, false );
	}

	public function get_videos_data( $videos ) {

		$key = $this->get_api_key();

		if ( ! $key ) {
			return array();
		}

		$urls = array_map(
			function ( $video ) {
				return $video->get_url();
			},
			$videos
		);

		$response_body = $this->api_client->get_data( $urls );

		if ( ! is_string( $response_body ) || ! $response_body ) {
			return array();
		}

		$response = json_decode( $response_body, true );

		$video_list = $response['data'];

		if ( ! is_array( $video_list ) ) {
			// Return if we cant parse the response.
			return array();
		}

		return array_filter( array_map( array( $this, 'get_video_from_video_data' ), $video_list ) );

	}

	public function get_video_from_video_data( $vimeo_video_data ) {

		if ( ! $vimeo_video_data ) {
			// If valid data not supplied dont init the object.
			return false;
		}
		$video              = new Video();
		$video->name        = $vimeo_video_data['name'];
		$video->description = $vimeo_video_data['description'];

		$video->content_url = $vimeo_video_data['link'];
		$video->embed_url   = 'https://player.vimeo.com/video/' . $this->get_id( $vimeo_video_data );
		if ( is_numeric( $vimeo_video_data['duration'] ) ) {
			$video->duration = 'PT' . $vimeo_video_data['duration'] . 'S';
		}
		$video->upload_date    = $vimeo_video_data['release_time'];
		$video->thumbnail_urls = $this->set_thumbnail_urls( $vimeo_video_data );
		$video->id             = $video->content_url;

		if ( array_key_exists( 'stats', $vimeo_video_data )
			 && array_key_exists( 'plays', $vimeo_video_data['stats'] ) ) {
			$video->views = (int) $vimeo_video_data['stats']['plays'];
		}

		return $video;
	}

	private function get_id( $api_response_data ) {
		return str_replace( '/videos/', '', $api_response_data['uri'] );
	}

	private function set_thumbnail_urls( $api_response_data ) {

		if ( ! array_key_exists( 'pictures', $api_response_data ) || ! array_key_exists(
			'sizes',
			$api_response_data['pictures']
		) ) {
			return array();
		}
		if ( ! is_array( $api_response_data['pictures']['sizes'] ) ) {
			return array();
		}
		$pictures = $api_response_data['pictures']['sizes'];

		return array_map(
			function ( $picture_data ) {
				return $picture_data['link'];
			},
			$pictures
		);

	}

}
