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

		if ( preg_match( '#(^|\s|>)https?://#i', $content ) ) {
			// Find URLs on their own line.
			preg_match_all( '|^(\s*)(https?://[^\s<>"]+)(\s*)$|im', $content, $line_matches );
			// Find URLs in their own paragraph.
			preg_match_all( '|(<p(?: [^>]*)?>\s*)(https?://[^\s<>"]+)(\s*<\/p>)|i', $content, $paragraph_matches );
		}

		$matches = array_map( array( $this, 'get_url_from_match' ), array_merge( $line_matches, $paragraph_matches ) );

		$matches = array_values( array_unique( array_filter( $matches ) ) );

		return array_map( array( $this, 'url_to_embedded_video_object' ), $matches );
	}


	/**
	 * @param $url
	 *
	 * @return Embedded_Video
	 */
	private static function url_to_embedded_video_object( $url ) {
		return Embedded_Video_Factory::get_embedded_video( $url );
	}

	public static function get_url_from_match( $match ) {
		return array_key_exists( 0, $match ) ? $match[0] : false;
	}

}