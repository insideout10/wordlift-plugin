<?php
/**
 * Services: Alphabet Service
 *
 * The Alphabet service provides alphabets for different languages in order to
 * support creating the Vocabulary Widget.
 *
 * @since      3.17.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_Alphabet_Service} class.
 *
 * @since 3.17.0
 */
class Wordlift_Alphabet_Service {

	/**
	 * Get the alphabet for the specified language code.
	 *
	 * @since 3.17.0
	 *
	 * @param string $language_code A two-letter language code.
	 *
	 * @return array The letters array or an empty array if the language isn't supported.
	 */
	public static function get( $language_code ) {

		switch ( $language_code ) {
			case '':
			case 'en':
			case 'fr':
			case 'de':
			case 'ca':
			case 'nl':
			case 'id':
			case 'it':
			case 'pt':
			case 'uk':
			case 'zh-cn':
				return self::reset( range( 'A', 'Z' ) );

			case 'be':
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/be_utf8/langconfig.php .
				return self::reset( explode( ',', 'А,Б,В,Г,Д,ДЖ,ДЗ,Е,Ё,Ж,З,І,Й,К,Л,М,Н,О,П,Р,С,Т,У,Ў,Ф,Х,Ц,Ч,Ш,Ы,Ь,Э,Ю,Я' ) );

			case 'no':
			case 'da':
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/nn_utf8/langconfig.php .
				return self::reset( explode( ',', 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,Æ,Ø,Å' ) );

			case 'bg':
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/bg_utf8/langconfig.php .
				return self::reset( explode( ',', 'А,Б,В,Г,Д,Е,Ж,З,И,Й,К,Л,М,Н,О,П,Р,С,Т,У,Ф,Х,Ц,Ч,Ш,Щ,Ъ,Ь,Ю,Я' ) );

			case 'cs':
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/cs_utf8/langconfig.php .
				return self::reset( explode( ',', 'A,Á,B,C,Č,D,Ď,E,É,Ě,F,G,H,I,Í,J,K,L,M,N,Ň,O,Ó,P,Q,R,Ř,S,Š,T,Ť,U,Ú,Ů,V,W,X,Y,Ý,Z,Ž' ) );

			case 'es':
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/es_utf8/langconfig.php .
				return self::reset( explode( ',', 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,Ñ,O,P,Q,R,S,T,U,V,W,X,Y,Z' ) );

			case 'et':
				// @see https://en.wikipedia.org/wiki/Estonian_orthography#Alphabet .
				return self::reset( explode( ',', 'A,B,D,E,F,G,H,I,J,K,L,M,N,O,P,R,S,Š,Z,Ž,T,U,V,Õ,Ä,Ö,Ü' ) );

			case 'fi':
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/fi_utf8/langconfig.php .
				return self::reset( explode( ',', 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,Å,Ä,Ö' ) );

			case 'hr':
				// @see https://github.com/dismine/Valentina_sonar/blob/2930a1c51406255001331bf6013f85fc340b91f7/scripts/alphabets.py .
				return self::reset( explode( ',', 'A.B.C.Č.Ć.D.DŽ.Ð.E.F.G.H.I.J.K,L,LJ,M,N,NJ,O,P,R,S,Š,T,U,V,Z,Ž' ) );

			case 'hu':
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/hu_utf8/langconfig.php .
				return self::reset( explode( ',', 'A,Á,B,C,CS,D,DZ,DZS,E,É,F,G,GY,H,I,Í,J,K,L,M,N,NY,O,Ó,Ö,Ő,P,Q,R,S,SZ,T,TY,U,Ú,Ü,Ű,V,W,X,Y,Z,ZS' ) );

			case 'is':
				// @see https://en.wikipedia.org/wiki/Icelandic_orthography .
				return self::reset( explode( ',', 'A,Á,B,D,Ð,E,É,F,G,H,I,Í,J,K,L,M,N,O,Ó,P,R,S,T,U,Ú,V,X,Y,Ý,Þ,Æ,Ö' ) );

			case 'lt':
				// @see https://en.wikipedia.org/wiki/Lithuanian_orthography#Alphabet .
				return self::reset( explode( ',', 'A,Ą,B,C,Č,D,E,Ę,Ė,F,G,H,I,Į,Y,J,K,L,M,N,O,P,R,S,Š,T,U,Ų,Ū,V,Z,Ž' ) );

			case 'lv':
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/lv_utf8/langconfig.php .
				return self::reset( explode( ',', 'A,Ā,B,C,Č,D,E,Ē,F,G,Ģ,H,I,Ī,J,K,Ķ,L,Ļ,M,N,Ņ,O,P,Q,R,S,Š,T,U,Ū,V,W,X,Y,Z,Ž' ) );

			case 'pl':
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/pl_utf8/langconfig.php .
				return self::reset( explode( ',', 'A,Ą,B,C,Ć,D,E,Ę,F,G,H,I,J,K,L,Ł,M,N,Ń,O,Ó,P,Q,R,S,Ś,T,U,V,W,X,Y,Z,Ź,Ż' ) );

			case 'ro':
				// @see https://github.com/dismine/Valentina_sonar/blob/2930a1c51406255001331bf6013f85fc340b91f7/scripts/alphabets.py .
				return self::reset( explode( ',', 'A,Ă,Â,B,C,D,E,F,G,H,I,Î,J,K,L,M,N,O,P,Q,R,S,Ș,T,Ț,U,V,W,X,Y,Z' ) );

			case 'ru':
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/ru_utf8/langconfig.php .
				return self::reset( explode( ',', 'А,Б,В,Г,Д,Е,Ё,Ж,З,И,К,Л,М,Н,О,П,Р,С,Т,У,Ф,Х,Ц,Ч,Ш,Щ,Э,Ю,Я' ) );

			case 'sk':
				// @see https://github.com/dismine/Valentina_sonar/blob/2930a1c51406255001331bf6013f85fc340b91f7/scripts/alphabets.py .
				return self::reset( explode( ',', 'A,Á,Ä,B,C,Č,D,Ď,DZ,DŽ,E,É,F,G,H,CH,I,Í,J,K,L,Ĺ,Ľ,M,N,Ň,O,Ó,Ô,P,Q,R,Ŕ,S,Š,T,Ť,U,Ú,V,W,X,Y,Ý,Z,Ž' ) );

			case 'sl':
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/sl_utf8/langconfig.php .
				return self::reset( explode( ',', 'A,B,C,Č,D,E,F,G,H,I,J,K,L,M,N,O,P,R,S,Š,T,U,V,Z,Ž' ) );

			case 'sq':
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/sq_utf8/langconfig.php .
				return self::reset( explode( ',', 'A,B,C,Ç,D,E,Ë,F,G,GJ,H,I,J,K,L,LL,M,N,O,P,Q,R,RR,S,SH,T,TH,U,V,X,XH,Y,Z,ZH' ) );

			case 'sr':
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/sr_cr_utf8/langconfig.php .
				return self::reset( explode( ',', 'А,Б,В,Г,Д,Ђ,Е,Ж,З,И,J,K,Л,Љ,М,Н,Њ,O,П,Р,С,Т,Ћ,У,Ф,Х,Ц,Ч,Џ,Ш' ) );

			case 'sv':
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/sv_utf8/langconfig.php .
				return self::reset( explode( ',', 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,Å,Ä,Ö' ) );

			case 'tr':
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/tr_utf8/langconfig.php .
				return self::reset( explode( ',', 'A,B,C,Ç,D,E,F,G,H,I,İ,J,K,L,M,N,O,Ö,P,R,S,Ş,T,U,Ü,V,Y,Z,Q,W,X' ) );

		}

		// If we got here, we don't support the language, then return an empty array.
		return self::reset( array() );
	}

	/**
	 * Reset the alphabet array by assigning an empty array to each key.
	 *
	 * @since 3.17.0
	 *
	 * @param array $alphabet The alphabet array.
	 *
	 * @return array The alphabet array with letters as keys and empty arrays as values.
	 */
	private static function reset( $alphabet ) {

		$letters = array_flip( $alphabet );

		foreach ( $letters as $key => $value ) {
			$letters[ $key ] = array();
		}

		return $letters + array(
			'#' => array(),
		);
	}

}
