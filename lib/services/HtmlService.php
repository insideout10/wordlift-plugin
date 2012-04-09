<?php

/**
 * Provides handy functions for Html conversion.
 */
class HtmlService {

	/**
	 * Escapes the content with their Html sequences.
	 * @param string $content
	 */
	public static function htmlEncode($content) {
		return htmlentities( $content, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	}
	
}

?>