<?php
/**
 * This class hooks in to `wl_videoobject_embedded_videos` filters and add the urls from embed
 * shortcodes.
 *
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Videoobject\Filters;

use Wordlift\Videoobject\Data\Embedded_Video\Default_Embedded_Video;

class Embed_Shortcode_Capture {

	public function init() {
		add_filter( 'wl_videoobject_embedded_videos', array( $this, 'wl_videoobject_embedded_videos' ), 10, 2 );
	}

	public function wl_videoobject_embedded_videos( $embedded_videos, $post_id ) {

		$post         = get_post( $post_id );
		$post_content = $post->post_content;
		$embed_regex  = get_shortcode_regex( array( 'embed' ) );

		$matches = array();

		preg_match_all( '/' . $embed_regex . '/', $post_content, $matches, PREG_SET_ORDER );

		// The url is returned in index 5
		$embed_shortcode_urls = array_filter(
			array_map(
				function ( $item ) {
					if ( isset( $item[5] ) && is_string( $item[5] ) && $item[5] ) {
						  return $item[5];
					}
					return false;
				},
				$matches
			)
		);

		$embed_shortcode_videos = array_map(
			function ( $url ) {
				return new Default_Embedded_Video( $url );
			},
			$embed_shortcode_urls
		);

		return array_merge( $embedded_videos, $embed_shortcode_videos );

	}

}

