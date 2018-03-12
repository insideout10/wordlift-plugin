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
	 * Checks whether we have permissions to show the popup.
	 *
	 * @version 3.19.0
	 *
	 * @return  bool `true` if we have permissions, false otherwise.
	 */
	public function has_permission_to_show_popup() {
		// Get the current page.
		global $pagenow;

		// Bail if the user doesn't have permissions
		// or if it's not the plugins page.
		if ( $pagenow !== 'plugins.php' ) {
			return false;
		}

		// Get the user preferences. We shouldn't show the feedback popup
		// if we don't have permissions for that.
		$configuration_service = Wordlift_Configuration_Service::get_instance();
		$user_preferences      = $configuration_service->get_diagnostic_preferences();

		// Bail. We don't have preferences to show the popup.
		if ( $user_preferences !== 'yes' ) {
			return false;
		}

		return true;
	}

	/**
	 * Render the feedback popup in the footer.
	 *
	 * @version 3.19.0
	 *
	 * @return  void
	 */
	public function render_feedback_popup() {
		// Bail if we don't have permissions to show the popup.
		if ( ! $this->has_permission_to_show_popup() ) {
			return;
		}
		// Include the partial.
		include plugin_dir_path( __FILE__ ) . '../admin/partials/wordlift-admin-feedback-popup.php';
	}

	/**
	 * Enqueue required popup scripts and styles.
	 *
	 * @version 3.19.0
	 *
	 * @return  void
	 */
	public function enqueue_popup_scripts() {
		// Bail if we don't have permissions to show the popup.
		if ( ! $this->has_permission_to_show_popup() ) {
			return;
		}
		wp_enqueue_style( 'wordlift-admin-feedback-popup', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/css/wordlift-admin-feedback-popup.css', array() );
		wp_enqueue_script( 'wordlift-admin-feedback-popup', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/wordlift-admin-feedback-popup.js', array( 'jquery' ) );

		wp_localize_script( 'wordlift-admin-feedback-popup', 'settings', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ) );
	}
}
