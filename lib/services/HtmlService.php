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
		if (false === is_string($content))
			return $content;

		return htmlentities( $content, ENT_QUOTES , 'UTF-8' );
	}

	/**
	 * Creates an Html fragment for an image. This method does not Html-encode the parameters, they must be Html encoded before.
	 * @param string $url
	 */
	public static function getImageFragment($url) {
		return '<img src="'.$url.'" />';
	}

	/**
	 * Creates a link with the provided Url and content. This method does not Html-encode the parameters, they must be Html encoded before.
	 * @param string $url
	 * @param string $content
	 * @return string
	 */
	public static function getLinkFragment($url, $content) {
		return '<a href="'.$url.'">'.$content.'</a>';
	}
}

?>