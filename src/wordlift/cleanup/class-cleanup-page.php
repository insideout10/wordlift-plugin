<?php
/**
 * Register entity annotation cleanup admin page.
 *
 * @since 3.34.1
 */

namespace Wordlift\Cleanup;

class Cleanup_Page {

	/**
	 * Sync_Page constructor.
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

	}

	public function admin_menu() {

		add_submenu_page( 'wl_admin_menu', __( 'Entity Annotation Cleanup', 'wordlift' ), __( 'Entity Annotation Cleanup', 'wordlift' ), 'manage_options', 'wl_entity_annotation_cleanup', array(
			$this,
			'render'
		) );

	}

	public function render() {

		wp_enqueue_style(
			'wl-tasks-page',
			plugin_dir_url( dirname( __FILE__ ) ) . 'tasks/admin/assets/tasks-page.css',
			array(),
			\Wordlift::get_instance()->get_version(),
			'all' );
		wp_enqueue_script(
			'wl-dataset-sync-page',
			plugin_dir_url( __FILE__ ) . 'assets/sync-page.js',
			array( 'wp-api' ),
			\Wordlift::get_instance()->get_version() );

		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Entity Annotation Cleanup', 'wordlift' ); ?></h2>

			<div class="wl-task__progress" style="border: 1px solid #23282D; height: 20px; margin: 8px 0;">
				<div class="wl-task__progress__bar"
				     style="width:0;background: #0073AA; text-align: center; height: 100%; color: #fff;"></div>
			</div>

			<button id="wl-start-btn" type="button" class="button button-large button-primary"><?php
				esc_html_e( 'Start', 'wordlift-framework' ); ?></button>
			<button id="wl-stop-btn" type="button" class="button button-large button-primary hidden"><?php
				esc_html_e( 'Stop', 'wordlift-framework' ); ?></button>

		</div>
		<?php
	}

}
