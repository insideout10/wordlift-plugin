<?php
/**
 * Properties: Entity Reference.
 *
 * @since   3.8.0
 * @package Wordlift
 */

use Wordlift\Jsonld\Post_Reference;
use Wordlift\Jsonld\Term_Reference;
use Wordlift\Object_Type_Enum;

/**
 * An entity reference property.
 *
 * @since   3.10.0 Exposes also the entity id.
 * @since   3.8.0
 *
 * @package Wordlift
 */
class Wordlift_Property_Entity_Reference {

	/**
	 * The URL associated with this entity reference.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var string $url The URL associated with the entity reference.
	 */
	private $url;

	/**
	 * The entity post id.
	 *
	 * @since  3.10.0
	 * @access private
	 * @var int $id The entity post id.
	 */
	private $id;

	private $required;

	/**
	 * @var int
	 */
	private $type;

	/**
	 * Create a Wordlift_Property_Entity_Reference instance with the provided URL.
	 *
	 * @param string $entity_uri The URL.
	 * @param int    $id The entity post id.
	 * @param bool   $required Whether this property is always required in SD output, default false.
	 * @param int    $type Instance of Object_Enum_Type
	 *
	 * @since 3.8.0
	 */
	public function __construct( $entity_uri, $id, $required = false, $type = Object_Type_Enum::POST ) {

		$this->url      = $entity_uri;
		$this->id       = $id;
		$this->required = $required;
		$this->type     = $type;

	}

	/**
	 * Return the type of the reference, one of the values in Object_Type_Enum
	 *
	 * @return int
	 * @since 3.38.0
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Get the URL associated with this entity reference.
	 *
	 * @return string The URL associated with the entity reference.
	 * @since 3.8.0
	 */
	public function get_url() {

		return $this->url;
	}

	/**
	 * Get the entity id.
	 *
	 * @return int The entity id.
	 * @since 3.10.0
	 */
	public function get_id() {

		return $this->id;
	}

	/**
	 * Get the required flag for this {@link Wordlift_Property_Entity_Reference}.
	 *
	 * The required flag may tell converters or consumers (like {@link \Wordlift\Jsonld\Jsonld_Service} that this
	 * property needs to be output to SD (JSON-LD).
	 *
	 * @since 3.27.1
	 */
	public function get_required() {

		return $this->required;

	}

	/**
	 * Set the required flag for this {@link Wordlift_Property_Entity_Reference}.
	 *
	 * The required flag may tell converters or consumers (like {@link \Wordlift\Jsonld\Jsonld_Service} that this
	 * property needs to be output to SD (JSON-LD).
	 *
	 * @param bool $value
	 *
	 * @since 3.27.1
	 */
	public function set_required( $value ) {

		$this->required = $value;

	}

	public function to_reference() {

		if ( Object_Type_Enum::POST === $this->type ) {
			return new Post_Reference( $this->id );
		}
		if ( Object_Type_Enum::TERM === $this->type ) {
			return new Term_Reference( $this->id );
		}
	}

}
