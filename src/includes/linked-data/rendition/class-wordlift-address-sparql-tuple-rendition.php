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
	 * @since 3.18.0
	 * @access private
	 * @var array $renditions The PostalAddress entity renditions.
	 */
	private $renditions;

	/**
	 * Create a {@link Wordlift_Address_Sparql_Tuple_Rendition} instance.
	 *
	 * @since 3.18.0
	 *
	 * @param \Wordlift_Sparql_Tuple_Rendition_Factory $rendition_factory The {@link Wordlift_Sparql_Tuple_Rendition_Factory}
	 *                                                                    instance.
	 * @param \Wordlift_Storage                        $storage           The {@link Wordlift_Storage}
	 *                                                                    instance.
	 * @param string|null                              $language          The language code or null.
	 */
	function __construct( $rendition_factory, $storage, $language_code ) {

		$this->renditions = array(
			// ### schema:streetAddress.
			$rendition_factory->create(
				$storage->post_meta( Wordlift_Schema_Service::FIELD_ADDRESS ),
				Wordlift_Query_Builder::SCHEMA_STREET_ADDRESS,
				null,
				$language_code,
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
				$language_code,
				'/address'
			),

			// ### schema:addressRegion.
			$rendition_factory->create(
				$storage->post_meta( Wordlift_Schema_Service::FIELD_ADDRESS_REGION ),
				'http://schema.org/addressRegion',
				null,
				$language_code,
				'/address'
			),

			// ### schema:addressCountry.
			$rendition_factory->create(
				$storage->post_meta( Wordlift_Schema_Service::FIELD_ADDRESS_COUNTRY ),
				'http://schema.org/addressCountry',
				null,
				$language_code,
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
	 * @return array An array of tuples.
	 */
	public function get( $post_id ) {
		$tuples = array();

		/** @var Wordlift_Sparql_Tuple_Rendition $rendition */
		foreach ( $this->renditions as $rendition ) {
			$tuples = array_merge( $tuples, $rendition->get( $post_id ) );
		}

		// Add a reference to the main entity if the tuples are not empty.
		if ( ! empty( $tuples ) ) {
			// Get the main entity uri.
			$post_uri = Wordlift_Entity_Service::get_instance()->get_uri( $post_id );

			// Push the reference.
			$tuples[] = "<$post_uri> <http://schema.org/address> <$post_uri/address> . ";
			$tuples[] = "<$post_uri/address> a <http://schema.org/PostalAddress> . ";
		};

		// Finally return the tuples.
		return $tuples;
	}

	/**
	 * Get the delete statement for current post id.
	 *
	 * @since 3.18.0
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array An array containing delete statements for both
	 * 				 the uri as subject and object.
	 */

	// @@todo: change to `get_delete_triples`.
	public function get_delete_statement( $post_id ) {
		$deletes = array();

		// Get the main entity uri.
		$post_uri = Wordlift_Entity_Service::get_instance()->get_uri( $post_id );

		// Loop through all renditions and generate the delete statements.
		foreach ( $this->renditions as $rendition ) {
			// Get the entity URI.
			$deletes = array_merge( $deletes, $rendition->get_delete_statement( $post_id ) );
		}

		// Finally merge the deletes with main entity address delete statement.
		return array_merge(
			$deletes,
			array(
				// The delete statements with the entity as subject.
				Wordlift_Query_Builder::new_instance()
					->delete()
					->statement( $post_uri, 'http://schema.org/address', '?o' )
					->build(),
				// The delete statements with the entity as object.
				Wordlift_Query_Builder::new_instance()
					->delete()
					->statement( '?s', 'http://schema.org/address', $post_uri, Wordlift_Query_Builder::OBJECT_URI )
					->build(),
			)
		);
	}
}