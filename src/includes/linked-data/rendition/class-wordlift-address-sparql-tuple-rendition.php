<?php
/**
 * Renditions: Sparql Tuple Rendition.
 *
 * Renders a property (accessed using a {@link Wordlift_Storage} instance) to
 * a tuple for use in SPARQL statements.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_Sparql_Tuple_Rendition} class.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_Address_Sparql_Tuple_Rendition implements Wordlift_Sparql_Tuple_Rendition {
	/**
	 * The PostalAddress entity renditions.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var array $renditions The PostalAddress entity renditions.
	 */
	private $renditions;

	/**
	 * The {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * Create a {@link Wordlift_Address_Sparql_Tuple_Rendition} instance.
	 *
	 * @since 3.18.0
	 *
	 * @param \Wordlift_Entity_Service                 $entity_service    The {@link Wordlift_Entity_Service}.
	 *
	 * @param \Wordlift_Sparql_Tuple_Rendition_Factory $rendition_factory The {@link Wordlift_Sparql_Tuple_Rendition_Factory}.
	 *                                                                    instance.
	 * @param \Wordlift_Storage                        $storage           The {@link Wordlift_Storage}
	 *                                                                    instance.
	 * @param string|null                              $language          The language code or null.
	 */
	public function __construct( $entity_service, $rendition_factory, $storage, $language ) {

		$this->entity_service = $entity_service;

		$this->renditions = array(
			// ### schema:streetAddress.
			$rendition_factory->create(
				$storage->post_meta( Wordlift_Schema_Service::FIELD_ADDRESS ),
				Wordlift_Query_Builder::SCHEMA_STREET_ADDRESS,
				null,
				$language,
				'/address'
			),
			// ### schema:postOfficeBoxNumber.
			$rendition_factory->create(
				$storage->post_meta( Wordlift_Schema_Service::FIELD_ADDRESS_PO_BOX ),
				'http://schema.org/postOfficeBoxNumber',
				null,
				null,
				'/address'
			),

			// ### schema:postalCode.
			$rendition_factory->create(
				$storage->post_meta( Wordlift_Schema_Service::FIELD_ADDRESS_POSTAL_CODE ),
				'http://schema.org/postalCode',
				null,
				null,
				'/address'
			),

			// ### schema:addressLocality.
			$rendition_factory->create(
				$storage->post_meta( Wordlift_Schema_Service::FIELD_ADDRESS_LOCALITY ),
				'http://schema.org/addressLocality',
				null,
				$language,
				'/address'
			),

			// ### schema:addressRegion.
			$rendition_factory->create(
				$storage->post_meta( Wordlift_Schema_Service::FIELD_ADDRESS_REGION ),
				'http://schema.org/addressRegion',
				null,
				$language,
				'/address'
			),

			// ### schema:addressCountry.
			$rendition_factory->create(
				$storage->post_meta( Wordlift_Schema_Service::FIELD_ADDRESS_COUNTRY ),
				'http://schema.org/addressCountry',
				null,
				$language,
				'/address'
			),
		);

	}
	
	/**
	 * Get tuple representations for the specified {@link WP_Post}.
	 *
	 * @since 3.18.0
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return array An array of triples.
	 */
	public function get_insert_triples( $post_id ) {
		$triples = array();

		/** @var Wordlift_Sparql_Tuple_Rendition $rendition */
		foreach ( $this->renditions as $rendition ) {
			$triples = array_merge( $triples, $rendition->get_insert_triples( $post_id ) );
		}

		// Add a reference to the main entity if the triples are not empty.
		if ( ! empty( $triples ) ) {
			// Get the main entity uri.
			$uri = $this->entity_service->get_uri( $post_id );

			// Push the reference.
			$triples[] = sprintf( '<%1$s> <%2$s> <%1$s/address> . ',
				Wordlift_Sparql_Service::escape_uri( $uri ),
				'http://schema.org/address'
			);

			$triples[] = sprintf( '<%s/address> a <%s> . ',
				Wordlift_Sparql_Service::escape_uri( $uri ),
				'http://schema.org/PostalAddress'
			);
		};

		// Finally return the triples.
		return $triples;
	}

	/**
	 * Get the delete statement for current post id.
	 *
	 * @since 3.18.0
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array An array of delete triples for current tuple renditions.
	 */
	public function get_delete_triples( $post_id ) {
		$deletes = array();

		// Loop through all renditions and generate the delete statements.
		foreach ( $this->renditions as $rendition ) {
			// Generate delete triples for each rendition.
			$deletes = array_merge(
				$deletes,
				// Get the triple from current rendition.
				$rendition->get_delete_triples( $post_id )
			);
		}

		// Return the delete statements along with delete statements for
		// address reference and PostalAddress rdf:type.
		return array_merge( $deletes, $this->get_address_delete_triples( $post_id ) );
	}

	/**
	 * Provide delete triples for address reference
	 * and PostalAddress rdf:type.
	 *
	 * @since 3.18.0
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array An arary of additional delete triples.
	 */
	private function get_address_delete_triples( $post_id ) {
		// Get the main entity uri.
		$uri = $this->entity_service->get_uri( $post_id );

		// Build and return the address delete triples.
		return array(
			// Push the address reference.
			sprintf( '<%1$s> <%2$s> <%1$s/address> . ',
				Wordlift_Sparql_Service::escape_uri( $uri ),
				'http://schema.org/address'
			),

			// Push the delete PostalAddress rdf:type.
			sprintf( '<%s/address> a <%s> . ',
				Wordlift_Sparql_Service::escape_uri( $uri ),
				'http://schema.org/PostalAddress'
			),
		);

	}
}
