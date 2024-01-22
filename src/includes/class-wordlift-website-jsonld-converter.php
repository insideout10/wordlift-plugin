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
class Wordlift_Website_Jsonld_Converter {

	/**
	 * @var Wordlift_Postid_To_Jsonld_Converter $postid_to_jsonld_converter
	 */
	private $postid_to_jsonld_converter;

	public function __construct( $postid_to_jsonld_converter ) {
		$this->postid_to_jsonld_converter = $postid_to_jsonld_converter;
	}

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
			array(
				'@context'      => 'http://schema.org',
				'@type'         => 'WebSite',
				'@id'           => "$home_url#website",
				'name'          => html_entity_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
				'alternateName' => Wordlift_Configuration_Service::get_instance()->get_alternate_name(),
				'url'           => $home_url,
			)
		);

		// Add search action.
		$this->set_search_action( $jsonld[0] );

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
		return apply_filters( 'wl_website_jsonld', $jsonld, get_the_ID() );
	}

	private function set_publisher_jsonld( &$jsonld ) {

		// If the publisher id isn't set don't do anything.
		$publisher_id = Wordlift_Configuration_Service::get_instance()->get_publisher_id();
		if ( empty( $publisher_id ) ) {
			return;
		}

		// If the publisher URI isn't set don't do anything.
		$publisher_uri = Wordlift_Entity_Service::get_instance()->get_uri( $publisher_id );
		if ( empty( $publisher_uri ) ) {
			return;
		}

		$jsonld[0]['publisher'] = array(
			'@id' => $publisher_uri,
		);

		$jsonld[] = $this->postid_to_jsonld_converter->convert( $publisher_id );
	}

	/**
	 * Add SearchAction part to the schema
	 *
	 * @param array $params The parameters array.
	 *
	 * @since 3.14.0
	 */
	private function set_search_action( &$main_entity_jsonld ) {
		/**
		 * Filter: 'wl_jsonld_search_url' - Allows filtering of the search URL.
		 *
		 * @since  3.14.0
		 * @api    string $search_url The search URL for this site with a `{search_term_string}` variable.
		 */
		$search_url = apply_filters( 'wl_jsonld_search_url', home_url( '/' ) . '?s={search_term_string}' );

		// Add search action to main item in JSON-LD.
		$main_entity_jsonld['potentialAction'] = array(
			'@type'       => 'SearchAction',
			'target'      => $search_url,
			'query-input' => 'required name=search_term_string',
		);

	}

}
