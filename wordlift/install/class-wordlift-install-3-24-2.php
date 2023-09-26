<?php
/**
 * This file provides the install 3.24.2 procedure.
 *
 * This procedure sets the autoload option on several WLP options that are often queries.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.24.2
 * @package Wordlift/install
 */

class Wordlift_Install_3_24_2 extends Wordlift_Install {

	/**
	 * {@inheritdoc}
	 */
	protected static $version = '3.24.2';

	/**
	 * @inheritDoc
	 */
	public function install() {

		global $wpdb;

		if ( false === get_option( 'wl_mappings' ) ) {
			add_option( 'wl_mappings', array(), '', true );
		}

		if ( false === get_option( 'wl_analytics_settings' ) ) {
			add_option( 'wl_analytics_settings', false, '', true );
		}

		if ( false === get_option( 'wl_entity_type_settings' ) ) {
			add_option( 'wl_entity_type_settings', array(), '', true );
		}

		// Added for feature request 1496 (Webhooks)
		if ( false === get_option( 'wl_webhooks_settings' ) ) {
			add_option( 'wl_webhooks_settings', array(), '', false );
		}

		$wpdb->query(
			"UPDATE {$wpdb->options} SET autoload = 'yes'"
					  . " WHERE option_name IN ( 'wl_mappings', 'wl_analytics_settings', 'wl_entity_type_settings', 'WPLANG' )"
		);

	}

}
