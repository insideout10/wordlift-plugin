<?php

namespace Wordlift\Modules\Pods;

use Wordlift\Modules\Common\Plugin;

class Notices {

	/**
	 * @var Plugin
	 */
	private $pods_plugin;

	public function __construct( Plugin $plugin ) {
		$this->pods_plugin = $plugin;
	}

	public function register_hooks() {
		add_action( 'wl_metabox_before_html', array( $this, 'pods_notice' ) );
	}

	public function pods_notice() {

		// Enqueue pods module scripts.
		wp_enqueue_script( 'wl-module-pods', WL_DIR_URL . 'js/dist/pods.js', array(), WORDLIFT_VERSION, true );

		$kses_options                 = array(
			'p'      => array(),
			'b'      => array(),
			'span' => array(
				'class' => array(),
			),
			'button' => array(
				'class'   => array(),
				'onclick' => array(),
			),
		);
		$installation_success_message = __(
			'<p>WordLift: <b>Pods – Custom Content Types and Fields</b> plugin installed and activated.</p>',
			'wordlift'
		);

		$installing_message          = __( 'Installing', 'wordlift' ) . ' <span class="spinner is-active"></span>';
		$installation_failed_message = '<p>' .
			__( 'Wordlift: Pods – Custom Content Types and Fields installation failed, please retry or contact support@wordlift.io', 'wordlift' )
			. '</p><button class="button action" onclick="wordliftInstallPods(this)">'
			. __( 'Retry', 'wordlift' )
			. '</button>';

		wp_localize_script(
			'wl-module-pods',
			'wl_module_pods',
			array(
				'ajax_url' => esc_html( wp_parse_url( admin_url( 'admin-ajax.php' ), PHP_URL_PATH ) ),
				'messages' => array(
					'installation_success' => wp_kses( $installation_success_message, $kses_options ),
					'installing'           => wp_kses( $installing_message, $kses_options ),
					'installation_failed'  => wp_kses( $installation_failed_message, $kses_options ),
				),
			)
		);

		/**
		 * When pods not installed or activated then the notice should appear.
		 */
		if ( ! $this->pods_plugin->is_plugin_installed() ) {
			$this->display_notice(
				__( 'WordLift detected that <b>Pods – Custom Content Types and Fields</b> is not installed.', 'wordlift' ),
				__( 'Reinstall & Activate', 'wordlift' )
			);

			// Dont display notice.
			return;
		}

		if ( ! $this->pods_plugin->is_plugin_activated() ) {
			$this->display_notice(
				__( 'WordLift detected that <b>Pods – Custom Content Types and Fields</b> is deactivated.', 'wordlift' ),
				__( 'Reactivate', 'wordlift' )
			);

			return;
		}
	}

	private function display_notice( $message, $button_text ) {
		?>
		<div class="wl-notice notice-error" id="wordlift_pods_plugin_installation_notice">
			<p>
				<?php echo wp_kses( $message, array( 'b' => array() ) ); ?>
				<button class="button action right" onclick="wordliftInstallPods(this)">
					<?php echo esc_html( $button_text ); ?>
				</button>
			</p>
			<br/>
		</div>
		<?php
	}
}
