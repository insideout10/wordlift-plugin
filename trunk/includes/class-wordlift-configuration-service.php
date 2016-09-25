<?php

/**
 * Get WordLift's configuration settings stored in WordPress database.
 *
 * @since 3.6.0
 */
class Wordlift_Configuration_Service {

	const ENTITY_BASE_PATH_KEY = 'wl_entity_base_path';

	/**
	 * The Wordlift_Configuration_Service's singleton instance.
	 *
	 * @since 3.6.0
	 *
	 * @access private
	 * @var \Wordlift_Configuration_Service $instance Wordlift_Configuration_Service's singleton instance.
	 */
	private static $instance;

	/**
	 * Create a Wordlift_Configuration_Service's instance.
	 *
	 * @since 3.6.0
	 */
	public function __construct() {

		self::$instance = $this;

	}

	/**
	 * Get the singleton instance.
	 *
	 * @since 3.6.0
	 *
	 * @return \Wordlift_Configuration_Service
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Get a configuration given the option name and a key. The option value is
	 * expected to be an array.
	 *
	 * @since 3.6.0
	 *
	 * @param string $option The option name.
	 * @param string $key A key in the option value array.
	 * @param string $default The default value in case the key is not found (by default an empty string).
	 *
	 * @return mixed The configuration value or the default value if not found.
	 */
	private function get( $option, $key, $default = '' ) {

		$options = get_option( $option, array() );

		return isset( $options[ $key ] ) ? $options[ $key ] : $default;
	}

	/**
	 * Get the entity base path, by default 'entity'.
	 *
	 * @since 3.6.0
	 *
	 * @return string The entity base path.
	 */
	public function get_entity_base_path() {

		return $this->get( 'wl_general_settings', self::ENTITY_BASE_PATH_KEY, 'entity' );
	}

}
