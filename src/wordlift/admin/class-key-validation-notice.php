<?php

namespace Wordlift\Admin;

use Wordlift\Cache\Ttl_Cache;

/**
 * @since 3.28.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Key_Validation_Notice {

	/**
	 * @var \Wordlift_Key_Validation_Service
	 */
	private $key_validation_service;

	/**
	 * @var \Wordlift_Configuration_Service
	 */
	private $configuration_service;

	/**
	 * Key_Validation_Notice constructor.
	 *
	 * @param \Wordlift_Key_Validation_Service $key_validation_service
	 * @param  \Wordlift_Configuration_Service $configuration_service
	 */
	public function __construct( $key_validation_service, $configuration_service ) {

		$this->key_validation_service = $key_validation_service;

		$this->configuration_service = $configuration_service;

		add_action( 'admin_notices', function () {

			if (! $this->key_validation_service->is_key_valid( ) ) {
				// Show the notice.
				echo "Key not valid";
			}
		} );
	}

}

