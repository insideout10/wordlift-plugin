<?php

namespace Wordlift\Videoobject\Parser;

use Wordlift\Videoobject\Data\Embedded_Video\Embedded_Video;
use Wordlift\Videoobject\Data\Embedded_Video\Embedded_Video_Factory;

/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Block_Editor_Parser implements Parser {

	public function get_videos( $post_id ) {
		$post         = get_post( $post_id );
		$content      = $post->post_content;
		$video_blocks = array_filter( parse_blocks( $content ), array( $this, 'filter_blocks' ) );
		return array_map( array( $this, 'block_to_video' ), $video_blocks );
	}

	/**
	 * @param $block
	 *
	 * @return Embedded_Video
	 */
	public function block_to_video( $block ) {
		return Embedded_Video_Factory::get_embedded_video( $block['attrs']['url'] );
	}

	public function filter_blocks( $block ) {
		return array_key_exists( 'blockName', $block )
			   && ( 'core/embed' === $block['blockName'] || 'core-embed/youtube' === $block['blockName']
					|| 'core-embed/vimeo' === $block['blockName'] )
			   // Check if attributes present
			   && array_key_exists( 'attrs', $block )
			   && is_array( $block['attrs'] )
			   // check if valid url present.
			   && array_key_exists( 'url', $block['attrs'] )
			   && is_string( $block['attrs']['url'] );
	}

}
