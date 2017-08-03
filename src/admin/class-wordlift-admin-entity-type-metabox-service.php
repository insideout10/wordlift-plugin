<?php
/**
 * Admin UI: Admin Entity Type Metabox.
 *
 * The {@link Wordlift_Admin_Entity_Type_MetaBoxe_Service} class handles modifications
 * to the metabox UI
 *
 * @link       https://wordlift.io
 *
 * @package    Wordlift
 * @subpackage Wordlift/admin
 * @since      3.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( ABSPATH . 'wp-admin/includes/class-walker-category-checklist.php' );

/**
 * The Entity type metabox controller.
 *
 * Methods to manipulate the output of the entity type metabox in edit pages
 *
 * @package    Wordlift
 * @subpackage Wordlift/admin
 *
 * @since 3.15.0
 */
class Wordlift_Admin_Entity_Type_MetaBox_Service extends Walker_Category_Checklist {

	/**
	 * Handle the wp_terms_checklist_args hook. Add this object as a walker for
	 * it if we are on entity editing page.
	 *
	 * @since 3.15.0
	 *
	 * @param array $args An array containing the various parameters to the
	 *                    generation of the metabox.
	 * @param int $post_id The ID of the post being edited
	 *
	 * @return array Same $args as received in the input if not editing an entity
	 *               Or add this object as a walker in case an entity is being edited.
	 */
	public function wp_terms_checklist_args( $args, $post_id ) {
		if ( Wordlift_Entity_Service::TYPE_NAME === get_post_type( $post_id ) ) {
			$args['walker'] = $this;
			return $args;
		} else {
			return $args;
		}
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
		if ( ('article' !== $category->slug ) ||
			 ( Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME !== $args['taxonomy'] ) ) {
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
	public function end_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		if ( ('article' !== $category->slug ) ||
			 ( Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME !== $args['taxonomy'] ) ) {
			parent::end_el( $output, $category, $depth, $args, $id );
		}

		// For entity type convert checkbox to radio
		if ( Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME === $args['taxonomy'] ) {
			$output = str_replace( '"checkbox"', '"radio"', $output );
		}
	}
}
