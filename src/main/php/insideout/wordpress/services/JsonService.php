<?php

/**
 * Provides helper functions to publish contents via JSON.
 */
class JsonService {

	/**
	 * Sends the content to the browser, json-encoded.
	 * @param array|mixed $content The structured content to send back as JSON.
	 * @param boolean $enableCompression If true, enables compression of output.
	 */
	public static function sendResponse(&$content, $enableCompression = true) {

		/************************************************************
		 * HTTP Response starts here.								*
		************************************************************/
		header('content-type: application/json; charset=utf-8');
		// header("access-control-allow-origin: *");

// 		// Turn on output buffering with the gzhandler
// 		// http://www.geekality.net/2011/10/31/php-simple-compression-of-json-data/
 		if (true === $enableCompression)
 			ob_start('ob_gzhandler');

		// create a JSONO representation of the result.
		
		$json = json_encode($content);

		# JSON if no callback
		if( ! isset($_GET['callback']))
			exit($json);

		# JSONP if valid callback
		if(self::isValidCallback($_GET['callback']))
			exit("{$_GET['callback']}($json)");

		# Otherwise, bad request
		header('status: 400 Bad Request', true, 400);

	}

	/**
	 * Check if a valid callback function (JSONP) has been provided.
	 * @param string $subject
	 * @return boolean
	 */
	private static function isValidCallback($subject) {

		/**
		 * Handy stuff to check that the callback is valid: see
		 * http://www.geekality.net/2010/06/27/php-how-to-easily-provide-json-and-jsonp/
		 */

		$identifierSyntax = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';

		$reservedWords = array('break', 'do', 'instanceof', 'typeof', 'case',
				'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue',
				'for', 'switch', 'while', 'debugger', 'function', 'this', 'with',
				'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum',
				'extends', 'super', 'const', 'export', 'import', 'implements', 'let',
				'private', 'public', 'yield', 'interface', 'package', 'protected',
				'static', 'null', 'true', 'false');

		return preg_match($identifierSyntax, $subject)
		&& ! in_array(mb_strtolower($subject, 'UTF-8'), $reservedWords);
	}

}

?>