<?php
/**
 * Fired during plugin uninstall
 *
 * @link       https://wordlift.io
 * @since      3.19.0
 *
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Fired during plugin uninstall.
 *
 * This class defines all code necessary to run during the plugin's uninstall.
 *
 * @since      3.19.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 * @author     WordLift <hello@wordlift.io>
 */
class Wordlift_Uninstaller {
	
	/**
	 * Fired on plugin uninstall
	 *
	 * @version 3.18.0
	 *
	 * @return  void 
	 */
	public static function uninstall() {
		// Bail if the user doesn't have permissions.
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		// Get the user preferences. We shouldn't show the feedback popup
		// if we don't have permissions for that.
		$configuration_service = Wordlift_Configuration_Service::get_instance();
		$user_preferences      = $configuration_service->get_diagnostic_preferences();

		// Bail. We don't have preferences to show the popup.
		if ( $user_preferences !== 'yes' ) {
			return;
		}

include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wordlift-admin-setup.php';
		// Uncomment the following line to see the function in action
		 exit;
	}

}
