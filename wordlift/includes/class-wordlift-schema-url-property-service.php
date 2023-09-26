<?php

/**
 * This file defines a Property Service class for the schema:url property. Ideally
 * this class should extend an abstract class that provides a common infrastructure
 * for all properties (we'll get there).
 */

/**
 * The Wordlift_Schema_Url_Property_Service provides handling of the
 *
 * @since 3.6.0
 */
class Wordlift_Schema_Url_Property_Service extends Wordlift_Property_Service {

	/**
	 * The meta key used to store data for this property. We don't use wl_url to
	 * avoid potential confusion about other URLs.
	 *
	 * @since 3.6.0
	 */
	const META_KEY = 'wl_schema_url';

	/**
	 * {@inheritdoc}
	 */
	public function get_rdf_predicate() {

		return 'http://schema.org/url';
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

		return __( 'Web Site(s)', 'wordlift' );
	}

	/**
	 * Create a Wordlift_Schema_Url_Property_Service instance.
	 *
	 * @since 3.6.0
	 */
	public function __construct() {
		parent::__construct();

		// Finally listen for metadata requests for this field.
		$this->add_filter_get_post_metadata();
	}

	/**
	 * Get the schema:url value for the specified post/entity.
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array|NULL The schema:url value or NULL if not set.
	 * @since 3.6.0
	 */
	public function get( $post_id ) {

		// Get the schema:url values set in WP.
		$values = get_post_meta( $post_id, self::META_KEY, false );

		// If the property has never been set, we set its default value the first
		// time to <permalink>.
		if ( empty( $values ) ) {
			return array( '<permalink>' );
		}

		// If there's only one value and that value is empty, we return NULL, i.e.
		// variable not set.
		if ( 1 === count( $values ) && empty( $values[0] ) ) {
			return null;
		}

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
	 * Get direct calls to read this meta and alter the response according to our
	 * own strategy, i.e. if a value has never been set for this meta, then return
	 * <permalink>.
	 *
	 * @param mixed  $value The original value.
	 * @param int    $object_id The post id.
	 * @param string $meta_key The meta key. We expect wl_schema_url or we return straight the value.
	 * @param bool   $single Whether to return a single value.
	 *
	 * @return array|mixed|NULL|string
	 * @since 3.6.0
	 */
	public function get_post_metadata( $value, $object_id, $meta_key, $single ) {

		// It's not us, return the value.
		if ( self::META_KEY !== $meta_key ) {
			return $value;
		}

		$this->remove_filter_get_post_metadata();

		$new_value = $this->get( $object_id );

		$this->add_filter_get_post_metadata();

		// If we must return a single value, but we don't have a value, then return an empty string.
		if ( $single && ( $new_value === null || empty( $new_value ) ) ) {
			return '';
		}

		// If we have a value and we need to return it as single, return the first value.
		if ( $single ) {
			return $new_value[0];
		}

		// Otherwise return the array.
		return $new_value;
	}

	private function add_filter_get_post_metadata() {

		add_filter(
			'get_post_metadata',
			array(
				$this,
				'get_post_metadata',
			),
			10,
			4
		);

	}

	private function remove_filter_get_post_metadata() {

		remove_filter(
			'get_post_metadata',
			array(
				$this,
				'get_post_metadata',
			),
			10
		);

	}

}
