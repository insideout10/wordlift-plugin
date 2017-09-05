<?php

/**
 */
class Wordlift_Sanitizer {

	/**
	 * Only accept URIs
	 */
	public static function sanitize_url( $value ) {

		// Initially this function used also filter_var( $value, FILTER_VALIDATE_URL )
		// but URLs with UTF-8 characters are not valid. We store those anyway in the DB as it's up to the browser
		// to do proper url encoding when requesting the URL.
		//
		// see also http://stackoverflow.com/questions/2137080/php-filter-var-filter-validate-url

		// Instead of the above do the minimal check that the value starts with http(s):// .

		if ( ! is_null( $value ) && '' != $value ) {
			$value = trim( $value );
			if ( ( 0 === strpos( $value, 'http://' ) ) ||
				 ( 0 === strpos( $value, 'https://' ) ) ) {
				return $value;
			} else {
				return null;
			}
		}

		return null;
	}

}
