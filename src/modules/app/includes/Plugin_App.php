<?php

namespace Wordlift\Modules\App;

class Plugin_App {

	private $handle = 'wl-angular-app';

	public function register_handle() {
		wp_register_script(
			$this->handle,
			plugin_dir_url( __DIR__ ) . 'js/app.js',
			array(),
			WORDLIFT_VERSION,
			true
		);

		add_action(
			'admin_enqueue_scripts',
			function () {
				// we dont want the filters to run if it was not enqueued.
				if ( wp_script_is( $this->handle, 'enqueued' ) ) {
					$settings = wp_json_encode( $this->get_settings() );
					wp_add_inline_script( $this->handle, "var _wlPluginAppSettings = {$settings};" );
				}
			},
			PHP_INT_MAX // This hook is running at last because we want to check if it was enqueued.
		);
	}

	/**
	 * @return mixed|null
	 */
	private function get_settings() {
		return apply_filters(
			'wl_plugin_app_settings',
			array(
				// Allows Stats Card to populate the settings.
				'stats'        => apply_filters( 'wl_dashboard__stats__settings', array() ),
				// @see https://developer.wordpress.org/rest-api/using-the-rest-api/authentication/
				'wp_api_nonce' => wp_create_nonce( 'wp_rest' ),
				'wp_api_base'  => untrailingslashit( rest_url() ),
				// Load wordlift api url and key.
				'wl_api_base'  => apply_filters( 'wl_api_base_url', WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE ),
				'wl_api_key'   => \Wordlift_Configuration_Service::get_instance()->get_key(),
			)
		);
	}

}
