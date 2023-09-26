<?php

namespace Wordlift\Videoobject\Parser;

use Wordlift\Videoobject\Data\Embedded_Video\Embedded_Video;
use Wordlift\Videoobject\Data\Embedded_Video\Embedded_Video_Factory;

class Classic_Editor_Parser implements Parser {

	public function get_videos( $post_id ) {

		$post = get_post( $post_id );

		$content = $post->post_content;

		$line_matches      = array();
		$paragraph_matches = array();

		/**
		 * This replicates the wp autoembed function, instead of replacing we capture
		 * all the urls.
		 */
		if ( preg_match( '#(^|\s|>)https?://#i', $content ) ) {
			// Find URLs on their own line.
			preg_match_all( '|^(\s*)(https?://[^\s<>"]+)(\s*)$|im', $content, $line_matches, PREG_SET_ORDER );
			// Find URLs in their own paragraph.
			preg_match_all( '|(<p(?: [^>]*)?>\s*)(https?://[^\s<>"]+)(\s*<\/p>)|i', $content, $paragraph_matches, PREG_SET_ORDER );
		}

		$regex_matches = array_merge( $line_matches, $paragraph_matches );
		$matches       = array_map( array( $this, 'get_url_from_match' ), $regex_matches );

		$matches = array_values( array_unique( array_filter( $matches ) ) );

		return array_map( array( $this, 'url_to_embedded_video_object' ), $matches );
	}

	/**
	 * @param $url
	 *
	 * @return Embedded_Video
	 */
	private static function url_to_embedded_video_object( $url ) {
		return Embedded_Video_Factory::get_embedded_video( trim( $url ) );
	}

	public static function get_url_from_match( $match ) {
		return array_key_exists( 0, $match ) ? $match[0] : false;
	}

}
