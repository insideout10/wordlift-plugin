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

	/**
	 * The entity base path option name.
	 *
	 * @since 3.6.0
	 */
	const ENTITY_BASE_PATH_KEY = 'wl_entity_base_path';

	/**
	 * The skip wizard (admin installation wizard) option name.
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
	 * WordLift's configured language option name.
	 *
	 * @since 3.9.0
	 */
	const LANGUAGE = 'site_language';

	/**
	 * The publisher entity post ID option name.
	 *
	 * @since 3.9.0
	 */
	const PUBLISHER_ID = 'publisher_id';

	/**
	 * The dataset URI option name
	 *
	 * @since 3.10.0
	 */
	const DATASET_URI = 'redlink_dataset_uri';

	/**
	 * The link by default option name.
	 *
	 * @since 3.11.0
	 */
	const LINK_BY_DEFAULT = 'link_by_default';

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
	 * Get the entity base path.
	 *
	 * @since 3.9.0
	 *
	 * @param string $value The entity base path.
	 */
	public function set_entity_base_path( $value ) {

		$this->set( 'wl_general_settings', self::ENTITY_BASE_PATH_KEY, $value );
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

		$this->set( 'wl_general_settings', self::SKIP_WIZARD, true === $value );

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

	/**
	 * Set WordLift's key.
	 *
	 * @since 3.9.0
	 *
	 * @param string $value WordLift's key.
	 */
	public function set_key( $value ) {

		$this->set( 'wl_general_settings', self::KEY, $value );
	}

	/**
	 * Get WordLift's configured language, by default 'en'.
	 *
	 * Note that WordLift's language is used when writing strings to the Linked Data dataset, not for the analysis.
	 *
	 * @since 3.9.0
	 *
	 * @return string WordLift's configured language code ('en' by default).
	 */
	public function get_language_code() {

		return $this->get( 'wl_general_settings', self::LANGUAGE, 'en' );
	}

	/**
	 * Set WordLift's language code, used when storing strings to the Linked Data dataset.
	 *
	 * @since 3.9.0
	 *
	 * @param string $value WordLift's language code.
	 */
	public function set_language_code( $value ) {

		$this->set( 'wl_general_settings', self::LANGUAGE, $value );

	}

	/**
	 * Get the publisher entity post id.
	 *
	 * The publisher entity post id points to an entity post which contains the data for the publisher used in schema.org
	 * Article markup.
	 *
	 * @since 3.9.0
	 *
	 * @return int|NULL The publisher entity post id or NULL if not set.
	 */
	public function get_publisher_id() {

		return $this->get( 'wl_general_settings', self::PUBLISHER_ID, null );
	}

	/**
	 * Set the publisher entity post id.
	 *
	 * @since 3.9.0
	 *
	 * @param int $value The publisher entity post id.
	 */
	public function set_publisher_id( $value ) {

		$this->set( 'wl_general_settings', self::PUBLISHER_ID, $value );

	}

	/**
	 * Get the dataset URI.
	 *
	 * @since 3.10.0
	 *
	 * @return string The dataset URI or an empty string if not set.
	 */
	public function get_dataset_uri() {

		return $this->get( 'wl_advanced_settings', self::DATASET_URI, null );
	}

	/**
	 * Set the dataset URI.
	 *
	 * @since 3.10.0
	 *
	 * @param string $value The dataset URI.
	 */
	public function set_dataset_uri( $value ) {

		$this->set( 'wl_advanced_settings', self::DATASET_URI, $value );
	}

	/**
	 * Intercept the change of the WordLift key in order to set the dataset URI.
	 *
	 * @since 3.11.0
	 *
	 * @param array $old_value The old settings.
	 * @param array $new_value The new settings.
	 */
	public function update_key( $old_value, $new_value ) {

		// Check the old key value and the new one. We're going to ask for the dataset URI only if the key has changed.
		$old_key = isset( $old_value['key'] ) ? $old_value['key'] : '';
		$new_key = isset( $new_value['key'] ) ? $new_value['key'] : '';

		// If the key hasn't changed, don't do anything.
		// WARN The 'update_option' hook is fired only if the new and old value are not equal
		if ( $old_key === $new_key ) {
			return;
		}

		// If the key is empty, empty the dataset URI.
		if ( '' === $new_key ) {
			$this->set_dataset_uri( '' );
		}

		// make the request to the remote server
		$this->get_remote_dataset_uri( $new_key );
	}

	/**
	 * Handle retrieving the dataset uri from the remote server.
	 *
	 * If a valid dataset uri is returned it is stored in the appropriate option,
	 * otherwise the option is set to empty string.
	 *
	 * @since 3.12.0
	 *
	 * @param string $key The key to be used
	 *
	 */
	private function get_remote_dataset_uri( $key ) {
		// Request the dataset URI.
		$response = wp_remote_get( $this->get_accounts_by_key_dataset_uri( $key ), unserialize( WL_REDLINK_API_HTTP_OPTIONS ) );

		// If the response is valid, then set the value.
		if ( ! is_wp_error( $response ) && 200 === (int) $response['response']['code'] ) {
			$this->set_dataset_uri( $response['body'] );
		} else {
			$this->set_dataset_uri( '' );
		}
	}

	/**
	 * Handle the edge case where a user submits the same key again
	 * when he does not have the dataset uri to regain it.
	 *
	 * This can not be handled in the normal option update hook because
	 * it is not being triggered when the save value equals to the one already
	 * in the DB.
	 *
	 * @since 3.12.0
	 *
	 * @param mixed $value     The new, unserialized option value.
	 * @param mixed $old_value The old option value.
	 *
	 * @return mixed The same value in the $value parameter
	 *
	 */
	function maybe_update_dataset_uri( $value, $old_value ) {

		$dataset_uri = $this->get_dataset_uri();

		if ( ! empty( $value ) && $value == $old_value && empty( $dataset_uri ) ) {

			// make the request to the remote server to try to get the dataset uri
			$this->get_remote_dataset_uri( $value );
		}

		return $value;
	}

	/**
	 * Get the API URI to retrieve the dataset URI using the WordLift Key.
	 *
	 * @since 3.11.0
	 *
	 * @param string $key The WordLift key to use.
	 *
	 * @return string The API URI.
	 */
	public function get_accounts_by_key_dataset_uri( $key ) {

		return WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE . "accounts/key=$key/dataset_uri";
	}

	/**
	 * Get the `link by default` option.
	 *
	 * @since 3.13.0
	 *
	 * @return bool True if entities must be linked by default otherwise false.
	 */
	public function is_link_by_default() {

		return 'yes' === $this->get( 'wl_general_settings', self::LINK_BY_DEFAULT, 'yes' );
	}

	/**
	 * Set the `link by default` option.
	 *
	 * @since 3.13.0
	 *
	 * @param bool $value True to enabling linking by default, otherwise false.
	 */
	public function set_link_by_default( $value ) {

		$this->set( 'wl_general_settings', self::LINK_BY_DEFAULT, true === $value ? 'yes' : 'no' );
	}

}
