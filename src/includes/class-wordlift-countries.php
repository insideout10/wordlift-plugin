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
	 * @var array|null An array of country codes => country names pairs or NULL if not initialized yet.
	 */
	private static $countries = null;

	/**
	 * The list of supported country codes.
	 *
	 * @since 3.18.0
	 *
	 * @var array An array of country codes.
	 */
	private static $codes = array(
		'ae' => array(
			'en',
		),
		'ar' => array(),
		'at' => array(),
		'au' => array(),
		'be' => array(
			'fr',
			'nl',
		),
		'bg' => array(),
		'br' => array(),
		'ca' => array(
			'en',
			'fr',
		),
		'ch' => array(
			'de',
			'en',
			'fr',
			'it',
		),
		'cl' => array(),
		'co' => array(),
		'cy' => array(
			'el',
			'en',
			'tr',
		),
		'cz' => array(),
		'de' => array(),
		'dk' => array(),
		'ec' => array(),
		'ee' => array(
			'et',
			'ru',
		),
		'eg' => array(),
		'es' => array(),
		'et' => array(
			'am',
			'en',
			'om',
			'so',
			'ti',
		),
		'fi' => array(
			'fi',
		),
		'fr' => array(),
		'ge' => array(
			'en',
		),
		'gr' => array(),
		'gt' => array(),
		'hr' => array(),
		'id' => array(),
		'ie' => array(),
		'il' => array(),
		'in' => array(
			'en',
			'hi',
			'ta',
			'te',
		),
		'it' => array(),
		'jp' => array(),
		'kh' => array(),
		'lt' => array(),
		'lu' => array(
			'de',
			'fr',
		),
		'ma' => array(),
		'mx' => array(),
		'my' => array(
			'en',
			'ms',
		),
		'nl' => array(),
		'no' => array(),
		'nz' => array(
			'en',
		),
		'pe' => array(
			'es',
			'qu',
		),
		'ph' => array(),
		'pl' => array(),
		'pt' => array(),
		'ro' => array(
			'hu',
			'ro',
		),
		'rs' => array(),
		'ru' => array(),
		'se' => array(),
		'sg' => array(
			'en',
			'ms',
			'zh-CN',
		),
		'sk' => array(),
		'th' => array(),
		'tr' => array(),
		'tw' => array(
			'zh-TW',
		),
		'ua' => array(
			'ru',
			'uk',
		),
		'uk' => array(),
		'us' => array(
			'en',
			'es',
			'zh-CN',
			'zh-TW',
		),
		'uy' => array(),
		'vn' => array(
			'en',
			'vi',
		),
		'za' => array(
			'af',
			'en',
		),
	);

	/**
	 * The list of country code => country name.
	 *
	 * @since 3.18.0
	 *
	 * @var array An array of country codes => country names.
	 */
	private static $country_codes = array(
		'af' => 'Afghanistan',
		'ax' => '\u00c5land Islands',
		'al' => 'Albania',
		'dz' => 'Algeria',
		'as' => 'American Samoa',
		'ad' => 'Andorra',
		'ao' => 'Angola',
		'ai' => 'Anguilla',
		'aq' => 'Antarctica',
		'ag' => 'Antigua and Barbuda',
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
		'bo' => 'Bolivia, Plurinational State of',
		'bq' => 'Bonaire, Sint Eustatius and Saba',
		'ba' => 'Bosnia and Herzegovina',
		'bw' => 'Botswana',
		'bv' => 'Bouvet Island',
		'br' => 'Brazil',
		'io' => 'British Indian Ocean Territory',
		'bn' => 'Brunei Darussalam',
		'bg' => 'Bulgaria',
		'bf' => 'Burkina Faso',
		'bi' => 'Burundi',
		'kh' => 'Cambodia',
		'cm' => 'Cameroon',
		'ca' => 'Canada',
		'cv' => 'Cape Verde',
		'ky' => 'Cayman Islands',
		'cf' => 'Central African Republic',
		'td' => 'Chad',
		'cl' => 'Chile',
		'cn' => 'China',
		'cx' => 'Christmas Island',
		'cc' => 'Cocos (Keeling) Islands',
		'co' => 'Colombia',
		'km' => 'Comoros',
		'cg' => 'Congo',
		'cd' => 'Congo, the Democratic Republic of the',
		'ck' => 'Cook Islands',
		'cr' => 'Costa Rica',
		'ci' => 'C\u00f4te d\'Ivoire',
		'hr' => 'Croatia',
		'cu' => 'Cuba',
		'cw' => 'Cura\u00e7ao',
		'cy' => 'Cyprus',
		'cz' => 'Czech Republic',
		'dk' => 'Denmark',
		'dj' => 'Djibouti',
		'dm' => 'Dominica',
		'do' => 'Dominican Republic',
		'ec' => 'Ecuador',
		'eg' => 'Egypt',
		'sv' => 'El Salvador',
		'gq' => 'Equatorial Guinea',
		'er' => 'Eritrea',
		'ee' => 'Estonia',
		'et' => 'Ethiopia',
		'fk' => 'Falkland Islands (Malvinas)',
		'fo' => 'Faroe Islands',
		'fj' => 'Fiji',
		'fi' => 'Finland',
		'fr' => 'France',
		'gf' => 'French Guiana',
		'pf' => 'French Polynesia',
		'tf' => 'French Southern Territories',
		'ga' => 'Gabon',
		'gm' => 'Gambia',
		'ge' => 'Georgia',
		'de' => 'Germany',
		'gh' => 'Ghana',
		'gi' => 'Gibraltar',
		'gr' => 'Greece',
		'gl' => 'Greenland',
		'gd' => 'Grenada',
		'gp' => 'Guadeloupe',
		'gu' => 'Guam',
		'gt' => 'Guatemala',
		'gg' => 'Guernsey',
		'gn' => 'Guinea',
		'gw' => 'Guinea-Bissau',
		'gy' => 'Guyana',
		'ht' => 'Haiti',
		'hm' => 'Heard Island and McDonald Islands',
		'va' => 'Holy See (Vatican City State)',
		'hn' => 'Honduras',
		'hk' => 'Hong Kong',
		'hu' => 'Hungary',
		'is' => 'Iceland',
		'in' => 'India',
		'id' => 'Indonesia',
		'ir' => 'Iran, Islamic Republic of',
		'iq' => 'Iraq',
		'ie' => 'Ireland',
		'im' => 'Isle of Man',
		'il' => 'Israel',
		'it' => 'Italy',
		'jm' => 'Jamaica',
		'jp' => 'Japan',
		'je' => 'Jersey',
		'jo' => 'Jordan',
		'kz' => 'Kazakhstan',
		'ke' => 'Kenya',
		'ki' => 'Kiribati',
		'kp' => 'Korea, Democratic People\'s Republic of',
		'kr' => 'Korea, Republic of',
		'kw' => 'Kuwait',
		'kg' => 'Kyrgyzstan',
		'la' => 'Lao People\'s Democratic Republic',
		'lv' => 'Latvia',
		'lb' => 'Lebanon',
		'ls' => 'Lesotho',
		'lr' => 'Liberia',
		'ly' => 'Libya',
		'li' => 'Liechtenstein',
		'lt' => 'Lithuania',
		'lu' => 'Luxembourg',
		'mo' => 'Macao',
		'mk' => 'Macedonia, the Former Yugoslav Republic of',
		'mg' => 'Madagascar',
		'mw' => 'Malawi',
		'my' => 'Malaysia',
		'mv' => 'Maldives',
		'ml' => 'Mali',
		'mt' => 'Malta',
		'mh' => 'Marshall Islands',
		'mq' => 'Martinique',
		'mr' => 'Mauritania',
		'mu' => 'Mauritius',
		'yt' => 'Mayotte',
		'mx' => 'Mexico',
		'fm' => 'Micronesia, Federated States of',
		'md' => 'Moldova, Republic of',
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
		'nc' => 'New Caledonia',
		'nz' => 'New Zealand',
		'ni' => 'Nicaragua',
		'ne' => 'Niger',
		'ng' => 'Nigeria',
		'nu' => 'Niue',
		'nf' => 'Norfolk Island',
		'mp' => 'Northern Mariana Islands',
		'no' => 'Norway',
		'om' => 'Oman',
		'pk' => 'Pakistan',
		'pw' => 'Palau',
		'ps' => 'Palestine, State of',
		'pa' => 'Panama',
		'pg' => 'Papua New Guinea',
		'py' => 'Paraguay',
		'pe' => 'Peru',
		'ph' => 'Philippines',
		'pn' => 'Pitcairn',
		'pl' => 'Poland',
		'pt' => 'Portugal',
		'pr' => 'Puerto Rico',
		'qa' => 'Qatar',
		're' => 'R\u00e9union',
		'ro' => 'Romania',
		'ru' => 'Russian Federation',
		'rw' => 'Rwanda',
		'bl' => 'Saint Barth\u00e9lemy',
		'sh' => 'Saint Helena, Ascension and Tristan da Cunha',
		'kn' => 'Saint Kitts and Nevis',
		'lc' => 'Saint Lucia',
		'mf' => 'Saint Martin (French part)',
		'pm' => 'Saint Pierre and Miquelon',
		'vc' => 'Saint Vincent and the Grenadines',
		'ws' => 'Samoa',
		'sm' => 'San Marino',
		'st' => 'Sao Tome and Principe',
		'sa' => 'Saudi Arabia',
		'sn' => 'Senegal',
		'rs' => 'Serbia',
		'sc' => 'Seychelles',
		'sl' => 'Sierra Leone',
		'sg' => 'Singapore',
		'sx' => 'Sint Maarten (Dutch part)',
		'sk' => 'Slovakia',
		'si' => 'Slovenia',
		'sb' => 'Solomon Islands',
		'so' => 'Somalia',
		'za' => 'South Africa',
		'gs' => 'South Georgia and the South Sandwich Islands',
		'ss' => 'South Sudan',
		'es' => 'Spain',
		'lk' => 'Sri Lanka',
		'sd' => 'Sudan',
		'sr' => 'Suriname',
		'sj' => 'Svalbard and Jan Mayen',
		'sz' => 'Swaziland',
		'se' => 'Sweden',
		'ch' => 'Switzerland',
		'sy' => 'Syrian Arab Republic',
		'tw' => 'Taiwan, Province of China',
		'tj' => 'Tajikistan',
		'tz' => 'Tanzania, United Republic of',
		'th' => 'Thailand',
		'tl' => 'Timor-Leste',
		'tg' => 'Togo',
		'tk' => 'Tokelau',
		'to' => 'Tonga',
		'tt' => 'Trinidad and Tobago',
		'tn' => 'Tunisia',
		'tr' => 'Turkey',
		'tm' => 'Turkmenistan',
		'tc' => 'Turks and Caicos Islands',
		'tv' => 'Tuvalu',
		'ug' => 'Uganda',
		'ua' => 'Ukraine',
		'ae' => 'United Arab Emirates',
		'gb' => 'United Kingdom',
		'uk' => 'United Kingdom',
		'us' => 'United States',
		'um' => 'United States Minor Outlying Islands',
		'uy' => 'Uruguay',
		'uz' => 'Uzbekistan',
		'vu' => 'Vanuatu',
		've' => 'Venezuela, Bolivarian Republic of',
		'vn' => 'Viet Nam',
		'vg' => 'Virgin Islands, British',
		'vi' => 'Virgin Islands, U.S.',
		'wf' => 'Wallis and Futuna',
		'eh' => 'Western Sahara',
		'ye' => 'Yemen',
		'zm' => 'Zambia',
		'zw' => 'Zimbabwe',
	);

	/**
	 * Get the list of WordLift's supported countries in an array with country code => country name pairs.
	 *
	 * @since 3.18.0
	 *
	 * @param bool|string $lang The language code we are looking for.
	 *
	 * @return array An array with country code => country name pairs.
	 */
	public static function get_countries( $lang = false ) {
		// Lazily load the countries.
		if ( ! is_null( self::$countries ) ) {
			return self::$countries;
		}

		// Get the country names from WP's own (multisite) function.
		foreach ( self::$codes as $key => $languages ) {
			if (
				// Process all countries if there is no language specified.
				empty( $lang ) ||

				// Or if there are no langugage limitations for current country.
				empty( self::$codes[ $key ] ) ||

				// Or if the language code exists for current country.
				! empty( $lang ) && in_array( $lang, self::$codes[ $key ] )
			) {
				self::$countries[ $key ] = self::format_country_code( $key );
			}
		}

		// Sort by country name.
		asort( self::$countries );

		// We don't sort here because `asort` returns bool instead of sorted array.
		return self::$countries;

	}

	/**
	 * Returns the country for a country code. This function is a clone of WP's function provided in `ms.php`.
	 *
	 * @since 3.18.0
	 *
	 * @param string $code Optional. The two-letter country code. Default empty.
	 *
	 * @return string The country corresponding to $code if it exists. If it does not exist,
	 *                then the first two letters of $code is returned.
	 */
	private static function format_country_code( $code = '' ) {
		$code = strtolower( substr( $code, 0, 2 ) );
		/**
		 * Filters the country codes.
		 *
		 * @since 3.18.0
		 *
		 * @param array  $country_codes Key/value pair of country codes where key is the short version.
		 * @param string $code          A two-letter designation of the country.
		 */
		$country_codes = apply_filters( 'country_code', self::$country_codes, $code );

		return strtr( $code, $country_codes );
	}

	/**
	 * Returns the country language pairs
	 *
	 * @since 3.18.0
	 *
	 * @return array Country language pairs
	 */
	public static function get_country_language_pairs() {
		return self::$codes;
	}

}
