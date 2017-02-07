<?php
/**
 * This file contains the Entity Types Taxonomy Walker whose main role is to turn checkboxes to radios for the
 * Entity Types taxonomy.
 */
if ( ! class_exists( 'Walker_Category_Checklist' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/template.php' );
}

/**
 * A class extending the {@link Walker_Category_Checklist} in order to turn checkboxes into radios.
 *
 * @since 3.1.0
 */
class Wordlift_Entity_Types_Taxonomy_Walker extends Walker_Category_Checklist {

	/**
	 * Entity taxonomy metabox must show exclusive options, no checkboxes.
	 *
	 * @since 3.1.0
	 *
	 * @param $args
	 *
	 * @return array An array of arguments, with this walker in case the taxonomy is the Entity Type taxonomy.
	 */
	function terms_checklist_args( $args ) {

		if ( ! isset( $args['taxonomy'] ) || Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME !== $args['taxonomy'] ) {
			return $args;
		}

		// We override the way WP prints the taxonomy metabox HTML.
		$args['walker']        = $this;
		$args['checked_ontop'] = false;

		return $args;

	}

	/**
	 * Change checkboxes to radios.
	 *
	 * $max_depth = -1 means flatly display every element.
	 * $max_depth = 0 means display all levels.
	 * $max_depth > 0 specifies the number of display levels.
	 *
	 * @since 3.1.0
	 *
	 * @param array $elements  An array of elements.
	 * @param int   $max_depth The maximum hierarchical depth.
	 *
	 * @param array $args      Additional arguments.
	 *
	 * @return string The hierarchical item output.
	 */
	public function walk( $elements, $max_depth, $args = array() ) {

		// `max_depth` force to -1 to display a flat taxonomy.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/305
		$output = parent::walk( $elements, - 1, $args );

		$output = str_replace(
			array( "type=\"checkbox\"", "type='checkbox'" ),
			array( "type=\"radio\"", "type='radio'" ),
			$output
		);

		return $output;
	}

}
