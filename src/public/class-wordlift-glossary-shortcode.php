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

	/**
	 * The shortcode.
	 *
	 * @since  3.17.0
	 */
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
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.17.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * Create a {@link Wordlift_Glossary_Shortcode} instance.
	 *
	 * @since 3.16.0
	 *
	 * @param \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	public function __construct( $configuration_service ) {
		parent::__construct();

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		$this->configuration_service = $configuration_service;

	}

	/**
	 * Check whether the requirements for this shortcode to work are available.
	 *
	 * @since 3.17.0
	 * @return bool True if the requirements are satisfied otherwise false.
	 */
	private static function are_requirements_satisfied() {

		return function_exists( 'mb_strlen' ) &&
			   function_exists( 'mb_substr' ) &&
			   function_exists( 'mb_convert_case' );
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

		// Bail out if the requirements aren't satisfied: we need mbstring for
		// the vocabulary widget to work.
		if ( ! self::are_requirements_satisfied() ) {
			$this->log->warn( "The vocabulary widget cannot be displayed because this WordPress installation doesn't satisfy its requirements." );

			return '';
		}

		// Extract attributes and set default values.
		$atts = shortcode_atts( array(
			// The entity type, such as `person`, `organization`, ...
			'type'  => 'all',
			// Limit the number of posts to 100 by default. Use -1 to remove the limit.
			'limit' => 100,
		), $atts );

		// Get the posts. Note that if a `type` is specified before, then the
		// `tax_query` from the `add_criterias` call isn't added.
		$posts = $this->get_posts( $atts );

		// Get the alphabet and add the `#` for titles not matching any letter.
		$language_code = $this->configuration_service->get_language_code();
		$letters       = Wordlift_Alphabet_Service::get( $language_code ) + array( '#' );
		$flip_letters  = array_flip( $letters ); // Small optimization for letter existence detection.

		$collection = array();

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
			ksort( $v, SORT_STRING | SORT_FLAG_CASE );

			// Add letter section.
			$sections .= '<div class="wl-glossary-letter-block" id="wl_glossary_' . esc_attr( $letter ) . '">';
			$sections .= '<aside class="wl-glossary-left-column">' . esc_html( $this->display_format( $letter ) ) . '</aside>';
			$sections .= '<div class="wl-glossary-right-column"><ul class="wl-glossary-items-list">';

			// Add links to the posts.
			foreach ( $v as $title => $post_id ) {
				$sections .= '<li><a href="' . esc_url( get_permalink( $post_id ) ) . '">' .
							 esc_html( get_the_title( $post_id ) ) . '</a></li>';
			}
			$sections .= '</ul></div></div>'; // Close list, right div and letter div.
		}

		wp_enqueue_style( 'wl_glossary_shortcode_css', dirname( plugin_dir_url( __FILE__ ) ) . '/public/css/wordlift-glossary-shortcode.css' );

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

	/**
	 * Get the posts from WordPress using the provided attributes.
	 *
	 * @since 3.17.0
	 *
	 * @param array $atts The shortcode attributes.
	 *
	 * @return array An array of {@link WP_Post}s.
	 */
	private function get_posts( $atts ) {

		// The default arguments for the query.
		$args = array(
			'numberposts'            => intval( $atts['limit'] ),
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		// Limit the based entity type if needed.
		if ( 'all' !== $atts['type'] ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME,
					'field'    => 'slug',
					'terms'    => $atts['type'],
				),
			);
		}

		// Get the posts. Note that if a `type` is specified before, then the
		// `tax_query` from the `add_criterias` call isn't added.
		return get_posts( Wordlift_Entity_Service::add_criterias( $args ) );

	}

}
