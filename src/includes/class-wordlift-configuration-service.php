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

use Wordlift\Api\Default_Api_Service;

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
	 * The skip installation notice.
	 *
	 * @since 3.40.3
	 */
	const SKIP_INSTALLATION_NOTICE = 'wl_skip_installation_notice';

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
	 * WordLift's configured country code.
	 *
	 * @since 3.18.0
	 */
	const COUNTRY_CODE = 'country_code';

	/**
	 * The alternateName option name.
	 *
	 * @since 3.38.6
	 */
	const ALTERNATE_NAME = 'wl-alternate-name';

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
	 * The analytics enable option.
	 *
	 * @since 3.21.0
	 */
	const ANALYTICS_ENABLE = 'analytics_enable';

	/**
	 * The analytics entity uri dimension option.
	 *
	 * @since 3.21.0
	 */
	const ANALYTICS_ENTITY_URI_DIMENSION = 'analytics_entity_uri_dimension';

	/**
	 * The analytics entity type dimension option.
	 *
	 * @since 3.21.0
	 */
	const ANALYTICS_ENTITY_TYPE_DIMENSION = 'analytics_entity_type_dimension';

	/**
	 * The user preferences about sharing data option.
	 *
	 * @since 3.19.0
	 */
	const SEND_DIAGNOSTIC = 'send_diagnostic';

	/**
	 * The package type configuration key.
	 *
	 * @since 3.20.0
	 */
	const PACKAGE_TYPE = 'package_type';
	/**
	 * The dataset ids connected to the current key
	 *
	 * @since 3.38.5
	 */
	const NETWORK_DATASET_IDS = 'network_dataset_ids';

	const OVERRIDE_WEBSITE_URL = 'wl-override-website-url';

	/**
	 * The {@link Wordlift_Log_Service} instance.
	 *
	 * @since 3.16.0
	 *
	 * @var \Wordlift_Log_Service $log The {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * Create a Wordlift_Configuration_Service's instance.
	 *
	 * @since 3.6.0
	 */
	protected function __construct() {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		// Sync some configuration properties when key is validated.
		add_action( 'wl_key_validation_response', array( $this, 'sync' ) );

	}

	/**
	 * @param $response \Wordlift\Api\Response
	 *
	 * @return void
	 */
	public function sync( $response ) {
		if ( ! $response->is_success() ) {
			return;
		}
		$data = json_decode( $response->get_body(), true );
		if ( ! is_array( $data ) || ! array_key_exists( 'networkDatasetId', $data ) ) {
			return;
		}
		$this->set_network_dataset_ids( $data['networkDatasetId'] );
	}

	/**
	 * The Wordlift_Configuration_Service's singleton instance.
	 *
	 * @since  3.6.0
	 *
	 * @access private
	 * @var \Wordlift_Configuration_Service $instance Wordlift_Configuration_Service's singleton instance.
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return \Wordlift_Configuration_Service
	 * @since 3.6.0
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get a configuration given the option name and a key. The option value is
	 * expected to be an array.
	 *
	 * @param string $option The option name.
	 * @param string $key A key in the option value array.
	 * @param string $default The default value in case the key is not found (by default an empty string).
	 *
	 * @return mixed The configuration value or the default value if not found.
	 * @since 3.6.0
	 */
	private function get( $option, $key, $default = '' ) {

		$options = get_option( $option, array() );

		return isset( $options[ $key ] ) ? $options[ $key ] : $default;
	}

	/**
	 * Set a configuration parameter.
	 *
	 * @param string $option Name of option to retrieve. Expected to not be SQL-escaped.
	 * @param string $key The value key.
	 * @param mixed  $value The value.
	 *
	 * @since 3.9.0
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
	 * @return string The entity base path.
	 * @since 3.6.0
	 */
	public function get_entity_base_path() {

		return $this->get( 'wl_general_settings', self::ENTITY_BASE_PATH_KEY, 'entity' );
	}

	/**
	 * Get the entity base path.
	 *
	 * @param string $value The entity base path.
	 *
	 * @since 3.9.0
	 */
	public function set_entity_base_path( $value ) {

		$this->set( 'wl_general_settings', self::ENTITY_BASE_PATH_KEY, $value );

	}

	/**
	 * Whether the installation skip wizard should be skipped.
	 *
	 * @return bool True if it should be skipped otherwise false.
	 * @since 3.9.0
	 */
	public function is_skip_wizard() {

		return $this->get( 'wl_general_settings', self::SKIP_WIZARD, false );
	}

	/**
	 * Set the skip wizard parameter.
	 *
	 * @param bool $value True to skip the wizard. We expect a boolean value.
	 *
	 * @since 3.9.0
	 */
	public function set_skip_wizard( $value ) {

		$this->set( 'wl_general_settings', self::SKIP_WIZARD, true === $value );

	}

	/**
	 * Get WordLift's key.
	 *
	 * @return string WordLift's key or an empty string if not set.
	 * @since 3.9.0
	 */
	public function get_key() {

		return $this->get( 'wl_general_settings', self::KEY, '' );
	}

	/**
	 * Set WordLift's key.
	 *
	 * @param string $value WordLift's key.
	 *
	 * @since 3.9.0
	 */
	public function set_key( $value ) {

		$this->set( 'wl_general_settings', self::KEY, $value );
	}

	/**
	 * Get WordLift's configured language, by default 'en'.
	 *
	 * Note that WordLift's language is used when writing strings to the Linked Data dataset, not for the analysis.
	 *
	 * @return string WordLift's configured language code ('en' by default).
	 * @since 3.9.0
	 */
	public function get_language_code() {

		$language = get_locale();
		if ( ! $language ) {
			return 'en';
		}

		return substr( $language, 0, 2 );
	}

	/**
	 * @param string $value WordLift's language code.
	 *
	 * @see https://github.com/insideout10/wordlift-plugin/issues/1466
	 *
	 * Set WordLift's language code, used when storing strings to the Linked Data dataset.
	 *
	 * @deprecated As of 3.32.7 this below method has no effect on setting the language, we use the
	 * language code form WordPress directly.
	 *
	 * @since 3.9.0
	 */
	public function set_language_code( $value ) {

		$this->set( 'wl_general_settings', self::LANGUAGE, $value );

	}

	/**
	 * Set the user preferences about sharing diagnostic with us.
	 *
	 * @param string $value The user preferences(yes/no).
	 *
	 * @since 3.19.0
	 */
	public function set_diagnostic_preferences( $value ) {

		$this->set( 'wl_general_settings', self::SEND_DIAGNOSTIC, $value );

	}

	/**
	 * Get the user preferences about sharing diagnostic.
	 *
	 * @since 3.19.0
	 */
	public function get_diagnostic_preferences() {

		return $this->get( 'wl_general_settings', self::SEND_DIAGNOSTIC, 'no' );
	}

	/**
	 * Get WordLift's configured country code, by default 'us'.
	 *
	 * @return string WordLift's configured country code ('us' by default).
	 * @since 3.18.0
	 */
	public function get_country_code() {

		return $this->get( 'wl_general_settings', self::COUNTRY_CODE, 'us' );
	}

	/**
	 * Set WordLift's country code.
	 *
	 * @param string $value WordLift's country code.
	 *
	 * @since 3.18.0
	 */
	public function set_country_code( $value ) {

		$this->set( 'wl_general_settings', self::COUNTRY_CODE, $value );

	}

	/**
	 * Get the alternateName.
	 *
	 * Website markup alternateName
	 *
	 * @return string|NULL alternateName or NULL if not set.
	 * @since 3.38.6
	 */
	public function get_alternate_name() {
		return $this->get( 'wl_general_settings', self::ALTERNATE_NAME );
	}

	/**
	 * Set the alternateName.
	 *
	 * @param int $value The alternateName value.
	 *
	 * @since 3.38.6
	 */
	public function set_alternate_name( $value ) {

		$this->set( 'wl_general_settings', self::ALTERNATE_NAME, wp_strip_all_tags( $value ) );

	}

	/**
	 * Get the publisher entity post id.
	 *
	 * The publisher entity post id points to an entity post which contains the data for the publisher used in schema.org
	 * Article markup.
	 *
	 * @return int|NULL The publisher entity post id or NULL if not set.
	 * @since 3.9.0
	 */
	public function get_publisher_id() {

		return $this->get( 'wl_general_settings', self::PUBLISHER_ID, null );
	}

	/**
	 * Set the publisher entity post id.
	 *
	 * @param int $value The publisher entity post id.
	 *
	 * @since 3.9.0
	 */
	public function set_publisher_id( $value ) {

		$this->set( 'wl_general_settings', self::PUBLISHER_ID, $value );

	}

	/**
	 * Get the dataset URI.
	 *
	 * @return string The dataset URI or an empty string if not set.
	 * @since 3.10.0
	 * @since 3.27.7 Always return null if `wl_features__enable__dataset` is disabled.
	 */
	public function get_dataset_uri() {

		if ( apply_filters( 'wl_feature__enable__dataset', true ) ) {
			return $this->get( 'wl_advanced_settings', self::DATASET_URI, null );
		} else {
			return null;
		}
	}

	/**
	 * Set the dataset URI.
	 *
	 * @param string $value The dataset URI.
	 *
	 * @since 3.10.0
	 */
	public function set_dataset_uri( $value ) {

		$this->set( 'wl_advanced_settings', self::DATASET_URI, $value );
	}

	/**
	 * Get the package type.
	 *
	 * @return string The package type or an empty string if not set.
	 * @since 3.20.0
	 */
	public function get_package_type() {

		return $this->get( 'wl_advanced_settings', self::PACKAGE_TYPE, null );
	}

	/**
	 * Set the package type.
	 *
	 * @param string $value The package type.
	 *
	 * @since 3.20.0
	 */
	public function set_package_type( $value ) {
		$this->set( 'wl_advanced_settings', self::PACKAGE_TYPE, $value );
	}

	/**
	 * Intercept the change of the WordLift key in order to set the dataset URI.
	 *
	 * @since 3.20.0 as of #761, we save settings every time a key is set, not only when the key changes, so to
	 *               store the configuration parameters such as country or language.
	 * @since 3.11.0
	 *
	 * @see https://github.com/insideout10/wordlift-plugin/issues/761
	 *
	 * @param array $old_value The old settings.
	 * @param array $new_value The new settings.
	 */
	public function update_key( $old_value, $new_value ) {

		// Check the old key value and the new one. We're going to ask for the dataset URI only if the key has changed.
		// $old_key = isset( $old_value['key'] ) ? $old_value['key'] : '';
		$new_key = isset( $new_value['key'] ) ? trim( $new_value['key'] ) : '';

		// If the key hasn't changed, don't do anything.
		// WARN The 'update_option' hook is fired only if the new and old value are not equal.
		// if ( $old_key === $new_key ) {
		// return;
		// }

		// If the key is empty, empty the dataset URI.
		if ( '' === $new_key ) {
			$this->set_dataset_uri( '' );
		}

		// make the request to the remote server.
		$this->get_remote_dataset_uri( $new_key );

		do_action( 'wl_key_updated' );

	}

	/**
	 * Handle retrieving the dataset uri from the remote server.
	 *
	 * If a valid dataset uri is returned it is stored in the appropriate option,
	 * otherwise the option is set to empty string.
	 *
	 * @param string $key The key to be used.
	 *
	 * @since 3.12.0
	 *
	 * @since 3.17.0 send the site URL and get the dataset URI.
	 */
	public function get_remote_dataset_uri( $key ) {

		$this->log->trace( 'Getting the remote dataset URI and package type...' );

		if ( empty( $key ) ) {
			$this->log->warn( 'Key set to empty value.' );

			$this->set_dataset_uri( '' );
			$this->set_package_type( null );

			return;
		}

		/**
		 * Allow 3rd parties to change the site_url.
		 *
		 * @param string $site_url The site url.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/850
		 *
		 * @since 3.20.0
		 */
		$home_url = get_option( 'home' );
		$site_url = apply_filters( 'wl_production_site_url', untrailingslashit( $home_url ) );

		// Build the URL.
		$url = '/accounts'
			   . '?key=' . rawurlencode( $key )
			   . '&url=' . rawurlencode( $site_url )
			   . '&country=' . $this->get_country_code()
			   . '&language=' . $this->get_language_code();

		$api_service = Default_Api_Service::get_instance();
		/**
		 * @since 3.27.7.1
		 * The Key should be passed to headers, otherwise api would return null.
		 */
		$headers  = array(
			'Authorization' => "Key $key",
		);
		$response = $api_service->request( 'PUT', $url, $headers )->get_response();

		// The response is an error.
		if ( is_wp_error( $response ) ) {
			$this->log->error( 'An error occurred setting the dataset URI: ' . $response->get_error_message() );

			$this->set_dataset_uri( '' );
			$this->set_package_type( null );

			return;
		}

		// The response is not OK.
		if ( ! is_array( $response ) || 200 !== (int) $response['response']['code'] ) {
			$base_url = $api_service->get_base_url();

			if ( ! is_array( $response ) ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export
				$this->log->error( "Unexpected response when opening URL $base_url$url: " . var_export( $response, true ) );
			} else {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export
				$this->log->error( "Unexpected status code when opening URL $base_url$url: " . $response['response']['code'] . "\n" . var_export( $response, true ) );
			}

			$this->set_dataset_uri( '' );
			$this->set_package_type( null );

			return;
		}

		/*
		 * We also store the package type.
		 *
		 * @since 3.20.0
		 */
		$json = json_decode( $response['body'] );
		/**
		 * @since 3.27.7
		 * Remove the trailing slash returned from the new platform api.
		 */
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$dataset_uri = untrailingslashit( $json->datasetURI );
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$package_type = isset( $json->packageType ) ? $json->packageType : null;

		$this->log->info( "Updating [ dataset uri :: $dataset_uri ][ package type :: $package_type ]..." );

		$this->set_dataset_uri( $dataset_uri );
		$this->set_package_type( $package_type );
	}

	/**
	 * Handle the edge case where a user submits the same key again
	 * when he does not have the dataset uri to regain it.
	 *
	 * This can not be handled in the normal option update hook because
	 * it is not being triggered when the save value equals to the one already
	 * in the DB.
	 *
	 * @param mixed $value The new, unserialized option value.
	 * @param mixed $old_value The old option value.
	 *
	 * @return mixed The same value in the $value parameter
	 * @since 3.12.0
	 */
	public function maybe_update_dataset_uri( $value, $old_value ) {

		// Check the old key value and the new one. Here we're only handling the
		// case where the key hasn't changed and the dataset URI isn't set. The
		// other case, i.e. a new key is inserted, is handled at `update_key`.
		$old_key = isset( $old_value['key'] ) ? $old_value['key'] : '';
		$new_key = isset( $value['key'] ) ? trim( $value['key'] ) : '';

		$dataset_uri = $this->get_dataset_uri();

		if ( ! empty( $new_key ) && $new_key === $old_key && empty( $dataset_uri ) ) {

			// make the request to the remote server to try to get the dataset uri.
			$this->get_remote_dataset_uri( $new_key );
		}

		return $value;
	}

	/**
	 * Get the API URI to retrieve the dataset URI using the WordLift Key.
	 *
	 * @param string $key The WordLift key to use.
	 *
	 * @return string The API URI.
	 * @since 3.11.0
	 */
	public function get_accounts_by_key_dataset_uri( $key ) {

		return WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE . "accounts/key=$key/dataset_uri";
	}

	/**
	 * Get the `accounts` end point.
	 *
	 * @return string The `accounts` end point.
	 * @since 3.16.0
	 */
	public function get_accounts() {

		return WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE . 'accounts';
	}

	/**
	 * Get the `link by default` option.
	 *
	 * @return bool True if entities must be linked by default otherwise false.
	 * @since 3.13.0
	 */
	public function is_link_by_default() {

		return 'yes' === $this->get( 'wl_general_settings', self::LINK_BY_DEFAULT, 'yes' );
	}

	/**
	 * Set the `link by default` option.
	 *
	 * @param bool $value True to enabling linking by default, otherwise false.
	 *
	 * @since 3.13.0
	 */
	public function set_link_by_default( $value ) {

		$this->set( 'wl_general_settings', self::LINK_BY_DEFAULT, true === $value ? 'yes' : 'no' );
	}

	/**
	 * Get the 'analytics-enable' option.
	 *
	 * @return string 'no' or 'yes' representing bool.
	 * @since 3.21.0
	 */
	public function is_analytics_enable() {
		return 'yes' === $this->get( 'wl_analytics_settings', self::ANALYTICS_ENABLE, 'no' );
	}

	/**
	 * Set the `analytics-enable` option.
	 *
	 * @param bool $value True to enabling analytics, otherwise false.
	 *
	 * @since 3.21.0
	 */
	public function set_is_analytics_enable( $value ) {

		$this->set( 'wl_general_settings', self::ANALYTICS_ENABLE, true === $value ? 'yes' : 'no' );
	}

	/**
	 * Get the 'analytics-entity-uri-dimention' option.
	 *
	 * @return int
	 * @since 3.21.0
	 */
	public function get_analytics_entity_uri_dimension() {
		return (int) $this->get( 'wl_analytics_settings', self::ANALYTICS_ENTITY_URI_DIMENSION, 1 );
	}

	/**
	 * Get the 'analytics-entity-type-dimension' option.
	 *
	 * @return int
	 * @since 3.21.0
	 */
	public function get_analytics_entity_type_dimension() {
		return $this->get( 'wl_analytics_settings', self::ANALYTICS_ENTITY_TYPE_DIMENSION, 2 );
	}

	/**
	 * Get the URL to perform autocomplete request.
	 *
	 * @return string The URL to call to perform the autocomplete request.
	 * @since 3.15.0
	 */
	public function get_autocomplete_url() {

		return WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE . 'autocomplete';

	}

	/**
	 * Get the URL to perform feedback deactivation request.
	 *
	 * @return string The URL to call to perform the feedback deactivation request.
	 * @since 3.19.0
	 */
	public function get_deactivation_feedback_url() {

		return WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE . 'feedbacks';

	}

	/**
	 * Get the base API URL.
	 *
	 * @return string The base API URL.
	 * @since 3.20.0
	 */
	public function get_api_url() {

		return WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE;
	}

	public function get_network_dataset_ids() {
		return $this->get( 'wl_advanced_settings', self::NETWORK_DATASET_IDS, array() );
	}

	public function set_network_dataset_ids( $network_dataset_ids ) {
		$this->set( 'wl_advanced_settings', self::NETWORK_DATASET_IDS, $network_dataset_ids );
	}

	public function get_skip_installation_notice() {

		return $this->get( 'wl_general_settings', self::SKIP_INSTALLATION_NOTICE, false );
	}

	public function set_skip_installation_notice( $value ) {

		$this->set( 'wl_general_settings', self::SKIP_INSTALLATION_NOTICE, $value );

	}

	/**
	 * The override URL or false if not set.
	 *
	 * @return false|string
	 */
	public function get_override_website_url() {
		$value = $this->get( 'wl_general_settings', self::OVERRIDE_WEBSITE_URL );

		return untrailingslashit( $value );
	}

	public function set_override_website_url( $value ) {
		$this->set( 'wl_general_settings', self::OVERRIDE_WEBSITE_URL, $value );
	}

}
