<?php
/**
 * @since 3.8.0
 * @package Wordlift
 */

/**
 * @since 3.8.0
 */
class Wordlift_Property_Entity_Reference {

	/**
	 * The URL associated with this entity reference.
	 *
	 * @since 3.8.0
	 * @access private
	 * @var string $url The URL associated with the entity reference.
	 */
	private $url;

	/**
	 * Create a Wordlift_Property_Entity_Reference instance with the provided URL.
	 *
	 * @since 3.8.0
	 *
	 * @param string $url The URL.
	 */
	public function __construct( $url ) {

		$this->url = $url;

	}

	/**
	 * Get the URL associated with this entity reference.
	 *
	 * @since 3.8.0
	 * @return string The URL associated with the entity reference.
	 */
	public function getURL() {

		return $this->url;
	}

}