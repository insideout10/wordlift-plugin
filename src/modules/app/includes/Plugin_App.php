<?php

namespace Wordlift\Modules\App;

class Plugin_App {

	public function render_settings() {

		$params = wp_json_encode(
			apply_filters(
				'wl_plugin_app_settings',
				array(
					// Allows Stats Card to populate the settings.
					'stats'           => apply_filters( 'wl_dashboard__stats__settings', array() ),
					// @see https://developer.wordpress.org/rest-api/using-the-rest-api/authentication/
					'wp_api_nonce'    => wp_create_nonce( 'wp_rest' ),
					'wp_api_base'     => untrailingslashit( rest_url() ),
					'remote_api_base' => apply_filters( 'wl_api_base_url', WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE ),
					'remote_api_key'  => \Wordlift_Configuration_Service::get_instance()->get_key(),
				)
			)
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo "<script type=\"text/javascript\">window._wlPluginAppSettings = $params</script>";
	}

	public function dashboard__main() {

		$iframe_src = esc_url( plugin_dir_url( __DIR__ ) . 'app/iframe.html' );

		$this->render_settings();

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo "
			<style>
			    #wlx-plugin-app {
			      margin-left: -20px;
			      width: calc(100% + 20px);
			      min-height: 1500px;
			    }
		    </style>
			<iframe id='wlx-plugin-app' src='$iframe_src'></iframe>
		";
	}

	// public function admin_enqueue_scripts() {
	// Required to support notices that close themselves (like the `WooCommerce needs to be installed ...` message).
	// wp_enqueue_script( 'wp-util' );
	// }

}
