<?php
/**
 * Properties: Entity Reference.
 *
 * @since   3.8.0
 * @package Wordlift
 */

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
	 * Create a Wordlift_Property_Entity_Reference instance with the provided URL.
	 *
	 * @param string $url The URL.
	 * @param int $id The entity post id.
	 * @param bool $required Whether this property is always required in SD output, default false.
	 *
	 * @since 3.8.0
	 */
	public function __construct( $url, $id, $required = false ) {

		$this->url      = $url;
		$this->id       = $id;
		$this->required = $required;

	}

	/**
	 * Get the URL associated with this entity reference.
	 *
	 * @return string The URL associated with the entity reference.
	 * @since 3.8.0
	 *
	 */
	public function get_url() {

		return $this->url;
	}

	/**
	 * Get the entity id.
	 *
	 * @return int The entity id.
	 * @since 3.10.0
	 *
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

}
