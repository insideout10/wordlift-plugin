<?php
/**
 * Wordlift_Countries class
 *
 * This class provides the list of countries supported by WordLift.
 *
 * @link    https://wordlift.io
 *
 * @package Wordlift
 * @since   3.18.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define the {@link Wordlift_Countries} class.
 *
 * @since 3.18.0
 */
class Wordlift_Countries {

	/**
	 * An array that will contain country codes => country names pairs. It gets lazily loaded the first time by the
	 * `get_countries` function.
	 *
	 * @since 3.18.0
	 * @var array An array of country codes => country names pairs or NULL if not initialized yet.
	 */
	private static $countries = array();

	/**
	 * The list of supported country codes, this is populated by self::lazy_populate_codes_and_country_codes_array.
	 *
	 * WARNING! If you change the list of supported countries, *you have* to add the related flag
	 * in the images/flags folder.
	 *
	 * @since 3.18.0
	 *
	 * @var array An array of country codes => supported_languages_array
	 */
	public static $codes = array();

	/**
	 * The list of country codes, this is populated by self::lazy_populate_codes_and_country_codes_array.
	 *
	 * WARNING! If you change the list of supported countries, *you have* to add the related flag
	 * in the images/flags folder.
	 *
	 * @since 3.18.0
	 *
	 * @var array An array of country codes => country names.
	 */
	private static $country_codes = array();

