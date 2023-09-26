<?php

class Wordlift_Sanitizer {

	/**
	 * Only accept URIs
	 *
	 * @param string $value
	 *
	 * @return null
	 */
	public static function sanitize_url( $value ) {

		// Initially this function used also filter_var( $value, FILTER_VALIDATE_URL )
		// but URLs with UTF-8 characters are not valid. We store those anyway in the DB as it's up to the browser
		// to do proper url encoding when requesting the URL.
		//
		// see also http://stackoverflow.com/questions/2137080/php-filter-var-filter-validate-url

		if ( ! is_string( $value ) ) {
			return null;
		}

		$trimmed_value = trim( $value );
		if ( '' !== $trimmed_value ) {
			return $trimmed_value;
		}

		return null;
	}

}
