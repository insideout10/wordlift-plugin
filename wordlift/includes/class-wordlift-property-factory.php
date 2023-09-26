<?php
/**
 * Defines the WordLift Property factory.
 *
 * @since 3.7.0
 */

/**
 *
 * @since 3.7.0
 */
class Wordlift_Property_Factory {

	/**
	 * The default {@link Wordlift_Property_Service}.
	 *
	 * @since 3.7.0
	 * @access private
	 * @var \Wordlift_Property_Service $default_property_service The default {@link Wordlift_Property_Service}.
	 */
	private $default_property_service;

	private $property_services = array();

	/**
	 * Wordlift_Property_Factory constructor.
	 *
	 * @since 3.7.0
	 *
	 * @param \Wordlift_Property_Service $default_property_service
	 */
	public function __construct( $default_property_service ) {

		$this->default_property_service = $default_property_service;

	}

	/**
	 * Set the {@link Wordlift_Property_Service} which handles that meta key.
	 *
	 * @since 3.7.0
	 *
	 * @param string                     $meta_key WordPress' meta key.
	 * @param \Wordlift_Property_Service $property_service A {@link Wordlift_Property_Service} instance.
	 */
	public function register( $meta_key, $property_service ) {

		$this->property_services[ $meta_key ] = $property_service;

	}

	/**
	 * Get the {@link Wordlift_Property_Service} which handles the specified meta key.
	 *
	 * @since 3.7.0
	 *
	 * @param $meta_key
	 *
	 * @return \Wordlift_Property_Service The {@link Wordlift_Property_Service} which handles the specified meta key.
	 */
	public function get( $meta_key ) {

		$service = $this->property_services[ $meta_key ];

		return $service ? $service : $this->default_property_service;
	}

}
