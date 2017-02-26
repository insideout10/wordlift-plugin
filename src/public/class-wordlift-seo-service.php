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

		/*
		 * Set filters to manipulate the HTML title element.
		 *
		 * There are two filters here, the first is the post WordPress 4.4
		 * proffered way, and the second is for backward compatibility with
		 * themes that do not play well with the first ones.
		 *
		 * Seems like Yoast SEO gladly ignores the title we supply and uses the
		 * term name, so for now use a higher priority to override its generated
		 * title (Yoast uses 15).
		 */
		add_filter( 'pre_get_document_title', array( $this, 'title' ), 20 );
		add_filter( 'wp_title', array( $this, 'title' ), 20, 3 );

		// Set the action to inject the description meta.
		add_action( 'wp_head', array( $this, 'description_meta' ) );

	}

	/**
	 * Override the default wordpress title for the entity type term archieve
	 * page.
	 *
	 * @since 3.11.0
	 *
	 * @param string $title              The title to be changed.
	 * @param string $separator          The separator between title elements.
	 * @param string $separator_location The location of the separator in the title.
	 *
	 * @return string If it is a entity type term page, the title set for the term
	 *                in the term settings screen. Otherwise return the $title argument.
	 */
	function title( $title, $separator = '', $separator_location = '' ) {

		// First check if we are serving an entity type term archive page.
		if ( is_tax( 'wl_entity_type' ) ) {

			// Get the term for the archive of which is displayed.
			$term = get_queried_object();

			/*
			 * Check if there is a none empty title setting for the term,
			 * and if there is, return it
			 */
			$entity_settings = get_option( 'wl_entity_type_settings', array() );

			if ( isset( $entity_settings[ $term->term_id ] ) ) {

				$settings = $entity_settings[ $term->term_id ];

				if ( ! empty( $settings['title'] ) ) {

					return $settings['title'];
				}

			}
		}

		// There was no modification done before, so just return the original.
		return $title;
	}

	/**
	 * Output a description meta into an entity type term archive page.
	 *
	 * @since 3.11.0
	 */
	function description_meta() {

		if ( is_tax( 'wl_entity_type' ) ) {

			$term = get_queried_object();

			$entity_settings = get_option( 'wl_entity_type_settings', array() );

			if ( isset( $entity_settings[ $term->term_id ] ) ) {

				$settings = $entity_settings[ $term->term_id ];

				if ( ! empty( $settings['description'] ) ) {

					echo '<meta name="description" content="', esc_attr( strip_tags( $settings['description'] ) ), '"/>', "\n";

				}

			}

		}

	}

}
