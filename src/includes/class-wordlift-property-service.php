<?php

/**
 * Define the Wordlift_Property_Service abstract class.
 *
 * @since 3.6.0
 */

/**
 * Wordlift_Property_Service provides basic functions and declarations for
 * properties that extend WL's schema.
 *
 * @since 3.6.0
 */
abstract class Wordlift_Property_Service {

	protected static $instance;

	public function __construct() {

		static::$instance = $this;
	}

	/**
	 * Get the field singleton.
	 *
	 * @since 3.6.0
	 * @return \Wordlift_Schema_Url_Property_Service The singleton instance.
	 */
	public static function get_instance() {

		return static::$instance;
	}

	/**
	 * Get the value for the specified post/entity.
	 *
	 * @since 3.6.0
	 *
	 * @param int $post_id The post id.
	 *
	 * @return mixed
	 */
	abstract public function get( $post_id );

	/**
	 * Sanitize the provided value.
	 *
	 * @since 3.6.0
	 *
	 * @param mixed $value The value to sanitize.
	 *
	 * @return mixed|NULL The sanitized value or NULL avoid saving this value (see {@link Wl_Metabox_Field}).
	 */
	abstract public function sanitize( $value );

	/**
	 * The RDF predicate for the property.
	 *
	 * @since 3.6.0
	 * @return string The RDF predicate.
	 */
	abstract public function get_rdf_predicate();

	/**
	 * The RDF data type.
	 *
	 * @since 3.6.0
	 * @return string The RDF data type.
	 */
	abstract public function get_rdf_data_type();

	/**
	 * The internal data type.
	 *
	 * @since 3.6.0
	 * @return string The internal data type.
	 */
	abstract public function get_data_type();

	/**
	 * The cardinality.
	 *
	 * @since 3.6.0
	 * @return mixed The cardinality.
	 */
	abstract public function get_cardinality();

	/**
	 * The metabox field class name.
	 *
	 * @since 3.6.0
	 * @return string The metabox field class name.
	 */
	abstract public function get_metabox_class();

	/**
	 * The untranslated metabox field label.
	 *
	 * @since 3.6.0
	 * @return string The untranslated metabox field label.
	 */
	abstract public function get_metabox_label();

	/**
	 * The definition of the property returned as a compatible array.
	 *
	 * @deprecated
	 *
	 * @since 3.6.0
	 * @return array An array of property definitions.
	 */
	public function get_compat_definition() {

		return array(
			'type'        => $this->get_data_type(),
			'predicate'   => $this->get_rdf_predicate(),
			'export_type' => $this->get_rdf_data_type(),
			'constraints' => array(
				'cardinality' => $this->get_cardinality(),
			),
			// Use the standard metabox for these URI (the URI metabox creates local entities).
			'metabox'     => array(
				'class' => $this->get_metabox_class(),
				'label' => $this->get_metabox_label(),
			),
			'sanitize'    => array( $this, 'sanitize' ),
		);
	}

}
