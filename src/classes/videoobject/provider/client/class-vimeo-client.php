<?php

namespace Wordlift\Videoobject\Provider\Client;

use Wordlift\Common\Singleton;

/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class acts as api client for vimeo.
 */
class Vimeo_Client extends Singleton implements Client {

	public static $requests_sent = 0;

	const VIMEO_URL_REGEX = '/https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/';

	public function get_data( $video_urls ) {

		$vimeo_ids = $this->get_video_ids( $video_urls );

		$vimeo_api_ids = array_map(
			function ( $item ) {
				return '/videos/' . $item;
			},
			$vimeo_ids
		);

		$ids = join( ',', $vimeo_api_ids );

		if ( ! $ids ) {
			return array();
		}

		$api_url = $this->get_api_url() . '/videos/';
		$api_url = add_query_arg(
			array(
				'uris'   => $ids,
				'fields' => 'name,description,link,uri,duration,release_time,pictures,stats',
			),
			$api_url
		);

		$response = wp_remote_get(
			$api_url,
			array(
				'headers' => array(
					'Authorization' => 'bearer ' . $this->get_api_key(),
				),
			)
		);

		++ self::$requests_sent;

		return wp_remote_retrieve_body( $response );
	}

	public static function get_api_key() {
		return get_option( self::get_api_key_option_name(), false );
	}

	public static function get_api_key_option_name() {
		return '_wl_videoobject_vimeo_api_key';
	}

	public function get_api_url() {
		return 'https://api.vimeo.com';
	}

	public function get_video_ids( $video_urls ) {

		$that = $this;

		return array_filter(
			array_map(
				function ( $video_url ) use ( $that ) {
					if ( ! $video_url ) {
						  return false;
					}
					preg_match( $that::VIMEO_URL_REGEX, $video_url, $matches );

					if ( ! array_key_exists( 3, $matches ) ) {
						return false;
					}

					return $matches[3];

				},
				$video_urls
			)
		);
	}
}
