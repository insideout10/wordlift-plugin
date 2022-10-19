<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Walkers: Entity Types Taxonomy Walker.
 *
 * This file contains the Entity Types Taxonomy Walker whose main role is to
 * turn checkboxes to radios for the Entity Types taxonomy.
 *
 * @since      3.1.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
if ( ! class_exists( 'Walker_Category_Checklist' ) ) {
	require_once ABSPATH . 'wp-admin/includes/template.php';
}

/**
 * A class extending the {@link Walker_Category_Checklist} in order to turn
 * checkboxes into radios.
 *
 * @since      3.1.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
// phpcs:ignore Generic.Classes.DuplicateClassName.Found
class Wordlift_Entity_Types_Taxonomy_Walker extends Walker_Category_Checklist {

	/**
	 * Entity taxonomy metabox must show exclusive options, no checkboxes.
	 *
	 * @since 3.1.0
	 *
	 * @param       $args     {
	 *                        An array of arguments.
	 *
	 * @type string $taxonomy The taxonomy name.
	 *              }
	 *
	 * @return array An array of arguments, with this walker in case the taxonomy is the Entity Type taxonomy.
	 */
	public function terms_checklist_args( $args ) {

		if ( ! isset( $args['taxonomy'] ) || Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME !== $args['taxonomy'] ) {
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
	public function walk( $elements, $max_depth, ...$args ) {

		// `max_depth` force to -1 to display a flat taxonomy.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/305
		$output = parent::walk( $elements, - 1, ...$args );

		$output = str_replace(
			array( 'type="checkbox"', "type='checkbox'" ),
			array( 'type="radio"', "type='radio'" ),
			$output
		);

		return $output;
	}

	/**
	 * Start the element output, output nothing in case of article term.
	 *
	 * @since 3.15.0
	 *
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category The current term object.
	 * @param int    $depth    Depth of the term in reference to parents. Default 0.
	 * @param array  $args     An array of arguments. @see wp_terms_checklist()
	 * @param int    $id       ID of the current term.
	 */
	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		global $post;

		if ( ! isset( $post ) ) {
			return;
		}

		if ( Wordlift_Entity_Service::TYPE_NAME !== $post->post_type
			 || 'article' !== $category->slug
			 || Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME !== $args['taxonomy'] ) {
			parent::start_el( $output, $category, $depth, $args, $id );
		}
	}

	/**
	 * End the element output, output nothing in case of article term.
	 *
	 * @since 3.15.0
	 *
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category The current term object.
	 * @param int    $depth    Depth of the term in reference to parents. Default 0.
	 * @param array  $args     An array of arguments. @see wp_terms_checklist()
	 */
	public function end_el( &$output, $category, $depth = 0, $args = array() ) {
		global $post;

		if ( ! isset( $post ) ) {
			return;
		}

		if ( Wordlift_Entity_Service::TYPE_NAME !== $post->post_type
			 || 'article' !== $category->slug
			 || Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME !== $args['taxonomy'] ) {
			parent::end_el( $output, $category, $depth, $args );
		}

	}

}
