<?php

namespace Wordlift\Modules\Common\Api;

class Cursor {

	const EMPTY_CURSOR = array(
		'position'  => null,
		'element'   => 'INCLUDED',
		'direction' => 'ASCENDING',
		'limit'     => 20,
		'sort'      => '+id',
		'query'     => array(),
	);

	/**
	 * This is an encoded representation of `EMPTY_CURSOR`.
	 */
	const EMPTY_CURSOR_AS_BASE64_STRING = 'eyJwb3NpdGlvbiI6IG51bGwsImVsZW1lbnQiOiAiSU5DTFVERUQiLCJkaXJlY3Rpb24iOiAiQVNDRU5ESU5HIiwibGltaXQiOiAyMCwic29ydCI6ICIraWQiLCJxdWVyeSI6IFtdfQ==';

	public static function rest_sanitize_request_arg( $value ) {
		// `base64_encode` used to push the cursor to the query string.
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		return json_decode( base64_decode( $value ), true );
	}

}
