<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 26.02.18
 * Time: 10:10
 */

class Wordlift_Address_Sparql_Tuple_Rendition implements Wordlift_Sparql_Tuple_Rendition {

	private $renditions;

	/**
	 * Get tuple representations for the specified {@link WP_Post}.
	 *
	 * @since 3.18.0
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return array An array of tuples.
	 */
	function get( $post_id ) {
		// TODO: Implement get() method.

		$tuples = array();
		/** @var Wordlift_Sparql_Tuple_Rendition $rendition */
		foreach ( $this->renditions as $rendition ) {
			array_merge( $tuples, $rendition->get( $post_id ) );
		}

		if (empty($tuples)) return array();



		return array_merge( $tuples, '<uri> <http://schema.org/address> <uri/address>');
	}

	function __construct( $rendition_factory, $storage_factory, $language_code ) {

		$this->renditions = array(

			// ### schema:streetAddress.
			$rendition_factory->create(
				$storage_factory->post_meta( Wordlift_Schema_Service::FIELD_ADDRESS ),
				Wordlift_Query_Builder::SCHEMA_STREET_ADDRESS,
				null,
				$language_code,
				'/address'
			),

			// ### schema:postOfficeBoxNumber.
			$rendition_factory->create(
				$storage_factory->post_meta( Wordlift_Schema_Service::FIELD_ADDRESS_POSTAL_CODE ),
				'http://schema.org/postOfficeBoxNumber',
				null,
				null,
				'/address'
			),

			// ### schema:addressLocality.
			$rendition_factory->create(
				$storage_factory->post_meta( Wordlift_Schema_Service::FIELD_ADDRESS_LOCALITY ),
				'http://schema.org/addressLocality',
				null,
				$language_code,
				'/address'
			),

			// ### schema:addressRegion.
			$rendition_factory->create(
				$storage_factory->post_meta( Wordlift_Schema_Service::FIELD_ADDRESS_REGION ),
				'http://schema.org/addressRegion',
				null,
				$language_code,
				'/address'
			),

			// ### schema:addressCountry.
			$rendition_factory->create(
				$storage_factory->post_meta( Wordlift_Schema_Service::FIELD_ADDRESS_COUNTRY ),
				'http://schema.org/addressCountry',
				null,
				$language_code,
				'/address'
			),
		);

	}

	function get_predicate() {

		return array_filter( array_map( function ( $item ) {
			return $item->get_predicate();
		}, $this->renditions ), function ( $item ) {
			return null !== $item;
		} );
	}

	function get_uri_suffix() {

		return array_filter( array_map( function ( $item ) {
			return $item->get_uri_suffix();
		}, $this->renditions ), function ( $item ) {
			return null !== $item;
		} );
	}

}