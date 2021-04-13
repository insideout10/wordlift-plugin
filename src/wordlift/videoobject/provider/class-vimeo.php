<?php
/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Videoobject\Provider;

use Wordlift\Videoobject\Data\Video\Video;

/**
 * Class Vimeo
 * @package Wordlift\Videoobject\Provider
 */
class Vimeo implements Provider {

	const API_URL = 'https://api.vimeo.com';
	const VIMEO_URL_REGEX = '/https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/';
	const API_FIELD_NAME = '_wl_videoobject_vimeo_api_key';


	/**
	 * @param $vimeo_urls
	 * @param $post_id
	 *
	 * @return string[]
	 */
	public function get_video_ids_for_api( $vimeo_urls ) {

		return array_map( array( $this, 'vimeo_url_to_id' ), $vimeo_urls );
	}

	/**
	 * @param $url string Vimeo url
	 */
	public function vimeo_url_to_id( $url ) {
		if ( ! $url ) {
			return false;
		}
		preg_match( self::VIMEO_URL_REGEX, $url, $matches );

		return '/videos/' . $matches[3];
	}

	private function get_api_key() {
		return get_option( self::API_FIELD_NAME, false );
	}

	public function get_videos_data( $videos ) {

		$key = $this->get_api_key();

		if ( ! $key ) {
			return array();
		}

		$urls = array_map( function ( $video ) {
			return $video->get_url();
		}, $videos );

		$ids = $this->get_video_ids_for_api( $urls );

		$api_url = add_query_arg( array(
			'uris'   => $ids,
			'fields' => 'name,description,link,uri,duration,release_time,pictures'
		), self::API_URL );

		$response = wp_remote_get( $api_url, array(
			'headers' => array(
				'bearer ' . $key
			)
		) );

		$response_body = wp_remote_retrieve_body( $response );
		// we need to parse the body.
		if ( ! array_key_exists( 'body', $response ) ||
		     ! array_key_exists( 'data', $response['body'] ) ) {
			return array();
		}

		$video_list = $response['body']['data'];

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
		$video->embed_url   = "https://player.vimeo.com/video/" . $this->get_id( $vimeo_video_data );
		if ( is_numeric( $vimeo_video_data['duration'] ) ) {
			$video->duration = "PT" . $vimeo_video_data['duration'] . "S";
		}
		$video->upload_date    = $vimeo_video_data['release_time'];
		$video->thumbnail_urls = $this->set_thumbnail_urls( $vimeo_video_data );
	}

	private function get_id( $api_response_data ) {
		return str_replace( "/videos/", "", $api_response_data['uri'] );
	}


	private function set_thumbnail_urls( $api_response_data ) {

		if ( ! array_key_exists( 'pictures', $api_response_data ) || ! array_key_exists( 'sizes',
				$api_response_data['pictures'] ) ) {
			return array();
		}
		if ( ! is_array( $api_response_data['pictures']['sizes'] ) ) {
			return array();
		}
		$pictures = $api_response_data['pictures']['sizes'];

		return array_map( function ( $picture_data ) {
			return $picture_data['link'];
		},
			$pictures );

	}


}