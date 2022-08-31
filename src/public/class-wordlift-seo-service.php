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
	public function __construct() {

		// If we are not on the admin, run the get_term filter for entity type terms.
		add_filter(
			'get_wl_entity_type',
			array(
				$this,
				'get_wl_entity_type',
			),
			10
		);

	}

	/**
	 * Filter the entity term object, and when not in admin context replace title
	 * and description with whatever was set in the entity settings page.
	 *
	 * @param WP_Term $term The term to filters.
	 *
	 * @return WP_Term The {@link WP_Term} with fields changed.
	 * @since    3.11
	 */
	public function get_wl_entity_type( $term ) {

		// Do nothing when in admin.
		if ( is_admin() ) {
			return $term;
		}

		// Get the terms' settings.
		$entity_settings = get_option( 'wl_entity_type_settings', array() );

		// If we have no settings for the specified term, then return the original
		// term.
		if ( ! isset( $entity_settings[ $term->term_id ] ) ) {

			return $term;
		}

		// Get the settings for the specified term.
		$settings = $entity_settings[ $term->term_id ];

		// Update the name.
		if ( ! empty( $settings['title'] ) ) {

			$term->name = $settings['title'];

		}

		// Update the description.
		if ( ! empty( $settings['description'] ) ) {

			$term->description = $settings['description'];

		}

		// Return the updated term.
		return $term;
	}

}
