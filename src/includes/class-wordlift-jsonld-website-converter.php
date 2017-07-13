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
	public function create_schema( $request ) {

		// Check if we have home/blog page id.
		if ( isset( $request['id'] ) ) {
			$post_id = $request['id'];

			// Get the base JSON-LD and the list of entities referenced by this entity.
			$jsonld = parent::convert( $post_id, $references );
		} else {
			// Create new jsonld, because we don't have id.
			$jsonld = array(
				'@context' => 'http://schema.org',
			);
		}

		// Change the jsonld to include site info, rather page info.
		$this->apply_website_info_to_json_ld( $jsonld );

		// Add seach action to jsonld
		$this->set_search_action( $jsonld );

		return $jsonld;
	}

	/**
	 * Update the JSON-LD structure to match the `WebSite` schema.org class.
	 *
	 * @since 3.14.0
	 *
	 * @param array $params  The JSON-LD structure.
	*/
	public function apply_website_info_to_json_ld( &$params ) {
		// Change values so they will be accurate for homepage

		// Change schema properties.
		$params['@type']       = 'WebSite';
		$params['headline']    = get_bloginfo( 'name' );
		$params['description'] = get_bloginfo( 'description' );
		$params['url']         = home_url( '/' );
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
