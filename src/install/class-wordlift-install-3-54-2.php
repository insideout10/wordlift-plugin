<?php

use Wordlift\Cache\Ttl_Cache;

/**
 * @since 3.52.1
 */
class Wordlift_Install_3_54_2 extends Wordlift_Install {

	/**
	 * {@inheritdoc}
	 */
	protected static $version = '3.54.2';

	public function install() {
		global $wpdb;

		// This is a fixed like query used to fix an issue with bogus data being imported between 21.09.2024 and
		// 04.10.2024.
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$wpdb->query(
			"DELETE FROM {$wpdb->prefix}wl_entities
			WHERE about_jsonld LIKE '%https://knowledge.cafemedia.com/food/https://%'"
		);

		Ttl_Cache::flush_all();
	}

}
