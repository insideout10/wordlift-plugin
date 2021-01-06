<?php

namespace Wordlift\Admin;

use Wordlift\Cache\Ttl_Cache;

/**
 * @since 3.28.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Key_Validation_Notice {

	const CACHE_KEY = 'is_key_valid';

	const KEY_VALIDATION_NONCE_ACTION = 'wl_key_validation_notice_nonce';

	const KEY_VALIDATION_NONCE_PARAM = '_wl_key_validation_notice_nonce';

	const KEY_VALIDATION_NOTICE_PARAM = 'wl_key_validation_notice';

	const NOTIFICATION_OPTION_KEY = 'wordlift_key_validation_notification_shown';

	/**
	 * @var \Wordlift_Key_Validation_Service
	 */
	private $key_validation_service;

	/**
	 * @var \Wordlift_Configuration_Service
	 */
	private $configuration_service;
	/**
	 * @var Ttl_Cache
	 */
	private $ttl_cache_service;

	/**
	 * Key_Validation_Notice constructor.
	 *
	 * @param \Wordlift_Key_Validation_Service $key_validation_service
	 * @param \Wordlift_Configuration_Service $configuration_service
	 */
	public function __construct( $key_validation_service, $configuration_service ) {

		$this->key_validation_service = $key_validation_service;

		$this->configuration_service = $configuration_service;

		$this->ttl_cache_service = new Ttl_Cache( 'key-validation-notification', 60 * 60 * 8 );

		if ( apply_filters( 'wl_feature__enable__notices', true ) ) {
			$this->display_key_validation_notice();
		}

		$that = $this;
		add_action( 'plugins_loaded', function () use ( $that ) {
			$that->notification_close_handler();
		} );
	}


	public function show_notification() {
		$settings_url = admin_url( 'admin.php?page=wl_configuration_admin_menu' );
		?>
        <div class="error">
            <p>
				<?php echo __( "Your WordLift key is not valid, please update the key in <a href='$settings_url'>WordLift Settings</a> or contact our support at hello@wordlift.io.", 'wordlift' ); ?>
            </p>
            <p class="submit">

                <a class="button-secondary skip"
                   href="<?php echo esc_url( wp_nonce_url( add_query_arg( self::KEY_VALIDATION_NOTICE_PARAM, self::KEY_VALIDATION_NOTICE_PARAM )
					   , self::KEY_VALIDATION_NONCE_ACTION, self::KEY_VALIDATION_NONCE_PARAM ) ); ?>">
					<?php esc_html_e( 'Close', 'wordlift' ); ?></a>
            </p>
        </div>
		<?php
	}

	private function is_key_valid() {

		$key = $this->configuration_service->get_key();

		// Check cache if the result is present, if not get the results
		// save it and return the data.
		if ( $this->ttl_cache_service->get( self::CACHE_KEY ) !== null ) {
			return $this->ttl_cache_service->get( self::CACHE_KEY );
		}

		$is_valid = $this->key_validation_service->is_key_valid( $key );

		// when the cache is set, clear the notification flag.
		delete_option( self::NOTIFICATION_OPTION_KEY );

		$this->ttl_cache_service->put( self::CACHE_KEY, $is_valid );

		return $is_valid;
	}

	private function display_key_validation_notice() {
		$that = $this;
		add_action( 'admin_notices', function () use ( $that ) {

			$is_notification_shown = get_option( Key_Validation_Notice::NOTIFICATION_OPTION_KEY, false );

			$key = $that->configuration_service->get_key();

			if ( ! $key ) {
				// Dont show warning or make API call, return early.
				return;
			}

			if ( $that->is_key_valid() ) {
				return;
			}

			if ( $is_notification_shown ) {
				return;
			}

			$that->show_notification();

		} );
	}

	public function notification_close_handler() {
		if ( ! isset( $_GET['wl_key_validation_notice'] )
		     || ! isset( $_GET['_wl_key_validation_notice_nonce'] ) ) {
			return false;
		}
		$type  = (string) $_GET['wl_key_validation_notice'];
		$nonce = (string) $_GET['_wl_key_validation_notice_nonce'];

		if ( wp_verify_nonce( $nonce, self::KEY_VALIDATION_NONCE_ACTION )
		     && current_user_can( 'manage_options' ) ) {
			// close the notification.
			update_option( self::NOTIFICATION_OPTION_KEY, true );
		}
	}

}

