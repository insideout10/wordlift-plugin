<?php

namespace Wordlift\Admin;

use Wordlift\Cache\Ttl_Cache;

/**
 * @since 3.28.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Key_Validation_Notice {

	const CACHE_KEY = 'is_key_valid';

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


	public function get_notification_template() {
		return <<<EOF
<p></p>
EOF;

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

			echo $this->get_notification_template();

		} );
	}

}

