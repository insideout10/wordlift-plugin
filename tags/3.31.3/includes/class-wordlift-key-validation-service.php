<?php
/**
 * Wordlift_Key_Validation_Service class
 *
 * The {@link Wordlift_Key_Validation_Service} class provides WordLift's key validation services.
 *
 * @link    https://wordlift.io
 *
 * @package Wordlift
 * @since   3.9.0
 */

use Wordlift\Api\Default_Api_Service;
use Wordlift\Cache\Ttl_Cache;

/**
 * Define the {@link Wordlift_Key_Validation_Service} class.
 *
 * @since 3.9.0
 */
class Wordlift_Key_Validation_Service {

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

	/**
	 * @var Ttl_Cache
	 */
	private $ttl_cache_service;

	/**
	 * Create a {@link Wordlift_Key_Validation_Service} instance.
	 *
	 * @param \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since 3.14.0
	 */
	public function __construct( $configuration_service ) {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Key_Validation_Service' );

		$this->configuration_service = $configuration_service;

		add_action( 'admin_init', array( $this, 'wl_load_plugin' ) );
		/**
		 * Filter: wl_feature__enable__notices.
		 *
		 * @param bool whether the notices needs to be enabled or not.
		 *
		 * @return bool
		 * @since 3.27.6
		 */
		if ( apply_filters( 'wl_feature__enable__notices', true ) ) {
			add_action( 'admin_notices', array( $this, 'wl_key_update_notice' ) );
		}

		$this->ttl_cache_service = new Ttl_Cache( 'key-validation-notification');

	}

	/**
	 * Validate the provided key.
	 *
	 * @param string $key WordLift's key to validate.
	 *
	 * @return WP_Error|array The response or WP_Error on failure.
	 * @since 3.9.0
	 *
	 */
	public function get_account_info( $key ) {

		$this->log->debug( 'Validating key...' );

		return Default_Api_Service::get_instance()->get( '/accounts/info', array(
			'Authorization' => "Key $key",
		) )->get_response();
	}

	/**
	 * Check if key is valid
	 *
	 * @param $key string
	 *
	 * @return bool
	 */
	public function is_key_valid( $key ) {

		$response = $this->get_account_info( $key );

		if ( is_wp_error( $response ) || 2 !== (int) $response['response']['code'] / 100 ) {
			return false;
		}
		$res_body = json_decode( wp_remote_retrieve_body( $response ), true );

		$url = $res_body['url'];

		// Considering that production URL may be filtered.
		$home_url = get_option( 'home' );
		$site_url = apply_filters( 'wl_production_site_url', untrailingslashit( $home_url ) );
		if ( is_null( $url ) || $url === $site_url ) {
			return true;
		}

		return false;
	}

	/**
	 * This function is hooked to the `wl_validate_key` AJAX call.
	 *
	 * @since 3.9.0
	 */
	public function validate_key() {

		// Ensure we don't have garbage before us.
		ob_clean();

		// Check if we have a key.
		if ( ! isset( $_POST['key'] ) ) {
			wp_send_json_error( 'The key parameter is required.' );
		}

		$response = $this->get_account_info( $_POST['key'] );

		// If we got an error, return invalid.
		if ( is_wp_error( $response ) || 2 !== (int) $response['response']['code'] / 100 ) {
			wp_send_json_success( array( 'valid' => false, 'message' => '' ) );
		}

		$res_body = json_decode( wp_remote_retrieve_body( $response ), true );

		// The URL stored in WLS. If this is the initial install the URL may be null.
		$url = $res_body['url'];

		// Considering that production URL may be filtered.
		$home_url = get_option( 'home' );
		$site_url = apply_filters( 'wl_production_site_url', untrailingslashit( $home_url ) );

		// If the URL isn't set or matches, then it's valid.
		if ( is_null( $url ) || $url === $site_url ) {
		    // Invalidate the cache key
			$this->ttl_cache_service->delete('is_key_valid' );
			wp_send_json_success( array( 'valid' => true, 'message' => '' ) );
		}

		// If the URL doesn't match it means that this key has been configured elsewhere already.
		if ( $url !== $site_url ) {
			Wordlift_Configuration_Service::get_instance()->set_key( '' );
			wp_send_json_success( array(
				'valid'   => false,
				'message' => __( 'The key is already used on another site, please contact us at hello@wordlift.io to move the key to another site.', 'wordlift' ),
			) );
		}

		// Set a response with valid set to true or false according to the key validity with message.
		wp_send_json_success( array(
			'valid'   => false,
			'message' => __( 'An error occurred, please contact us at hello@wordlift.io', 'wordlift' ),
		) );
	}

	/**
	 * This function is hooked `admin_init` to check _wl_blog_url.
	 *
	 */
	public function wl_load_plugin() {

		$wl_blog_url = get_option( '_wl_blog_url' );
		$home_url    = get_option( 'home' );

		if ( ! $wl_blog_url ) {
			update_option( '_wl_blog_url', $home_url, true );
		} else if ( $wl_blog_url !== $home_url ) {
			update_option( '_wl_blog_url', $home_url, true );
			Wordlift_Configuration_Service::get_instance()->set_key( '' );
			set_transient( 'wl-key-error-msg', __( "Your web site URL has changed. To avoid data corruption, WordLift's key has been removed. Please provide a new key in WordLift Settings. If you believe this to be an error, please contact us at hello@wordlift.io", 'wordlift' ), 10 );
		}

	}

	/**
	 * This function is hooked to the `admin_notices` to show admin notification.
	 *
	 */
	public function wl_key_update_notice() {
		if ( get_transient( 'wl-key-error-msg' ) ) {
			?>
            <div class="updated notice is-dismissible error">
                <p><?php _e( get_transient( 'wl-key-error-msg' ), 'wordlift' ); ?></p>
            </div>
			<?php
		}
	}
}
