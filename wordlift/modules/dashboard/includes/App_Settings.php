<?php

namespace Wordlift\Modules\Dashboard;

use Wordlift\Modules\Common\Date_Utils;
use Wordlift\Modules\Dashboard\Synchronization\Synchronization_Service;

/**
 * This class is used to configure the angular app, which is used for rendering
 * the dashboard.
 */
class App_Settings {

	/**
	 * @var Synchronization_Service $synchronization_service
	 */
	private $synchronization_service;

	/**
	 * @param Synchronization_Service $synchronization_service
	 */
	public function __construct( $synchronization_service ) {
		$this->synchronization_service = $synchronization_service;
	}

	public function register_hooks() {
		add_filter(
			'wl_plugin_app_settings',
			function ( $settings ) {
				// Allows Stats Card to populate the settings.
				$settings['stats']           = apply_filters( 'wl_dashboard__stats__settings', array() );
				$settings['synchronization'] = array(
					'last_sync' => Date_Utils::to_iso_string( $this->synchronization_service->get_last_sync() ),
					'next_sync' => Date_Utils::to_iso_string( $this->synchronization_service->get_next_sync() ),
				);
				return $settings;
			}
		);
	}

}
