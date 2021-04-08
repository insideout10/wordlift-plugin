<?php

namespace Wordlift_Videoobject\Vimeo;

use Wordlift_Videoobject\Singleton;

/**
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Video_Url_Service extends Singleton {

	const VIMEO_URL_REGEX = '/https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/';


	/**
	 * @param $vimeo_urls
	 * @param $post_id
	 *
	 * @return string[]
	 */
	public function get_video_ids_for_api( $vimeo_urls ) {

		$vimeo_ids = array_map( array( $this, 'vimeo_url_to_id' ), $vimeo_urls );

		return $vimeo_ids;
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

	/**
	 * @return array|string[]
	 */
	public function get_vimeo_video_urls( $post_id ) {
		$attachments = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_mime_type' => 'oembed/vimeo',
				'post_parent'    => $post_id,
				'numberposts'    => - 1
			)
		);

		$vimeo_urls = array_map( function ( $attachment ) {
			return $attachment->guid;
		},
			$attachments );

		$vimeo_urls = array_unique( $vimeo_urls );

		return $vimeo_urls;
	}

}
