<?php
/**
 * Wordlift_Languages class
 *
 * This class provides the list of languages supported by WordLift.
 *
 * @link    https://wordlift.io
 *
 * @package Wordlift
 * @since   3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// We need `format_code_lang` defined in the ms.php file. This file only contains function, we can require it once.
require_once( ABSPATH . 'wp-admin/includes/ms.php' );

/**
 * Define the {@link Wordlift_Languages} class.
 *
 * @since 3.9.0
 */
class Wordlift_Languages {

	/**
	 * An array that will contain language codes => language names pairs. It gets lazily loaded the first time by the
	 * `get_languages` function.
	 *
	 * @since 3.9.0
	 * @var array|null An array of language codes => language names pairs or NULL if not initialized yet.
	 */
	private static $languages = null;

	/**
	 * The list of supported language codes.
	 *
	 * @since 3.9.0
	 *
	 * @var array An array of language codes.
	 */
	private static $codes = array(
		'be',
		'bg',
		'ca',
		'cs',
		'da',
		'en',
		'es',
		'et',
		'fi',
		'fr',
		'hr',
		'hu',
		'id',
		'is',
		'it',
		'lt',
		'lv',
		'nl',
		'no',
		'pl',
		'pt',
		'ro',
		'ru',
		'sk',
		'sl',
		'sq',
		'sr',
		'sv',
		'tr',
		'uk',
		'zh',
	);

	/**
	 * Get the list of WordLift's supported languages in an array with language code => language name pairs.
	 *
	 * @since 3.9.0
	 *
	 * @return array An array with language code => language name pairs.
	 */
	public static function get_languages() {

		// Lazily load the languages.
		if ( null === self::$languages ) {

			// Get the language names from WP's own (multisite) function.
			foreach ( self::$codes as $key ) {
				self::$languages[ $key ] = format_code_lang( $key );
			}

			// Sort by language name.
			asort( self::$languages );
		}

		return self::$languages;
	}

}
