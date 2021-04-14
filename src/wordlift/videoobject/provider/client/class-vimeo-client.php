<?php

namespace Wordlift\Videoobject\Provider\Client;
use Wordlift\Common\Singleton;

/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class acts as api client for vimeo.
 */
class Vimeo_Client extends Singleton implements Client {
	const VIMEO_URL_REGEX = '/https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/';

	/**
	 * @param $url string Vimeo url
	 */
	public function vimeo_url_to_id( $url ) {
		if ( ! $url ) {
			return false;
		}
		preg_match( self::VIMEO_URL_REGEX, $url, $matches );

		if ( ! array_key_exists( 3, $matches ) ) {
			return false;
		}

		return '/videos/' . $matches[3];
	}
	/**
	 * @param $vimeo_urls
	 * @param $post_id
	 *
	 * @return string[]
	 */
	public function get_video_ids_for_api( $vimeo_urls ) {

		return array_filter( array_map( array( $this, 'vimeo_url_to_id' ), $vimeo_urls ) );
	}


	public function get_data( $video_urls ) {
		$ids = join( ",", $this->get_video_ids_for_api( $video_urls ) );

		if ( ! $ids ) {
			return array();
		}

		$api_url = $this->get_api_url() . "/videos/";
		$api_url = add_query_arg( array(
			'uris'   => $ids,
			'fields' => 'name,description,link,uri,duration,release_time,pictures'
		), $api_url );


		$response = wp_remote_get( $api_url, array(
			'headers' => array(
				'Authorization' => 'bearer ' . $this->get_api_key()
			)
		) );

		return wp_remote_retrieve_body( $response );
	}

	public function get_api_key() {
		return get_option( $this->get_api_key_option_name(), false );
	}

	public function get_api_key_option_name() {
		return '_wl_videoobject_vimeo_api_key';
	}

	public function get_api_url() {
		return 'https://api.vimeo.com';
	}
}