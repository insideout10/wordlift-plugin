<?php

namespace Wordlift\Modules\Dashboard;

use Wordlift\Modules\Common\Date_Utils;
use Wordlift\Modules\Dashboard\Stats\Stats;
use Wordlift\Modules\Dashboard\Synchronization\Synchronization_Service;

class Plugin_App {
	/**
	 * @var Synchronization_Service
	 */
	private $synchronization_service;
	/**
	 * @var Stats
	 */
	private $stats;

	/**
	 * @param $stats Stats
	 * @param $synchronization_service Synchronization_Service
	 */
	public function __construct( $stats, $synchronization_service ) {
		$this->stats                   = $stats;
		$this->synchronization_service = $synchronization_service;
	}

	public function register_hooks() {
		add_action( '_wl_dashboard__main', array( $this, 'dashboard__main' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	public function dashboard__main() {
		$iframe_src = esc_url( plugin_dir_url( __DIR__ ) . 'app/iframe.html' );

		$params = wp_json_encode(
			array(
				'synchronization' => array(
					'last_sync' => Date_Utils::to_iso_string( $this->synchronization_service->get_last_sync() ),
					'next_sync' => Date_Utils::to_iso_string( $this->synchronization_service->get_next_sync() ),
				),
				// Allows Stats Card to populate the settings.
				'stats'           => apply_filters( 'wl_dashboard__stats__settings', array() ),
				// @see https://developer.wordpress.org/rest-api/using-the-rest-api/authentication/
				'wp_api_nonce'    => wp_create_nonce( 'wp_rest' ),
				'wp_api_base'     => untrailingslashit( rest_url() ),
				'remote_api_base' => apply_filters( 'wl_api_base_url', WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE ),
				'remote_api_key'  => \Wordlift_Configuration_Service::get_instance()->get_key(),
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

	public function admin_enqueue_scripts() {
		// Required to support notices that close themselves (like the `WooCommerce needs to be installed ...` message).
		wp_enqueue_script( 'wp-util' );
	}

}
