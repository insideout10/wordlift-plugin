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
	 * The Log service.
	 *
	 * @since 3.1.0
	 * @access private
	 * @var \Wordlift_Log_Service $log_service The Log service.
	 */
	private $log_service;

	/**
	 * Create an instance of Wordlift_Entity_Types_Taxonomy_Walker.
	 *
	 * @since 3.1.0
	 */
	public function __construct() {

		$this->log_service = Wordlift_Log_Service::get_logger( 'Wordlift_Entity_Types_Taxonomy_Walker' );

	}

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

		// We override the way WP prints the taxonomy metabox HTML
		$args['walker']        = $this;
		$args['checked_ontop'] = false;

		return $args;

	}

	/**
	 * Starts the list before the elements are added.
	 *
	 * Override the behaviour of the parent class to get a "flat" display
	 * by not emmiting the UL element
	 *
	 * @since 3.10
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of category. Used for tab indentation.
	 * @param array  $args   An array of arguments. @see wp_terms_checklist()
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * Override the behaviour of the parent class to get a "flat" display
	 * by not emmiting the UL element
	 *
	 * @since 3.10
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of category. Used for tab indentation.
	 * @param array  $args   An array of arguments. @see wp_terms_checklist()
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
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
	 * @param array $elements An array of elements.
	 * @param int $max_depth The maximum hierarchical depth.
	 *
	 * @param array $args Additional arguments.
	 *
	 * @return string The hierarchical item output.
	 */
	public function walk( $elements, $max_depth, $args = array() ) {

		$output = parent::walk( $elements, $max_depth, $args );

		global $post;

		$output = str_replace(
			array( "type=\"checkbox\"", "type='checkbox'" ),
			array( "type=\"radio\"", "type='radio'" ),
			$output
		);

		return $output;
	}

}
