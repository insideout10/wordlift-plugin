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

	protected $params;

	// TODO: check that this is relative to the extending class.
	protected static $instance;

	public function __construct() {
		// Add a reference to the validation function.
		$this->params['sanitize'] = array( $this, 'sanitize' );

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
	public abstract function get( $post_id );

	/**
	 * Sanitize the provided value.
	 *
	 * @since 3.6.0
	 *
	 * @param mixed $value The value to sanitize.
	 *
	 * @return mixed|NULL The sanitized value or NULL avoid saving this value (see {@link WL_Metabox_Field}).
	 */
	public abstract function sanitize( $value );

	/**
	 * Get the field parameters.
	 *
	 * @since 3.6.0
	 * @return array An array of parameters.
	 */
	public function get_params() {

		return $this->params;
	}

}
