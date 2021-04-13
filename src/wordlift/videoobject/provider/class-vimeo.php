<?php
/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Videoobject\Provider;

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


	public function get_videos_data( $videos ) {
		$urls = array_map( function ( $video ) {
			return $video->get_url();
		}, $videos );
		$ids  = $this->get_video_ids_for_api( $urls );
		wp_remote_get( add_query_arg( array(
			'uris'   => $ids,
			'fields' => 'name,description,link,uri,duration,release_time,pictures'
		) ) );

	}
}