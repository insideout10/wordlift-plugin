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

	/**
	 * Create a Wordlift_Property_Entity_Reference instance with the provided URL.
	 *
	 * @since 3.8.0
	 *
	 * @param string $url The URL.
	 * @param int    $id  The entity post id.
	 */
	public function __construct( $url, $id ) {

		$this->url = $url;
		$this->id  = $id;

	}

	/**
	 * Get the URL associated with this entity reference.
	 *
	 * @since 3.8.0
	 *
	 * @return string The URL associated with the entity reference.
	 */
	public function getURL() {

		return $this->url;
	}

	/**
	 * Get the entity id.
	 *
	 * @since 3.10.0
	 *
	 * @return int The entity id.
	 */
	public function getID() {

		return $this->id;
	}

}
