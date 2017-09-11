<?php
/**
 * Shortcodes: Glossary Shortcode.
 *
 * `wl_glossary` implementation.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_Glossary_Shortcode} class.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_Glossary_Shortcode extends Wordlift_Shortcode {

	const SHORTCODE = 'wl_glossary';

	/**
	 * Create a {@link Wordlift_Glossary_Shortcode} instance.
	 *
	 * @since 3.15.0
	 */
	public function __construct() {
		parent::__construct();

	}

	/**
	 * Render the shortcode.
	 *
	 * @since 3.15.0
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
			'referenced' => '0',
			'type'	=> 'all',
			'excerpt'	=> false,
		), $atts );

		$args = array(
			'numberposts'	=> -1,
		);

		// Limit the based entity type if needed.
		if ( 'all' != $atts['type'] ) {
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
		foreach ( $posts as $p ) {
			$title = get_the_title( $p->ID );
			$current_letter = mb_convert_case( mb_substr( $title, 0, 1 ), MB_CASE_UPPER );
			if ( ! isset( $collection[ $current_letter ] ) ) {
				$collection[ $current_letter ] = array();
			}
			$collection[ $current_letter ][ $title ] = $p->ID;
		}

		// Sort alphbetically the letters collection.
		ksort( $collection,  SORT_STRING );

		// Generate the header.
		$header = '';
		foreach ( $collection as $letter => $v ) {
			$header .= '<a href="#wl_glossary_' . esc_attr( $letter ) .
						'">' . esc_html( $letter ) . '</a>';
		}

		// Generate the sections.
		$sections = '';
		foreach ( $collection as $letter => $v ) {

			// Sort case insensitive by title.
			ksort( $v,  SORT_STRING | SORT_FLAG_CASE );

			// Add section header.
			$sections .= '<h3 id="wl_glossary_' . esc_attr( $letter ) .
							'">' . esc_html( $letter ) . '</h3>';

			// Add links to the posts.
			foreach ( $v as $title => $post_id ) {
				$sections .= '<div><a href="' . esc_url( get_permalink( $post_id ) ) . '">' .
								esc_html( $title ) . '</a></div>';
				if ( $atts['excerpt'] ) {
					$post = get_post( $post_id );
					setup_postdata( $post );
					$sections .= '<div class="wl_glossary_excerpt">' . get_the_excerpt() . '</div>';
				}
			}
		}

		/*
		 * We had to mangle the global variable to get the excerpt working properly,
		 * need to return them to original state.
		 */
		if ( $atts['excerpt'] ) {
			wp_reset_postdata();
		}

		// Return HTML template.
		return <<<EOF
<div class="wl_glossary">
	<div class="wl_glossary_header">
		$header
	</div>
	<div class="wl_glossary_sections">
		$sections
	</div>
</div>
EOF;
	}

}
