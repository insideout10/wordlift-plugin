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
	 * @param $post_id
	 *
	 * @return array A JSON-LD array.
	 * @since 3.14.0
	 *
	 */
	public function create_schema( $post_id ) {

		// Create new jsonld.
		$home_url = home_url( '/' );

		$jsonld = array(
			'@context'      => 'http://schema.org',
			'@type'         => 'WebSite',
			'@id'           => "$home_url#website",
			'name'          => html_entity_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
			'alternateName' => html_entity_decode( get_bloginfo( 'description' ), ENT_QUOTES ),
			'url'           => $home_url,
		);

		// Add publisher information.
		$this->set_publisher( $jsonld );

		// Add search action.
		$this->set_search_action( $jsonld );

		/**
		 * Call the `wl_website_jsonld` filter.
		 *
		 * @param array $jsonld The JSON-LD structure.
		 *
		 * Added $post_id parameter since 3.27.8
		 *
		 * @since 3.14.0
		 *
		 * @api
		 *
		 */
		$website_jsonld = apply_filters( 'wl_website_jsonld', $jsonld, $post_id );

		/**
		 * Filter : `wl_website_jsonld_array`
		 *
		 * @since 3.30.0
		 *
		 * @param array $website_jsonld An Jsonld array.
		 */
		return apply_filters( 'wl_website_jsonld_array', array( $website_jsonld ), $post_id );

	}

	/**
	 * Add SearchAction part to the schema
	 *
	 * @param array $params The parameters array.
	 *
	 * @since 3.14.0
	 *
	 */
	private function set_search_action( &$params ) {
		/**
		 * Filter: 'wl_jsonld_search_url' - Allows filtering of the search URL.
		 *
		 * @since  3.14.0
		 * @api    string $search_url The search URL for this site with a `{search_term_string}` variable.
		 */
		$search_url = apply_filters( 'wl_jsonld_search_url', home_url( '/' ) . '?s={search_term_string}' );

		// Add search action
		$params['potentialAction'] = array(
			'@type'       => 'SearchAction',
			'target'      => $search_url,
			'query-input' => 'required name=search_term_string',
		);

	}

}
