<?php

/**
 * This file defines a Property Service class for the schema:url property. Ideally
 * this class should extend an abstract class that provides a common infrastructure
 * for all properties (we'll get there).
 */

/**
 * The Wordlift_Schema_Location_Property_Service provides handling of the
 *
 * @since 3.7.0
 */
class Wordlift_Schema_Location_Property_Service extends Wordlift_Property_Service {

	/**
	 * The meta key used to store data for this property. We don't use wl_url to
	 * avoid potential confusion about other URLs.
	 *
	 * @since 3.7.0
	 */
	const META_KEY = 'wl_location';

	/**
	 * {@inheritdoc}
	 */
	public function get_rdf_predicate() {

		return 'http://schema.org/location';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_rdf_data_type() {

		return 'xsd:anyURI';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_data_type() {

		return Wordlift_Schema_Service::DATA_TYPE_URI;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_cardinality() {

		return INF;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_metabox_class() {

		return 'WL_Metabox_Field';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_metabox_label() {

		return 'Location(s)';
	}

	/**
	 * @var
	 */
	private $sparql_service;

	/**
	 * Create a Wordlift_Schema_Location_Property_Service instance.
	 * @since 3.7.0
	 *
	 * @param Wordlift_Sparql_Service $sparql_service
	 */
	public function __construct( $sparql_service ) {
		parent::__construct();

		$this->sparql_service = $sparql_service;
	}

	/**
	 * Get the schema:url value for the specified post/entity.
	 *
	 * @since 3.7.0
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array|NULL The schema:url value or NULL if not set.
	 */
	public function get( $post_id ) {

		// Get the schema:url values set in WP.
		$values = get_post_meta( $post_id, self::META_KEY, FALSE );

		// Finally return whatever values the editor set.
		return $values;
	}

	/**
	 * {@inheritdoc}
	 */
	public function sanitize( $value ) {

		// TODO: check that it's an URL or that is <permalink>

		return $value;
	}

	/**
	 * Generate an insert query that inserts the schema:url values for the specified
	 * post.
	 *
	 * @since 3.7.0
	 *
	 * @param string $s The subject URI.
	 * @param int $post_id The post id.
	 *
	 * @return string The insert query or an empty string.
	 */
	public function get_insert_query( $s, $post_id ) {

		// If we have no value, return an empty string (no query).
		if ( NULL === ( $values = $this->get( $post_id ) ) ) {
			return '';
		}

		// Create the insert query.
		$q = Wordlift_Query_Builder::new_instance()->insert();

		// Add each schema:url, replacing <permalink> with the actual post permalink.
		// TODO
		foreach ( $values as $value ) {
			$q = $q->statement( $s, $this->get_rdf_predicate(), $value, Wordlift_Query_Builder::OBJECT_URI );
		}

		// Build and return the query.
		return $q->build();
	}

}
