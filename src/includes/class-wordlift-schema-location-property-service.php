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

		return 'Wl_Metabox_Field';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_metabox_label() {

		return 'Location(s)';
	}

	/**
	 * Get the schema:url value for the specified post/entity.
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array|NULL The schema:url value or NULL if not set.
	 * @since 3.7.0
	 */
	public function get( $post_id ) {

		// Get the schema:url values set in WP.
		$values = get_post_meta( $post_id, self::META_KEY, false );

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

}
