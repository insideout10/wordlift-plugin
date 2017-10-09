<?php
/**
 * Shortcodes: Glossary Shortcode.
 *
 * `wl_glossary` implementation.
 *
 * @since      3.16.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_Glossary_Shortcode} class.
 *
 * @since      3.16.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_Glossary_Shortcode extends Wordlift_Shortcode {

	const SHORTCODE = 'wl_glossary';

	/**
	 * The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.11.0
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

	/**
	 * Create a {@link Wordlift_Glossary_Shortcode} instance.
	 *
	 * @since 3.16.0
	 *
	 * @param \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	public function __construct( $configuration_service ) {
		parent::__construct();
		$this->configuration_service = $configuration_service;
	}

	/**
	 * Display format for a letter.
	 *
	 * A utility function returning the string representation of a letter
	 * as required for output - first letter uppercase and the rest (if there are)
	 * lower case.
	 *
	 * @since 3.16.0
	 *
	 * @param string $letter The letter, assumed to be upper case.
	 *
	 * @return string The letter as it should be displayed.
	 */
	private function display_format( $letter ) {
		if ( 1 === mb_strlen( $letter ) ) {
			return $letter;
		}

		return mb_substr( $letter, 0, 1 ) . mb_convert_case( mb_substr( $letter, 1 ), MB_CASE_LOWER );
	}

	/**
	 * Render the shortcode.
	 *
	 * @since 3.16.0
	 *
	 * @param array $atts An array of shortcode attributes as set by the editor.
	 *
	 * @return string The output html code.
	 */
	public function render( $atts ) {
		global $post;

		// Extract attributes and set default values.
		$atts = shortcode_atts( array(
			'type'  => 'all',
			'type'	=> 'all',
		), $atts );

		$args = array(
			'numberposts'	=> -1,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		// Limit the based entity type if needed.
		if ( 'all' !== $atts['type'] ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME,
					'field' => 'slug',
					'terms' => $atts['type'],
				),
			);
		}

		$posts = get_posts( Wordlift_Entity_Service::add_criterias( $args ) );

		$collection = array();
		$lang_code = $this->configuration_service->get_language_code();
		$letters = array();
		switch ( $lang_code ) {
			case '' :
			case 'en' :
			case 'fr' :
			case 'de' :
			case 'ca' :
			case 'nl' :
			case 'id' :
			case 'it' :
			case 'pt' :
			case 'uk' :
			case 'zh-cn' :
				$letters = range( 'A', 'Z' );
				break;
			case 'be' :
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/be_utf8/langconfig.php .
				$letters = explode( ',' , 'А,Б,В,Г,Д,ДЖ,ДЗ,Е,Ё,Ж,З,І,Й,К,Л,М,Н,О,П,Р,С,Т,У,Ў,Ф,Х,Ц,Ч,Ш,Ы,Ь,Э,Ю,Я' );
				break;
			case 'no' :
			case 'da' :
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/nn_utf8/langconfig.php .
				$letters = explode( ',' , 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,Æ,Ø,Å' );
				break;
			case 'bg' :
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/bg_utf8/langconfig.php .
				$letters = explode( ',' , 'А,Б,В,Г,Д,Е,Ж,З,И,Й,К,Л,М,Н,О,П,Р,С,Т,У,Ф,Х,Ц,Ч,Ш,Щ,Ъ,Ь,Ю,Я' );
				break;
			case 'cs' :
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/cs_utf8/langconfig.php .
				$letters = explode( ',' , 'A,Á,B,C,Č,D,Ď,E,É,Ě,F,G,H,I,Í,J,K,L,M,N,Ň,O,Ó,P,Q,R,Ř,S,Š,T,Ť,U,Ú,Ů,V,W,X,Y,Ý,Z,Ž' );
				break;
			case 'es' :
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/es_utf8/langconfig.php .
				$letters = explode( ',' , 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,Ñ,O,P,Q,R,S,T,U,V,W,X,Y,Z' );
				break;
			case 'et' :
				// @see https://en.wikipedia.org/wiki/Estonian_orthography#Alphabet .
				$letters = explode( ',' , 'A,B,D,E,F,G,H,I,J,K,L,M,N,O,P,R,S,Š,Z,Ž,T,U,V,Õ,Ä,Ö,Ü' );
				break;
			case 'fi' :
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/fi_utf8/langconfig.php .
				$letters = explode( ',' , 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,Å,Ä,Ö' );
				break;
			case 'hr' :
				// @see https://github.com/dismine/Valentina_sonar/blob/2930a1c51406255001331bf6013f85fc340b91f7/scripts/alphabets.py .
				$letters = explode( ',' , 'A.B.C.Č.Ć.D.DŽ.Ð.E.F.G.H.I.J.K,L,LJ,M,N,NJ,O,P,R,S,Š,T,U,V,Z,Ž' );
				break;
			case 'hu' :
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/hu_utf8/langconfig.php .
				$letters = explode( ',' , 'A,Á,B,C,CS,D,DZ,DZS,E,É,F,G,GY,H,I,Í,J,K,L,M,N,NY,O,Ó,Ö,Ő,P,Q,R,S,SZ,T,TY,U,Ú,Ü,Ű,V,W,X,Y,Z,ZS' );
				break;
			case 'is' :
				// @see https://en.wikipedia.org/wiki/Icelandic_orthography .
				$letters = explode( ',' , 'A,Á,B,D,Ð,E,É,F,G,H,I,Í,J,K,L,M,N,O,Ó,P,R,S,T,U,Ú,V,X,Y,Ý,Þ,Æ,Ö' );
				break;
			case 'lt' :
				// @see https://en.wikipedia.org/wiki/Lithuanian_orthography#Alphabet .
				$letters = explode( ',' , 'A,Ą,B,C,Č,D,E,Ę,Ė,F,G,H,I,Į,Y,J,K,L,M,N,O,P,R,S,Š,T,U,Ų,Ū,V,Z,Ž' );
				break;
			case 'lv' :
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/lv_utf8/langconfig.php .
				$letters = explode( ',' , 'A,Ā,B,C,Č,D,E,Ē,F,G,Ģ,H,I,Ī,J,K,Ķ,L,Ļ,M,N,Ņ,O,P,Q,R,S,Š,T,U,Ū,V,W,X,Y,Z,Ž' );
				break;
			case 'pl' :
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/pl_utf8/langconfig.php .
				$letters = explode( ',' , 'A,Ą,B,C,Ć,D,E,Ę,F,G,H,I,J,K,L,Ł,M,N,Ń,O,Ó,P,Q,R,S,Ś,T,U,V,W,X,Y,Z,Ź,Ż' );
				break;
			case 'ro' :
				// @see https://github.com/dismine/Valentina_sonar/blob/2930a1c51406255001331bf6013f85fc340b91f7/scripts/alphabets.py .
				$letters = explode( ',' , 'A,Ă,Â,B,C,D,E,F,G,H,I,Î,J,K,L,M,N,O,P,Q,R,S,Ș,T,Ț,U,V,W,X,Y,Z' );
				break;
			case 'ru' :
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/ru_utf8/langconfig.php .
				$letters = explode( ',' , 'А,Б,В,Г,Д,Е,Ё,Ж,З,И,К,Л,М,Н,О,П,Р,С,Т,У,Ф,Х,Ц,Ч,Ш,Щ,Э,Ю,Я' );
				break;
			case 'sk' :
				// @see https://github.com/dismine/Valentina_sonar/blob/2930a1c51406255001331bf6013f85fc340b91f7/scripts/alphabets.py .
				$letters = explode( ',' , 'A,Á,Ä,B,C,Č,D,Ď,DZ,DŽ,E,É,F,G,H,CH,I,Í,J,K,L,Ĺ,Ľ,M,N,Ň,O,Ó,Ô,P,Q,R,Ŕ,S,Š,T,Ť,U,Ú,V,W,X,Y,Ý,Z,Ž' );
				break;
			case 'sl' :
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/sl_utf8/langconfig.php .
				$letters = explode( ',' , 'A,B,C,Č,D,E,F,G,H,I,J,K,L,M,N,O,P,R,S,Š,T,U,V,Z,Ž' );
				break;
			case 'sq' :
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/sq_utf8/langconfig.php .
				$letters = explode( ',' , 'A,B,C,Ç,D,E,Ë,F,G,GJ,H,I,J,K,L,LL,M,N,O,P,Q,R,RR,S,SH,T,TH,U,V,X,XH,Y,Z,ZH' );
				break;
			case 'sr' :
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/sr_cr_utf8/langconfig.php .
				$letters = explode( ',' , 'А,Б,В,Г,Д,Ђ,Е,Ж,З,И,J,K,Л,Љ,М,Н,Њ,O,П,Р,С,Т,Ћ,У,Ф,Х,Ц,Ч,Џ,Ш' );
				break;
			case 'sv' :
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/sv_utf8/langconfig.php .
				$letters = explode( ',' , 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,Å,Ä,Ö' );
				break;
			case 'tr' :
				// @see https://github.com/mudrd8mz/moodle-lang/blob/9f43e2c74953545f2ff836159cbec9fb7a710fcd/tr_utf8/langconfig.php .
				$letters = explode( ',' , 'A,B,C,Ç,D,E,F,G,H,I,İ,J,K,L,M,N,O,Ö,P,R,S,Ş,T,U,Ü,V,Y,Z,Q,W,X' );
				break;
		}
		$letters[] = '#';  // For letters not in the alphabet.
		$flip_letters = array_flip( $letters ); // Small optimization for letter existance detection.

		foreach ( $posts as $p ) {
			$title = remove_accents( get_the_title( $p->ID ) );

			// Need to handle letters which consist of 3 and 2 characters.
			$current_letter = mb_convert_case( mb_substr( $title, 0, 3 ), MB_CASE_UPPER );
			if ( ! isset( $flip_letters[ $current_letter ] ) ) {
				$current_letter = mb_convert_case( mb_substr( $title, 0, 2 ), MB_CASE_UPPER );
				if ( ! isset( $flip_letters[ $current_letter ] ) ) {
					$current_letter = mb_convert_case( mb_substr( $title, 0, 1 ), MB_CASE_UPPER );
				}
			}

			// No letter matched? use the # "letter".
			if ( ! isset( $flip_letters[ $current_letter ] ) ) {
				$current_letter = '#';
			}

			if ( ! isset( $collection[ $current_letter ] ) ) {
				$collection[ $current_letter ] = array();
			}
			$collection[ $current_letter ][ $title ] = $p->ID;
		}

		// Generate the header.
		$header = '';
		foreach ( $letters as $letter ) {
			$display = $this->display_format( $letter );
			if ( ! isset( $collection[ $letter ] ) ) {
				$header .= '<span class="wl-glossary-disabled">' . esc_html( $display ) . '</span>';
			} else {
				$header .= '<a href="#wl_glossary_' . esc_attr( $letter ) .
						'">' . esc_html( $display ) . '</a>';
			}
		}

		// Generate the sections.
		$sections = '';
		foreach ( $letters as $letter ) {

			if ( ! isset( $collection[ $letter ] ) ) {
				continue;
			}

			$v = $collection[ $letter ];

			// Sort case insensitive by title.
			ksort( $v,  SORT_STRING | SORT_FLAG_CASE );

			// Add letter section.
			$sections .= '<div class="wl-letter-block" id="wl_glossary_' . esc_attr( $letter ) . '>';
			$sections .= '<aside class="wl-left-column">' . esc_html( $this->display_format( $letter ) ) . '</aside>';
			$sections .= '<div class="wl-right-column"><ul class="wl-glossary-items-list">';

			// Add links to the posts.
			foreach ( $v as $title => $post_id ) {
				$sections .= '<li><a href="' . esc_url( get_permalink( $post_id ) ) . '">' .
								esc_html( get_the_title( $post_id ) ) . '</a></li>';
			}
			$sections .= '</ul></div></div>'; // Close list, right div and letter div.
		}

		wp_enqueue_style( 'wl_glossary_shortcode_css', dirname( plugin_dir_url( __FILE__ ) ) . '/css/wordlift-glossary-shortcode.css' );

		// Return HTML template.
		return <<<EOF
<div class="wl-glossary">
	<nav class="wl-glossary-alphabet-nav">
		$header
	</nav>
	<div class="wl-glossary-grid">
		$sections
	</div>
</div>
EOF;

	}

}
