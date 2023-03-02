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
use Wordlift\Entity_Type\Entity_Type_Setter;

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
	 * @var Ttl_Cache
	 */
	private $ttl_cache_service;

	/**
	 * Create a {@link Wordlift_Key_Validation_Service} instance.
	 *
	 * @since 3.14.0
	 */
	public function __construct() {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Key_Validation_Service' );

		add_action( 'admin_init', array( $this, 'wl_load_plugin' ) );
		/**
		 * Filter: wl_feature__enable__notices.
		 *
		 * @return bool
		 * @since 3.27.6
		 */
		if ( apply_filters( 'wl_feature__enable__notices', true ) ) {
			add_action( 'admin_notices', array( $this, 'wl_key_update_notice' ) );
		}

		$this->ttl_cache_service = new Ttl_Cache( 'key-validation-notification' );

	}

	/**
	 * Validate the provided key.
	 *
	 * @param string $key WordLift's key to validate.
	 *
	 * @return WP_Error|array The response or WP_Error on failure.
	 * @since 3.9.0
	 */
	public function get_account_info( $key ) {

		$this->log->debug( 'Validating key...' );

		$response = Default_Api_Service::get_instance()->get(
			'/accounts/info',
			array(
				'Authorization' => "Key $key",
			)
		);

		/**
		 * @param $response \Wordlift\Api\Response
		 *
		 * @since 3.38.5
		 * This action is fired when the key is validated.
		 */
		do_action( 'wl_key_validation_response', $response );

		return $response->get_response();
	}

	private function key_validation_request( $key ) {
		$response = $this->get_account_info( $key );

		if ( is_wp_error( $response ) || 2 !== (int) $response['response']['code'] / 100 ) {
			throw new \Exception( __( 'An error occurred, please contact us at hello@wordlift.io', 'wordlift' ) );
		}

		$res_body = json_decode( wp_remote_retrieve_body( $response ), true );

		$url = $res_body['url'];

		$enabled_features = array_keys( array_filter( (array) $res_body['features'] ) );
		$plugin_features  = array(
			Entity_Type_Setter::STARTER_PLAN,
			Entity_Type_Setter::PROFESSIONAL_PLAN,
			Entity_Type_Setter::BUSINESS_PLAN,
		);

		if ( count( array_intersect( $enabled_features, $plugin_features ) ) === 0 ) {
			throw new \Exception( __( 'This key is not valid. Start building your Knowledge Graph by purchasing a WordLift subscription <a href=\'https://wordlift.io/pricing/\'>here</a>.', 'wordlift' ) );
		}

		// Considering that production URL may be filtered.
		$home_url = get_option( 'home' );
		$site_url = apply_filters( 'wl_production_site_url', untrailingslashit( $home_url ) );

		if ( ! empty( $url ) && $url !== $site_url ) {
			throw new \Exception( __( 'The key is already used on another site, please contact us at hello@wordlift.io to move the key to another site.', 'wordlift' ) );
		}

		return true;
	}

	/**
	 * Check if key is valid
	 *
	 * @param $key string
	 *
	 * @return bool
	 */
	public function is_key_valid( $key ) {
		try {
			$this->key_validation_request( $key );

			return true;
		} catch ( \Exception $e ) {
			return false;
		}
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
		if ( ! isset( $_POST['key'] ) ) {  //phpcs:ignore WordPress.Security.NonceVerification.Missing
			wp_send_json_error( 'The key parameter is required.' );
		}

		$this->ttl_cache_service->delete( 'is_key_valid' );

		try {
			$this->key_validation_request( sanitize_text_field( wp_unslash( (string) $_POST['key'] ) ) ); //phpcs:ignore WordPress.Security.NonceVerification.Missing
			wp_send_json_success(
				array(
					'valid'   => true,
					'message' => '',
				)
			);

		} catch ( \Exception $e ) {
			Wordlift_Configuration_Service::get_instance()->set_key( '' );
			wp_send_json_success(
				array(
					'valid'   => false,
					'message' => $e->getMessage(),
					'api_url' => Default_Api_Service::get_instance()->get_base_url(),
				)
			);
		}
	}

	/**
	 * This function is hooked `admin_init` to check _wl_blog_url.
	 */
	public function wl_load_plugin() {

		$wl_blog_url = get_option( '_wl_blog_url' );
		$home_url    = get_option( 'home' );

		if ( ! $wl_blog_url ) {
			update_option( '_wl_blog_url', $home_url, true );
		} elseif ( $wl_blog_url !== $home_url ) {
			update_option( '_wl_blog_url', $home_url, true );
			Wordlift_Configuration_Service::get_instance()->set_key( '' );
			set_transient( 'wl-key-error-msg', __( "Your web site URL has changed. To avoid data corruption, WordLift's key has been removed. Please provide a new key in WordLift Settings. If you believe this to be an error, please contact us at hello@wordlift.io", 'wordlift' ), 10 );
		}

	}

	/**
	 * This function is hooked to the `admin_notices` to show admin notification.
	 */
	public function wl_key_update_notice() {
		if ( get_transient( 'wl-key-error-msg' ) ) {
			?>
		  <div class="updated notice is-dismissible error">
			<p><?php esc_html( get_transient( 'wl-key-error-msg' ) ); ?></p>
		  </div>
			<?php
		}
	}
}
