<?php
/**
 * Services: SEO Services.
 *
 * The file defines a class for SEO related manipulations.
 *
 * @link       https://wordlift.io
 *
 * @since      3.11.0
 *
 * @package    Wordlift
 * @subpackage Wordlift/public
 */

/**
 * Handles SEO related manipulation in the HTML header section
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/public
 */
class Wordlift_Seo_Service {

	/**
	 * @inheritdoc
	 */
	function __construct() {

		// If we are not on the admin, run the get_term filter for entity type terms.
		if ( ! is_admin() ) {
			add_filter( 'get_wl_entity_type', array( $this, 'get_wl_entity_type' ), 10, 2 );
		}
	}

	/**
	 * Filter the entity term object, replace title and description
	 * with whatever was set in the entity settings page
	 *
	 * @since 3.11
	 *
	 * @param WP_Term	$Term	The term to filters.
	 * @param string 	$taxonomy	The taxonomy name.
	 *
	 * @return WP_Term which is $term with fields changed
	 */
	function get_wl_entity_type( $term, $taxonomy ) {

		$entity_settings = get_option( 'wl_entity_type_settings', array() );

		if ( isset( $entity_settings[ $term->term_id ] ) ) {

			$settings = $entity_settings[ $term->term_id ];
			if ( ! empty( $settings['title'] ) ) {
				$term->name = $settings['title'];
			}
			if ( ! empty( $settings['description'] ) ) {
				$term->description = $settings['description'];
			}
		}

		return $term;
	}

}
