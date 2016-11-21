<?php
/**
 * Wordlift_Configuration_Service class.
 *
 * The {@link Wordlift_Configuration_Service} class provides helper functions to get configuration parameter values.
 *
 * @link       https://wordlift.io
 *
 * @package    Wordlift
 * @since      3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get WordLift's configuration settings stored in WordPress database.
 *
 * @since 3.6.0
 */
class Wordlift_Configuration_Service {

	const ENTITY_BASE_PATH_KEY = 'wl_entity_base_path';

	/**
	 * The skip wizard (admin installation wizard) option key.
	 *
	 * @since 3.9.0
	 */
	const SKIP_WIZARD = 'wl_skip_wizard';

	/**
	 * WordLift's key option name.
	 *
	 * @since 3.9.0
	 */
	const KEY = 'key';

	/**
	 * The Wordlift_Configuration_Service's singleton instance.
	 *
	 * @since  3.6.0
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
	 * @param string $option  The option name.
	 * @param string $key     A key in the option value array.
	 * @param string $default The default value in case the key is not found (by default an empty string).
	 *
	 * @return mixed The configuration value or the default value if not found.
	 */
	private function get( $option, $key, $default = '' ) {

		$options = get_option( $option, array() );

		return isset( $options[ $key ] ) ? $options[ $key ] : $default;
	}

	/**
	 * Set a configuration parameter.
	 *
	 * @since 3.9.0
	 *
	 * @param string $option Name of option to retrieve. Expected to not be SQL-escaped.
	 * @param string $key    The value key.
	 * @param mixed  $value  The value.
	 */
	private function set( $option, $key, $value ) {

		$values         = get_option( $option );
		$values         = isset( $values ) ? $values : array();
		$values[ $key ] = $value;
		update_option( $option, $values );

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

	/**
	 * Whether the installation skip wizard should be skipped.
	 *
	 * @since 3.9.0
	 *
	 * @return bool True if it should be skipped otherwise false.
	 */
	public function is_skip_wizard() {

		return $this->get( 'wl_general_settings', self::SKIP_WIZARD, false );
	}

	/**
	 * Set the skip wizard parameter.
	 *
	 * @since 3.9.0
	 *
	 * @param bool $value True to skip the wizard. We expect a boolean value.
	 */
	public function set_skip_wizard( $value ) {

		$this->set( 'wl_general_settings', self::SKIP_WIZARD, $value === true );

	}

	/**
	 * Get WordLift's key.
	 *
	 * @since 3.9.0
	 *
	 * @return WordLift's key or an empty string if not set.
	 */
	public function get_key() {

		return $this->get( 'wl_general_settings', self::KEY, '' );
	}

}
