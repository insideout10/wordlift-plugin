<?php
/**
 * Converters: JSON-LD Website Converter.
 *
 * This file defines a converter for home and blog pages to JSON-LD array.
 *
 * @since   3.14.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Website_Jsonld_Converter} class.
 *
 * @since 3.14.0
 */
class Wordlift_Website_Jsonld_Converter extends Wordlift_Post_To_Jsonld_Converter {

	/**
	 * Convert the home/blog page to a JSON-LD array.
	 *
	 * @since 3.14.0
	 *
	 * @param array $request An array of homepage info.
	 *
	 * @return array A JSON-LD array.
	 */
	public function create_schema() {

		// Create new jsonld.
		$jsonld = array(
			'@context'      => 'http://schema.org',
			'@type'         => 'WebSite',
			'name'          => get_bloginfo( 'name' ),
			'alternateName' => get_bloginfo( 'description' ),
			'url'           => home_url( '/' ),
		);

		// Add publisher information.
		$this->set_publisher( $jsonld );

		// Add search action.
		$this->set_search_action( $jsonld );

		// Return the jsonld schema.
		return $jsonld;
	}

	/**
	 * Add SearchAction part to the schema
	 *
	 * @since 3.14.0
	 *
	 * @param array $params The parameters array.
	 */
	private function set_search_action( &$params ) {
		/**
		 * Filter: 'wordlift_json_ld_search_url' - Allows filtering of the search URL.
		 *
		 * @since  3.14.0
		 * @api string $search_url The search URL for this site with a `{search_term_string}` variable.
		 */
		$search_url = apply_filters( 'wordlift_json_ld_search_url', home_url( '/' ) . '?s={search_term_string}' );

		// Add search action
		$params['potentialAction'] = array(
			'@type'       => 'SearchAction',
			'target'      => $search_url,
			'query-input' => 'required name=search_term_string',
		);
	}
}
