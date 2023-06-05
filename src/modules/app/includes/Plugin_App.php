<?php

namespace Wordlift\Modules\App;

class Plugin_App {

	public function register_handle() {

		wp_register_script(
			WL_ANGULAR_APP_SCRIPT_HANDLE,
			plugin_dir_url( __DIR__ ) . 'js/app.js',
			array(),
			WORDLIFT_VERSION,
			true
		);
		$settings = wp_json_encode( $this->get_settings() );
		wp_add_inline_script( WL_ANGULAR_APP_SCRIPT_HANDLE, "var _wlPluginAppSettings = {$settings};" );

		// add_action(
		// 'admin_enqueue_scripts',
		// array( $this, 'print_settings' ),
		// PHP_INT_MAX // This hook is running at last because we want to check if it was enqueued.
		// );
	}

	/**
	 * @return mixed|null
	 */
	private function get_settings() {
		return apply_filters(
			'wl_plugin_app_settings',
			array(
				// @see https://developer.wordpress.org/rest-api/using-the-rest-api/authentication/
				'wp_api_nonce'      => wp_create_nonce( 'wp_rest' ),
				'wp_api_base'       => untrailingslashit( rest_url() ),
				// Load wordlift api url and key.
				'wl_api_base'       => apply_filters( 'wl_api_base_url', WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE ),
				'wl_api_key'        => \Wordlift_Configuration_Service::get_instance()->get_key(),
				'wl_dashboard_link' => admin_url( 'admin.php?page=wl_admin_menu' ),
			)
		);
	}

}