	/**
	 * An array of flag filenames.
	 *
	 * @since 3.20.0
	 *
	 * @var array An array of flag filenames.
	 */
	private static $country_flags = array(
		'af' => 'Afghanistan',
		'ax' => 'Aland',
		'al' => 'Albania',
		'dz' => 'Algeria',
		'as' => 'American-Samoa',
		'ad' => 'Andorra',
		'ao' => 'Angola',
		'ai' => 'Anguilla',
		'aq' => 'Antarctica',
		'ag' => 'Antigua-and-Barbuda',
		'ar' => 'Argentina',
		'am' => 'Armenia',
		'aw' => 'Aruba',
		'au' => 'Australia',
		'at' => 'Austria',
		'az' => 'Azerbaijan',
		'bs' => 'Bahamas',
		'bh' => 'Bahrain',
		'bd' => 'Bangladesh',
		'bb' => 'Barbados',
		'by' => 'Belarus',
		'be' => 'Belgium',
		'bz' => 'Belize',
		'bj' => 'Benin',
		'bm' => 'Bermuda',
		'bt' => 'Bhutan',
		'bo' => 'Bolivia',
		// Uses Netherlands' flag, see https://en.wikipedia.org/wiki/Caribbean_Netherlands.
		'bq' => 'Netherlands',
		'ba' => 'Bosnia-and-Herzegovina',
		'bw' => 'Botswana',
		'bv' => 'Bouvet Island',
		'br' => 'Brazil',
		'io' => null,
		'bn' => 'Brunei',
		'bg' => 'Bulgaria',
		'bf' => 'Burkina-Faso',
		'bi' => 'Burundi',
		'kh' => 'Cambodia',
		'cm' => 'Cameroon',
		'ca' => 'Canada',
		'cv' => 'Cape-Verde',
		'ky' => 'Cayman-Islands',
		'cf' => 'Central-African-Republic',
		'td' => 'Chad',
		'cl' => 'Chile',
		'cn' => 'China',
		'cx' => 'Christmas-Island',
		'cc' => 'Cocos-Keeling-Islands',
		'co' => 'Colombia',
		'km' => 'Comoros',
		'cg' => 'Republic-of-the-Congo',
		'cd' => 'Democratic-Republic-of-the-Congo',
		'ck' => 'Cook-Islands',
		'cr' => 'Costa-Rica',
		'ci' => 'Cote-dIvoire',
		'hr' => 'Croatia',
		'cu' => 'Cuba',
		'cw' => 'Curacao',
		'cy' => 'Cyprus',
		'cz' => 'Czech-Republic',
		'dk' => 'Denmark',
		'dj' => 'Djibouti',
		'dm' => 'Dominica',
		'do' => 'Dominican-Republic',
		'ec' => 'Ecuador',
		'eg' => 'Egypt',
		'sv' => 'El-Salvador',
		'gq' => 'Equatorial-Guinea',
		'er' => 'Eritrea',
		'ee' => 'Estonia',
		'et' => 'Ethiopia',
		'fk' => 'Falkland-Islands',
		'fo' => 'Faroes',
		'fj' => 'Fiji',
		'fi' => 'Finland',
		'fr' => 'France',
		// Uses France's flag, see https://en.wikipedia.org/wiki/French_Guiana.
		'gf' => 'France',
		'pf' => 'French-Polynesia',
		'tf' => 'French-Southern-Territories',
		'ga' => 'Gabon',
		'gm' => 'Gambia',
		'ge' => 'Georgia',
		'de' => 'Germany',
		'gh' => 'Ghana',
		'gi' => 'Gibraltar',
		'gr' => 'Greece',
		'gl' => 'Greenland',
		'gd' => 'Grenada',
		// Uses France's flag, see https://en.wikipedia.org/wiki/Guadeloupe.
		'gp' => 'France',
		'gu' => 'Guam',
		'gt' => 'Guatemala',
		'gg' => 'Guernsey',
		'gn' => 'Guinea',
		'gw' => 'Guinea-Bissau',
		'gy' => 'Guyana',
		'ht' => 'Haiti',
		// Uses Australia's flag, see https://en.wikipedia.org/wiki/Heard_Island_and_McDonald_Islands.
		'hm' => 'Australia',
		'va' => 'Vatican-City',
		'hn' => 'Honduras',
		'hk' => 'Hong-Kong',
		'hu' => 'Hungary',
		'is' => 'Iceland',
		'in' => 'India',
		'id' => 'Indonesia',
		'ir' => 'Iran',
		'iq' => 'Iraq',
		'ie' => 'Ireland',
		'im' => 'Isle-of-Man',
		'il' => 'Israel',
		'it' => 'Italy',
		'jm' => 'Jamaica',
		'jp' => 'Japan',
		'je' => 'Jersey',
		'jo' => 'Jordan',
		'kz' => 'Kazakhstan',
		'ke' => 'Kenya',
		'ki' => 'Kiribati',
		'kp' => 'North-Korea',
		'kr' => 'South-Korea',
		'kw' => 'Kuwait',
		'kg' => 'Kyrgyzstan',
		'la' => 'Laos',
		'lv' => 'Latvia',
		'lb' => 'Lebanon',
		'ls' => 'Lesotho',
		'lr' => 'Liberia',
		'ly' => 'Libya',
		'li' => 'Liechtenstein',
		'lt' => 'Lithuania',
		'lu' => 'Luxembourg',
		'mo' => 'Macau',
		'mk' => 'Macedonia',
		'mg' => 'Madagascar',
		'mw' => 'Malawi',
		'my' => 'Malaysia',
		'mv' => 'Maldives',
		'ml' => 'Mali',
		'mt' => 'Malta',
		'mh' => 'Marshall-Islands',
		'mq' => 'Martinique',
		'mr' => 'Mauritania',
		'mu' => 'Mauritius',
		'yt' => 'Mayotte',
		'mx' => 'Mexico',
		'fm' => 'Micronesia',
		'md' => 'Moldova',
		'mc' => 'Monaco',
		'mn' => 'Mongolia',
		'me' => 'Montenegro',
		'ms' => 'Montserrat',
		'ma' => 'Morocco',
		'mz' => 'Mozambique',
		'mm' => 'Myanmar',
		'na' => 'Namibia',
		'nr' => 'Nauru',
		'np' => 'Nepal',
		'nl' => 'Netherlands',
		'nc' => 'New-Caledonia',
		'nz' => 'New-Zealand',
		'ni' => 'Nicaragua',
		'ne' => 'Niger',
		'ng' => 'Nigeria',
		'nu' => 'Niue',
		'nf' => 'Norfolk-Island',
		'mp' => 'Northern-Mariana-Islands',
		'no' => 'Norway',
		'om' => 'Oman',
		'pk' => 'Pakistan',
		'pw' => 'Palau',
		'ps' => 'Palestine',
		'pa' => 'Panama',
		'pg' => 'Papua-New-Guinea',
		'py' => 'Paraguay',
		'pe' => 'Peru',
		'ph' => 'Philippines',
		'pn' => 'Pitcairn-Islands',
		'pl' => 'Poland',
		'pt' => 'Portugal',
		'pr' => 'Puerto Rico',
		'qa' => 'Qatar',
		// Uses France's flag, see https://en.wikipedia.org/wiki/R%C3%A9union.
		're' => 'France',
		'ro' => 'Romania',
		'ru' => 'Russia',
		'rw' => 'Rwanda',
		'bl' => 'Saint-Barthelemy',
		'sh' => 'Saint-Helena',
		'kn' => 'Saint-Kitts-and-Nevis',
		'lc' => 'Saint-Lucia',
		'mf' => 'Saint-Martin',
		// Uses France's flag, see https://en.wikipedia.org/wiki/Saint_Pierre_and_Miquelon.
		'pm' => 'France',
		'vc' => 'Saint-Vincent-and-the-Grenadines',
		'ws' => 'Samoa',
		'sm' => 'San-Marino',
		'st' => 'Sao-Tome-and-Principe',
		'sa' => 'Saudi-Arabia',
		'sn' => 'Senegal',
		'rs' => 'Serbia',
		'sc' => 'Seychelles',
		'sl' => 'Sierra-Leone',
		'sg' => 'Singapore',
		'sx' => null,
		'sk' => 'Slovakia',
		'si' => 'Slovenia',
		'sb' => 'Solomon-Islands',
		'so' => 'Somalia',
		'za' => 'South-Africa',
		'gs' => 'South-Georgia-and-the-South-Sandwich-Islands',
		'ss' => 'South-Sudan',
		'es' => 'Spain',
		'lk' => 'Sri-Lanka',
		'sd' => 'Sudan',
		'sr' => 'Suriname',
		// Uses Norway's flag, see https://en.wikipedia.org/wiki/Svalbard_and_Jan_Mayen.
		'sj' => 'Norway',
		'sz' => 'Swaziland',
		'se' => 'Sweden',
		'ch' => 'Switzerland',
		'sy' => 'Syria',
		'tw' => 'Taiwan',
		'tj' => 'Tajikistan',
		'tz' => 'Tanzania',
		'th' => 'Thailand',
		'tl' => 'East-Timor',
		'tg' => 'Togo',
		'tk' => 'Tokelau',
		'to' => 'Tonga',
		'tt' => 'Trinidad-and-Tobago',
		'tn' => 'Tunisia',
		'tr' => 'Turkey',
		'tm' => 'Turkmenistan',
		'tc' => 'Turks-and-Caicos-Islands',
		'tv' => 'Tuvalu',
		'ug' => 'Uganda',
		'ua' => 'Ukraine',
		'ae' => 'United-Arab-Emirates',
		'gb' => 'United-Kingdom',
		'uk' => 'United-Kingdom',
		'us' => 'United-States',
		'um' => 'United-States',
		'uy' => 'Uruguay',
		'uz' => 'Uzbekistan',
		'vu' => 'Vanuatu',
		've' => 'Venezuela',
		'vn' => 'Vietnam',
		'vg' => 'British-Virgin-Islands',
		'vi' => 'US-Virgin-Islands',
		'wf' => 'Wallis-And-Futuna',
		'eh' => 'Western-Sahara',
		'ye' => 'Yemen',
		'zm' => 'Zambia',
		'zw' => 'Zimbabwe',
	);

