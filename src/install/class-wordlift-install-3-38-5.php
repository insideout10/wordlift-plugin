<?php

/**
 * Remove possible corrupted main ingredients.
 *
 * @since 3.38.5
 */
class Wordlift_Install_3_38_5 extends Wordlift_Install {

	/**
	 * {@inheritdoc}
	 */
	protected static $version = '3.38.5';

	public function install() {
		global $wpdb;

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $wpdb->postmeta
				WHERE meta_key = %s
					AND meta_value NOT LIKE '%\"@id\":%'
				",
				'_wl_main_ingredient_jsonld'
			)
		);

		// Flush the JSON-LD caches.
		do_action( 'wl_ttl_cache_cleaner__flush' );

	}

}
