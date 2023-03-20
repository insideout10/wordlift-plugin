<?php

namespace Wordlift\Modules\Dashboard;

class Plugin_App {

	public function register_hooks() {
		add_action( '_wl_dashboard__main', array( $this, 'dashboard__main' ) );
	}

	public function dashboard__main() {
		$iframe_src = esc_url( plugin_dir_url( __FILE__ ) . 'app/index.html' );
		$params     = wp_json_encode(
			array(
				'synchronization' => array(
					'state'     => 'idle',
					'last_sync' => date_create( '2022-01-31 23:45:23' )->getTimestamp(),
					'next_sync' => date_create( '2024-01-31 23:45:23' )->getTimestamp(),
				),
			)
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo "
			<style>
			    #wlx-plugin-app {
			      margin-left: -20px;
			      width: calc(100% + 20px);
			      min-height: 1340px;
			    }
		    </style>
			<script type=\"text/javascript\">const __wlPluginAppSettings = $params</script>
			<iframe id='wlx-plugin-app' src='$iframe_src'></iframe>
		";
	}

}
