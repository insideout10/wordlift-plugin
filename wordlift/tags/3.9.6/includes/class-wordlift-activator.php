<?php

/**
 * Fired during plugin activation
 *
 * @link       https://wordlift.io
 * @since      1.0.0
 *
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 * @author     WordLift <hello@wordlift.io>
 */
class Wordlift_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		// If WordLift's key is not set `_wl_activation_redirect` transient. We won't redirect here, because we can't give
		// for granted that we're in a browser admin session.
		if ( '' === Wordlift_Configuration_Service::get_instance()->get_key() ) {
			set_transient( '_wl_activation_redirect', TRUE, 30 );
		}

	}

}
