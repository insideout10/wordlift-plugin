<?php
/**
 * Converters: JSON-LD Website Converter.
 *
 * This file defines a converter for home and blog pages to JSON-LD array.
 *
 * @since   3.14.0
 * @package Wordlift
 */

use Wordlift\Relation\Relations;

/**
 * Define the {@link Wordlift_Website_Jsonld_Converter} class.
 *
 * @since 3.14.0
 */
class Wordlift_Website_Jsonld_Converter extends Wordlift_Post_To_Jsonld_Converter {

	/**
	 * Convert the home/blog page to a JSON-LD array.
	 *
	 * @return array A JSON-LD array.
	 * @since 3.14.0
	 */
	public function create_schema() {

		// Create new jsonld.
		$home_url = home_url( '/' );

		$jsonld = array(
			'@context'      => 'http://schema.org',
			'@type'         => 'WebSite',
			'@id'           => "$home_url#website",
			'name'          => html_entity_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
			'alternateName' => Wordlift_Configuration_Service::get_instance()->get_alternate_name(),
			'url'           => $home_url,
		);

		// Add search action.
		$this->set_search_action( $jsonld );

		// Add publisher information.
		$this->set_publisher_jsonld( $jsonld );

		/**
		 * Call the `wl_website_jsonld` filter.
		 *
		 * @param array $jsonld The JSON-LD structure.
		 *
		 * @since 3.14.0
		 *
		 * @api
		 */
		return apply_filters( 'wl_website_jsonld', $jsonld );
	}

	private function set_publisher_jsonld( &$jsonld ) {
		$publisher_id = Wordlift_Configuration_Service::get_instance()->get_publisher_id();

		// If the publisher id isn't set don't do anything.
		$publisher_id = Wordlift_Configuration_Service::get_instance()->get_publisher_id();
		if ( empty( $publisher_id ) ) {
			return;
		}

		// Get the post instance.
		$post = get_post( $publisher_id );
		if ( ! is_a( $post, '\WP_Post' ) ) {
			// Publisher not found.
			return;
		}

		// Get the Publisher data
		$references     = array();
		$reference_info = array();
		$relations      = new Relations();

		$publisher_jsonld = $this->convert(
			$publisher_id,
			$references,
			$reference_info,
			$relations
		);

		$jsonld['publisher'] = array(
			'@id' => $publisher_jsonld['@id']
		);

		$jsonld = [ $jsonld, $publisher_jsonld ];
	}

	/**
	 * Add SearchAction part to the schema
	 *
	 * @param array $params The parameters array.
	 *
	 * @since 3.14.0
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
