<?php

/**
 * @since 3.45.1
 */
class Wordlift_Install_3_49_2 extends Wordlift_Install {

	/**
	 * {@inheritdoc}
	 */
	protected static $version = '3.49.2';

	public function install() {

		global $wpdb;

		$wpdb->query( "DELETE FROM $wpdb->termmeta WHERE meta_key = 'wl_mentions_count'" );
		$wpdb->query( "DELETE FROM $wpdb->termmeta WHERE meta_key = 'wl_about_count'" );
		$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key = 'wl_mentions_count'" );
		$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key = 'wl_about_count'" );

	}

}
