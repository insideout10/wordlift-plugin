<?php

/**
 * The property service provides access to entities properties values. Each entity
 * type has a list of custom fields which map to WP's meta. The property service
 * maps meta keys to property services.
 *
 * @since 3.8.0
 */
class Wordlift_Property_Getter {

	/**
	 * An array of {@link Wordlift_Simple_Property_Service}s which can access a
	 * property.
	 *
	 * @since 3.8.0
	 * @access private
	 * @var Wordlift_Simple_Property_Service[] $services An array of {@link Wordlift_Simple_Property_Service}s.
	 */
	private $services = array();

	/**
	 * The default {@link Wordlift_Simple_Property_Service} which is used to access
	 * a property when no specific {@link Wordlift_Simple_Property_Service} is found
	 * in the {@see $services} array.
	 * @var Wordlift_Simple_Property_Service
	 */
	private $default;

	/**
	 * Create a property service with the provided {@link Wordlift_Simple_Property_Service}
	 * as default.
	 *
	 * @since 3.8.0
	 *
	 * @param $default
	 */
	public function __construct( $default ) {

		$this->default = $default;

	}

	/**
	 * Register a {@link Wordlift_Simple_Property_Service} for the specified meta keys.
	 *
	 * @since 3.8.0
	 *
	 * @param \Wordlift_Simple_Property_Service $property_service A {@link Wordlift_Simple_Property_Service} instance.
	 * @param array $meta_keys An array of meta keys that the provided {@link Wordlift_Simple_Property_Service} will handle.
	 */
	public function register( $property_service, $meta_keys ) {

		// Register the specified property service for each meta key.
		foreach ( $meta_keys as $meta_key ) {
			$this->services[ $meta_key ] = $property_service;
		}

	}

	/**
	 * Get the value for the specified entity post id and WP's meta key.
	 *
	 * @since 3.8.0
	 *
	 * @param int $post_id The post id.
	 * @param string $meta_key The meta key.
	 *
	 * @return mixed|null The property value or null.
	 */
	public function get( $post_id, $meta_key ) {

		return isset( $this->services[ $meta_key ] )
			// Use a specific property service.
			? $this->services[ $meta_key ]->get( $post_id, $meta_key )
			// Use the default property service.
			: $this->default->get( $post_id, $meta_key );
	}

}
