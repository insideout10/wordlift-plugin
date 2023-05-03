<?php

namespace Wordlift\Modules\App;

use Wordlift\Assertions;

class Plugin_App {

	public function render( $callable ) {
		Assertions::is_callable( $callable );
		$this->render_settings();
		call_user_func( $callable, $this->get_iframe_url() );
	}

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

	public function get_iframe_url() {
		return esc_url( plugin_dir_url( __DIR__ ) . 'app/iframe.html' );
	}

}