	/**
	 * Parse_country_code_json_file_to_array.
	 *
	 * @param string $file_name The json file name where the supported country
	 * and languages are present.
	 *
	 * @return array An Array having two maps, country_code_language_map and country_code_name_map.
	 */
	public static function parse_country_code_json_file_to_array( $file_name ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$json_file_contents = file_get_contents( $file_name );
		$decoded_array      = json_decode( $json_file_contents, true );
		// decoded array would be null if the json_decode parses
		// invalid content.
		if ( null === $decoded_array ) {
			return array(
				'country_code_name_map'     => array(),
				'country_code_language_map' => array(),
			);
		}
		$result = array();
		// country_code => country_language map.
		$country_code_language_map = array();
		// country_code => country_name map.
		$country_code_name_map = array();
		foreach ( $decoded_array as $key => $value ) {
			$country_code_language_map[ $key ] = $value['supportedLang'];
			$country_code_name_map [ $key ]    = $value['defaultLoc']['loc_name'];
		}
		$result['country_code_language_map'] = $country_code_language_map;
		$result['country_code_name_map']     = $country_code_name_map;

		return $result;
	}

	/**
	 * Get the list of WordLift's supported country codes from json file mapping country_code => languages.
	 *
	 * @param string $file_name The json file where the supported country codes and language_codes are stored.
	 *
	 * @return array An Array having two maps, country_code_language_map and country_code_name_map.
	 * @since 3.22.5.1
	 */
	public static function get_codes_from_json_file( $file_name ) {
		if ( file_exists( $file_name ) ) {
			return self::parse_country_code_json_file_to_array( $file_name );
		}

		return array(
			'country_code_name_map'     => array(),
			'country_code_language_map' => array(),
		);
	}

