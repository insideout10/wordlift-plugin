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
					AND ( meta_value NOT LIKE %s
						OR meta_value LIKE %s )",
				'_wl_main_ingredient_jsonld',
				'%' . $wpdb->esc_like( '"@id":' ) . '%',
				'%' . $wpdb->esc_like( ' "' ) . '%'
			)
		);

		// Flush the JSON-LD caches.
		do_action( 'wl_ttl_cache_cleaner__flush' );

	}

}
