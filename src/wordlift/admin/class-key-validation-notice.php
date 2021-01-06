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
	}


	public function show_notification() {
		?>
        <div class="error">
            <p><?php esc_html_e( 'WordLift key is not valid', 'wordlift' ); ?></p>
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
		$this->ttl_cache_service->put( self::CACHE_KEY, $is_valid );

		return $is_valid;
	}

	private function display_key_validation_notice() {
		add_action( 'admin_notices', function () {

			$key = $this->configuration_service->get_key();

			if ( ! $key ) {
				// Dont show warning or make API call, return early.
				return;
			}

			if ( $this->is_key_valid() ) {
				return;
			}

			$this->show_notification();

		} );
	}

}

