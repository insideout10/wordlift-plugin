<?php

namespace Wordlift\Videoobject\Pages;

class Import_Videos_Page {

	/**
	 * Sync_Page constructor.
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

	}

	public function admin_menu() {

		add_submenu_page(
			'wl_admin_menu',
			__( 'Import all videos', 'wordlift' ),
			__( 'Import all videos', 'wordlift' ),
			'manage_options',
			'wl_videos_import',
			array(
				$this,
				'render',
			)
		);

	}

	public function render() {

		wp_enqueue_script(
			'wl-videos-sync-page',
			plugin_dir_url( __FILE__ ) . 'assets/videoobject-import-page.js',
			array( 'wp-api' ),
			WORDLIFT_VERSION,
			false
		);
		wp_localize_script(
			'wl-videos-sync-page',
			'_wlVideoObjectImportSettings',
			array(
				'restUrl' => get_rest_url(),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
			)
		)

		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Import all videos', 'wordlift' ); ?></h2>

			<div class="wl-task__progress" style="border: 1px solid #23282D; height: 20px; margin: 8px 0;">
				<div class="wl-task__progress__bar"
					 style="width:0;background: #0073AA; text-align: center; height: 100%; color: #fff;"></div>
			</div>

			<button id="wl-start-btn" type="button" class="button button-large button-primary">
			<?php
				esc_html_e( 'Start', 'wordlift' );
			?>
				</button>
			<button id="wl-stop-btn" type="button" class="button button-large button-primary hidden">
			<?php
				esc_html_e( 'Stop', 'wordlift' );
			?>
				</button>

		</div>
		<?php
	}

}
