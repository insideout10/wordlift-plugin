<?php

namespace Wordlift\Dataset;

class Sync_Page {

	/**
	 * Sync_Page constructor.
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

	}

	public function admin_menu() {

		add_submenu_page(
			'wl_admin_menu',
			__( 'Synchronize Dataset', 'wordlift' ),
			__( 'Synchronize Dataset', 'wordlift' ),
			'manage_options',
			'wl_dataset_sync',
			array(
				$this,
				'render',
			)
		);

	}

	public function render() {

		wp_enqueue_style(
			'wl-tasks-page',
			plugin_dir_url( __DIR__ ) . 'tasks/admin/assets/tasks-page.css',
			array(),
			\Wordlift::get_instance()->get_version()
		);

		wp_enqueue_script(
			'wl-dataset-sync-page',
			plugin_dir_url( __FILE__ ) . 'assets/sync-page.js',
			array( 'wp-api' ),
			\Wordlift::get_instance()->get_version(),
			false
		);

		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Synchronize Dataset', 'wordlift' ); ?></h2>

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
