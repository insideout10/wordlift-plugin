<?php

namespace Wordlift\Videoobject\Parser;

/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Parser_Factory {

	const BLOCK_EDITOR = 'block-editor';

	const CLASSIC_EDITOR = 'classic-editor';

	/**
	 * @param $parser_config
	 *
	 * @return Parser
	 */
	public static function get_parser( $parser_config ) {
		if ( self::BLOCK_EDITOR === $parser_config ) {
			return new Block_Editor_Parser();
		} elseif ( self::CLASSIC_EDITOR === $parser_config ) {
			return new Classic_Editor_Parser();
		}

	}

	public static function get_parser_from_content( $post_content ) {
		if ( function_exists( 'has_blocks' )
			 && function_exists( 'parse_blocks' ) && has_blocks( $post_content ) ) {
			return self::get_parser( self::BLOCK_EDITOR );
		} else {
			return self::get_parser( self::CLASSIC_EDITOR );
		}
	}

}
