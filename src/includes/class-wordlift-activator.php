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

		// Do not let the plugin be activate on wordpress versions before 4.2.
		$version = get_bloginfo( 'version' );
		if ( version_compare( $version, '4.2', '<' ) ) {
			die( __( 'The WordLift plugin requires WordPress version 4.2 or above.', 'wordlift' ) );
		}

		$configuration_service = Wordlift_Configuration_Service::get_instance();

		// Create a blank application key if there is none
		$key = $configuration_service->get_key();
		if ( empty( $key ) ) {
			$configuration_service->set_key( '' );
		}

		wl_core_install_entity_type_data();
		wl_core_install_create_relation_instance_table();
		flush_rewrite_rules();

		// If WordLift's key is not configured, set `_wl_activation_redirect` transient. We won't redirect here, because we can't give
		// for granted that we're in a browser admin session.
		if ( '' === $configuration_service->get_key() ) {
			set_transient( '_wl_activation_redirect', true, 30 );
		}

	}

}
