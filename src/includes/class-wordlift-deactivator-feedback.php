<?php
/**
 * Fired during plugin deactivate
 *
 * @link       https://wordlift.io
 * @since      3.19.0
 *
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Fired during plugin deactivate.
 *
 * This class defines all code necessary to run during the plugin's deactivate.
 *
 * @since      3.19.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 * @author     WordLift <hello@wordlift.io>
 */
class Wordlift_Deactivator_Feedback {

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.19.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * Wordlift_Deactivator_Feedback constructor.
	 *
	 * @since 3.19.0
	 */
	public function __construct() {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Deactivator_Feedback' );

	}

	/**
	 * Checks whether we have permissions to show the popup.
	 *
	 * @return  bool `true` if we have permissions, false otherwise.
	 * @version 3.19.0
	 */
	private function has_permission_to_show_popup() {
		// Get the current page.
		global $pagenow;

		// Bail if the user doesn't have permissions
		// or if it's not the plugins page.
		if ( 'plugins.php' !== $pagenow ) {
			return false;
		}

		// Get the user preferences. We shouldn't show the feedback popup
		// if we don't have permissions for that.
		$user_preferences = Wordlift_Configuration_Service::get_instance()->get_diagnostic_preferences();

		// Bail. We don't have preferences to show the popup.
		if ( 'yes' !== $user_preferences ) {
			return false;
		}

		return true;
	}

	/**
	 * Render the feedback popup in the footer.
	 *
	 * @return  void
	 * @version 3.19.0
	 */
	public function render_feedback_popup() {
		// Bail if we don't have permissions to show the popup.
		if ( ! $this->has_permission_to_show_popup() ) {
			return;
		}
		// Include the partial.
		include plugin_dir_path( __FILE__ ) . '../admin/partials/wordlift-admin-deactivation-feedback-popup.php';
	}

	/**
	 * Enqueue required popup scripts and styles.
	 *
	 * @return  void
	 * @version 3.19.0
	 */
	public function enqueue_popup_scripts() {
		// Bail if we don't have permissions to show the popup.
		if ( ! $this->has_permission_to_show_popup() ) {
			return;
		}

		wp_enqueue_style( 'wordlift-admin-feedback-popup', plugin_dir_url( __DIR__ ) . 'admin/css/wordlift-admin-feedback-popup.css', array(), WORDLIFT_VERSION );
		wp_enqueue_script( 'wordlift-admin-feedback-popup', plugin_dir_url( __DIR__ ) . 'admin/js/wordlift-admin-feedback-popup.js', array( 'jquery' ), WORDLIFT_VERSION, false );
	}

	/**
	 * Handle the deactivation ajax call
	 * and perform a request to external server.
	 *
	 * @return  void
	 * @version 3.19.0
	 */
	public function wl_deactivation_feedback() {
		// Bail if the nonce is not valid.
		if (
			empty( $_POST['wl_deactivation_feedback_nonce'] ) || // The nonce doens't exists.
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wl_deactivation_feedback_nonce'] ) ), 'wl_deactivation_feedback_nonce' ) // The nonce is invalid.
		) {
			wp_send_json_error( __( 'Nonce Security Check Failed!', 'wordlift' ) );
		}

		// We allow user to deactivate without providing a reason
		// so bail and send success response.
		if ( empty( $_POST['code'] ) ) {
			wp_send_json_success();
		}

		$plugin_data = get_plugin_data( plugin_dir_path( __DIR__ ) . 'wordlift.php', false, false );

		// Prepare the options.
		$options = array(
			// The deactivation reason.
			'code'             => sanitize_text_field( wp_unslash( (string) $_POST['code'] ) ),
			// Additional information if provided.
			'details'          => ( ! empty( $_POST['details'] ) ) ? sanitize_text_field( wp_unslash( (string) $_POST['details'] ) ) : '',
			// The website url.
			'url'              => get_bloginfo( 'url' ),
			// WP version.
			'wordpressVersion' => get_bloginfo( 'version' ),
			// WL version.
			'wordliftVersion'  => $plugin_data['Version'],
			// The admin email.
			'email'            => get_bloginfo( 'admin_email' ),
		);

		$response = wp_remote_post(
			Wordlift_Configuration_Service::get_instance()->get_deactivation_feedback_url(),
			array(
				'method'  => 'POST',
				'body'    => wp_json_encode( $options ),
				'headers' => array( 'Content-Type' => 'application/json; charset=utf-8' ),
			)
		);

		$code    = wp_remote_retrieve_response_code( $response );
		$message = wp_remote_retrieve_response_message( $response );

		// Add message to the error log if the response code is not 200.
		if ( 201 !== $code ) {
			// Write the error in the logs.
			$this->log->error( 'An error occurred while requesting a feedback endpoint error_code: ' . $code . ' message: ' . $message );
		}

		// We should send success message even when the feedback is not
		// send, because otherwise the plugin cannot be deactivated.
		wp_send_json_success();
	}
}
