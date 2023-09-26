<?php

require_once ABSPATH . 'wp-admin/includes/upgrade.php';

/**
 * Create a daily schedule for WLP for existing installs.
 *
 * @since 3.40.2
 */
class Wordlift_Install_3_42_0 extends Wordlift_Install {

	/**
	 * {@inheritdoc}
	 */
	protected static $version = '3.42.0';

	public function install() {

		global $wpdb;

		$wpdb->query(
			"
			UPDATE {$wpdb->prefix}wl_entities
			SET about_jsonld = NULL
			WHERE length( about_jsonld ) <= 2
		"
		);

	}
}