	/**
	 * Returns the country language pairs.
	 *
	 * @return array The country language pairs.
	 * @since 3.18.0
	 */
	public static function get_codes() {
		return self::$codes;
	}

	/**
	 * Populate self::codes and self::country_codes if not done before.
	 *
	 * @param string $file_name The json file where the supported country codes and language_codes are stored.
	 *
	 * @return void
	 * @since 3.22.5.1
	 */
	private static function lazy_populate_codes_and_country_codes_array( $file_name ) {
		if ( null === $file_name ) {
			$file_name = __DIR__ . '/supported-countries.json';
		}
		if ( 0 === count( self::$codes ) || 0 === count( self::$country_codes ) ) {
			// populate the two arrays.
			$result_array        = self::get_codes_from_json_file( $file_name );
			self::$codes         = $result_array['country_code_language_map'];
			self::$country_codes = $result_array['country_code_name_map'];
		}

	}

	/**
	 * Reset codes_and_country_codes static variable, used for testing
	 *
	 * @return void
	 * @since 3.22.5.1
	 */
	public static function reset_codes_and_country_codes() {
		self::$codes         = array();
		self::$country_codes = array();
	}

	/**
	 * Get the list of WordLift's supported countries in an array with country code => country name pairs.
	 *
	 * @param string|false $lang Optional. The language code we are looking for. Default `any`.
	 *
	 * @param string|null  $file_name Optional. The json file containing country codes
	 *   and language data.
	 *
	 * @return array An array with country code => country name pairs.
	 * @since 3.18.0
	 */
	public static function get_countries( $lang = false, $file_name = null ) {

		// populate the codes and countries array if it is not done before.
		self::lazy_populate_codes_and_country_codes_array( $file_name );

		// Lazily load the countries.
		// $lang_key = false === $lang ? 'any' : $lang;
		$lang_key = 'any';
		$lang     = '';

		if ( isset( self::$countries[ $lang_key ] ) ) {
			return self::$countries[ $lang_key ];
		}

		// Prepare the array.
		self::$countries[ $lang ] = array();

		// Get the country names from WP's own (multisite) function.
		foreach ( self::$codes as $key => $languages ) {
			if (
				// Process all countries if there is no language specified.
				empty( $lang ) ||

				// Or if there are no language limitations for current country.
				empty( self::$codes[ $key ] ) ||

				// Or if the language code exists for current country.
				! empty( $lang ) && in_array( $lang, self::$codes[ $key ], true )
			) {
				self::$countries[ $lang_key ][ $key ] = self::format_country_code( $key );
			}
		}

		// Sort by country name.
		asort( self::$countries[ $lang_key ] );

		// We don't sort here because `asort` returns bool instead of sorted array.
		return self::$countries[ $lang_key ];
	}

	/**
	 * Returns the country for a country code. This function is a clone of WP's function provided in `ms.php`.
	 *
	 * @param string $code Optional. The two-letter country code. Default empty.
	 *
	 * @return string The country corresponding to $code if it exists. If it does not exist,
	 *                then the first two letters of $code is returned.
	 * @since 3.18.0
	 */
	private static function format_country_code( $code = '' ) {

		$code = strtolower( substr( $code, 0, 2 ) );
		/**
		 * Filters the country codes.
		 *
		 * @param array $country_codes Key/value pair of country codes where key is the short version.
		 * @param string $code A two-letter designation of the country.
		 *
		 * @since 3.18.0
		 */
		$country_codes = apply_filters( 'country_code', self::$country_codes, $code );

		return strtr( $code, $country_codes );
	}

	/**
	 * Get a flag URL.
	 *
	 * @param string $country_code The country code.
	 *
	 * @return string|null The flag url or null if not available.
	 * @since 3.20.0
	 */
	public static function get_flag_url( $country_code ) {

		// Bail out if we don't have the flag.
		if ( ! isset( self::$country_flags[ $country_code ] )
			 || ( self::$country_flags[ $country_code ] ) === null ) {
			return null;
		}

		return plugin_dir_url( __DIR__ )
			   . 'images/flags/16/'
			   . self::$country_flags[ $country_code ]
			   . '.png';
	}

	/**
	 * Get a country name given a country code.
	 *
	 * @param string $country_code The 2-letters country code.
	 *
	 * @return null|string The country name (in English) or null if not found.
	 * @since 3.20.0
	 */
	public static function get_country_name( $country_code ) {

		/**
		 * @since 3.27.6
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/1188
		 */
		if ( ! isset( self::$country_codes[ $country_code ] ) ) {
			return '';
		}

		return self::$country_codes[ $country_code ];
	}

}
