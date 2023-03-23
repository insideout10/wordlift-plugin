<?php

namespace Wordlift\Modules\Dashboard;

use Wordlift\Modules\Common\Date_Utils;
use Wordlift\Modules\Dashboard\Synchronization\Synchronization_Service;

class Plugin_App {

	/**
	 * @var Synchronization_Service
	 */
	private $synchronization_service;

	public function __construct( Synchronization_Service $synchronization_service ) {
		$this->synchronization_service = $synchronization_service;
	}

	public function register_hooks() {
		add_action( '_wl_dashboard__main', array( $this, 'dashboard__main' ) );
	}

	public function dashboard__main() {
		$iframe_src = esc_url( plugin_dir_url( __DIR__ ) . 'app/iframe.html' );
		$params     = wp_json_encode(
			array(
				'synchronization' => array(
					'state'     => 'idle',
					'last_sync' => Date_Utils::to_iso_string( $this->synchronization_service->get_last_sync() ),
					'next_sync' => Date_Utils::to_iso_string( $this->synchronization_service->get_next_sync() ),
				),
				'api_url'         => rest_url( '/wl-dashboard/v1' ),
			)
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo "
			<style>
			    #wlx-plugin-app {
			      margin-left: -20px;
			      width: calc(100% + 20px);
			      min-height: 1500px;
			    }
		    </style>
			<script type=\"text/javascript\">window._wlPluginAppSettings = $params</script>
			<iframe id='wlx-plugin-app' src='$iframe_src'></iframe>
		";
	}

}
