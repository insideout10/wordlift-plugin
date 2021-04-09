<?php

namespace Wordlift\Videoobject\Parser;
/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Parser_Factory {

	const BLOCK_EDITOR = 'block-editor';

	const CLASSIC_EDITOR = 'classic-editor';

	public static function get_parser( $parser_config ) {
		if ( self::BLOCK_EDITOR === $parser_config) {
			return new Block_Editor_Parser();
		}
		else if ( self::CLASSIC_EDITOR === $parser_config ) {

		}

	}

}