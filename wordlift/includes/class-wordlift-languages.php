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
		'ar',
		'be',
		'bg',
		'ca',
		'cs',
		'da',
		'de',
		'el',
		'en',
		'es',
		'et',
		'fi',
		'fr',
		'he',
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
		'zh-cn',
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
				self::$languages[ $key ] = self::get_language_name( $key );
			}

			// Sort by language name.
			asort( self::$languages );
		}

		return self::$languages;
	}

	/**
	 * Returns the language for a language code. This function is a clone of WP's function provided in `ms.php`.
	 *
	 * @since 3.9.3
	 *
	 * @param string $code Optional. The two-letter language code. Default empty.
	 *
	 * @return string The language corresponding to $code if it exists. If it does not exist,
	 *                then the first two letters of $code is returned.
	 */
	public static function get_language_name( $code = '' ) {
		$code       = strtolower( substr( $code, 0, 2 ) );
		$lang_codes = array(
			'aa' => 'Afar',
			'ab' => 'Abkhazian',
			'af' => 'Afrikaans',
			'ak' => 'Akan',
			'sq' => 'Albanian',
			'am' => 'Amharic',
			'ar' => 'Arabic',
			'an' => 'Aragonese',
			'hy' => 'Armenian',
			'as' => 'Assamese',
			'av' => 'Avaric',
			'ae' => 'Avestan',
			'ay' => 'Aymara',
			'az' => 'Azerbaijani',
			'ba' => 'Bashkir',
			'bm' => 'Bambara',
			'eu' => 'Basque',
			'be' => 'Belarusian',
			'bn' => 'Bengali',
			'bh' => 'Bihari',
			'bi' => 'Bislama',
			'bs' => 'Bosnian',
			'br' => 'Breton',
			'bg' => 'Bulgarian',
			'my' => 'Burmese',
			'ca' => 'Catalan; Valencian',
			'ch' => 'Chamorro',
			'ce' => 'Chechen',
			'zh' => 'Chinese',
			'cu' => 'Church Slavic; Old Slavonic; Church Slavonic; Old Bulgarian; Old Church Slavonic',
			'cv' => 'Chuvash',
			'kw' => 'Cornish',
			'co' => 'Corsican',
			'cr' => 'Cree',
			'cs' => 'Czech',
			'da' => 'Danish',
			'dv' => 'Divehi; Dhivehi; Maldivian',
			'nl' => 'Dutch; Flemish',
			'dz' => 'Dzongkha',
			'en' => 'English',
			'eo' => 'Esperanto',
			'et' => 'Estonian',
			'ee' => 'Ewe',
			'fo' => 'Faroese',
			'fj' => 'Fijjian',
			'fi' => 'Finnish',
			'fr' => 'French',
			'fy' => 'Western Frisian',
			'ff' => 'Fulah',
			'ka' => 'Georgian',
			'de' => 'German',
			'gd' => 'Gaelic; Scottish Gaelic',
			'ga' => 'Irish',
			'gl' => 'Galician',
			'gv' => 'Manx',
			'el' => 'Greek, Modern',
			'gn' => 'Guarani',
			'gu' => 'Gujarati',
			'ht' => 'Haitian; Haitian Creole',
			'ha' => 'Hausa',
			'he' => 'Hebrew',
			'hz' => 'Herero',
			'hi' => 'Hindi',
			'ho' => 'Hiri Motu',
			'hu' => 'Hungarian',
			'ig' => 'Igbo',
			'is' => 'Icelandic',
			'io' => 'Ido',
			'ii' => 'Sichuan Yi',
			'iu' => 'Inuktitut',
			'ie' => 'Interlingue',
			'ia' => 'Interlingua (International Auxiliary Language Association)',
			'id' => 'Indonesian',
			'ik' => 'Inupiaq',
			'it' => 'Italian',
			'jv' => 'Javanese',
			'ja' => 'Japanese',
			'kl' => 'Kalaallisut; Greenlandic',
			'kn' => 'Kannada',
			'ks' => 'Kashmiri',
			'kr' => 'Kanuri',
			'kk' => 'Kazakh',
			'km' => 'Central Khmer',
			'ki' => 'Kikuyu; Gikuyu',
			'rw' => 'Kinyarwanda',
			'ky' => 'Kirghiz; Kyrgyz',
			'kv' => 'Komi',
			'kg' => 'Kongo',
			'ko' => 'Korean',
			'kj' => 'Kuanyama; Kwanyama',
			'ku' => 'Kurdish',
			'lo' => 'Lao',
			'la' => 'Latin',
			'lv' => 'Latvian',
			'li' => 'Limburgan; Limburger; Limburgish',
			'ln' => 'Lingala',
			'lt' => 'Lithuanian',
			'lb' => 'Luxembourgish; Letzeburgesch',
			'lu' => 'Luba-Katanga',
			'lg' => 'Ganda',
			'mk' => 'Macedonian',
			'mh' => 'Marshallese',
			'ml' => 'Malayalam',
			'mi' => 'Maori',
			'mr' => 'Marathi',
			'ms' => 'Malay',
			'mg' => 'Malagasy',
			'mt' => 'Maltese',
			'mo' => 'Moldavian',
			'mn' => 'Mongolian',
			'na' => 'Nauru',
			'nv' => 'Navajo; Navaho',
			'nr' => 'Ndebele, South; South Ndebele',
			'nd' => 'Ndebele, North; North Ndebele',
			'ng' => 'Ndonga',
			'ne' => 'Nepali',
			'nn' => 'Norwegian Nynorsk; Nynorsk, Norwegian',
			'nb' => 'Bokmål, Norwegian, Norwegian Bokmål',
			'no' => 'Norwegian',
			'ny' => 'Chichewa; Chewa; Nyanja',
			'oc' => 'Occitan, Provençal',
			'oj' => 'Ojibwa',
			'or' => 'Oriya',
			'om' => 'Oromo',
			'os' => 'Ossetian; Ossetic',
			'pa' => 'Panjabi; Punjabi',
			'fa' => 'Persian',
			'pi' => 'Pali',
			'pl' => 'Polish',
			'pt' => 'Portuguese',
			'ps' => 'Pushto',
			'qu' => 'Quechua',
			'rm' => 'Romansh',
			'ro' => 'Romanian',
			'rn' => 'Rundi',
			'ru' => 'Russian',
			'sg' => 'Sango',
			'sa' => 'Sanskrit',
			'sr' => 'Serbian',
			'hr' => 'Croatian',
			'si' => 'Sinhala; Sinhalese',
			'sk' => 'Slovak',
			'sl' => 'Slovenian',
			'se' => 'Northern Sami',
			'sm' => 'Samoan',
			'sn' => 'Shona',
			'sd' => 'Sindhi',
			'so' => 'Somali',
			'st' => 'Sotho, Southern',
			'es' => 'Spanish; Castilian',
			'sc' => 'Sardinian',
			'ss' => 'Swati',
			'su' => 'Sundanese',
			'sw' => 'Swahili',
			'sv' => 'Swedish',
			'ty' => 'Tahitian',
			'ta' => 'Tamil',
			'tt' => 'Tatar',
			'te' => 'Telugu',
			'tg' => 'Tajik',
			'tl' => 'Tagalog',
			'th' => 'Thai',
			'bo' => 'Tibetan',
			'ti' => 'Tigrinya',
			'to' => 'Tonga (Tonga Islands)',
			'tn' => 'Tswana',
			'ts' => 'Tsonga',
			'tk' => 'Turkmen',
			'tr' => 'Turkish',
			'tw' => 'Twi',
			'ug' => 'Uighur; Uyghur',
			'uk' => 'Ukrainian',
			'ur' => 'Urdu',
			'uz' => 'Uzbek',
			've' => 'Venda',
			'vi' => 'Vietnamese',
			'vo' => 'Volapük',
			'cy' => 'Welsh',
			'wa' => 'Walloon',
			'wo' => 'Wolof',
			'xh' => 'Xhosa',
			'yi' => 'Yiddish',
			'yo' => 'Yoruba',
			'za' => 'Zhuang; Chuang',
			'zu' => 'Zulu',
		);

		/**
		 * Filters the language codes.
		 *
		 * @since MU
		 *
		 * @param array $lang_codes Key/value pair of language codes where key is the short version.
		 * @param string $code A two-letter designation of the language.
		 */
		$lang_codes = apply_filters( 'lang_codes', $lang_codes, $code );

		return strtr( $code, $lang_codes );
	}

}
