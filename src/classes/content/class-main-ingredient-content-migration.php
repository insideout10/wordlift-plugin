<?php

namespace Wordlift\Content;

class Main_Ingredient_Content_Migration {

	public function __construct() {
		add_action( 'init', array( $this, 'migrate' ), 100 );
	}

	public function migrate() {
		if ( get_option( '_wl_main_ingredient_content_migration__migrated' )
			 || version_compare( get_option( 'wl_db_version', '0.0.0' ), '3.33.9', '<' ) ) {
			return;
		}

		$this->migrate_main_ingredient_jsonld();
		$this->delete_legacy_fields_from_meta();

		update_option( '_wl_main_ingredient_content_migration__migrated', true, true );
	}

	private function migrate_main_ingredient_jsonld() {
		global $wpdb;

		$wpdb->query(
			"UPDATE {$wpdb->prefix}wl_entities e
					JOIN $wpdb->postmeta pm ON pm.post_ID = e.content_id
						AND pm.meta_key = '_wl_main_ingredient_jsonld'
					SET e.about_jsonld = pm.meta_value
					WHERE e.content_type = 0"
		);

		$wpdb->query(
			"UPDATE {$wpdb->prefix}wl_entities e
					JOIN $wpdb->termmeta tm ON tm.term_id = e.content_id
						AND tm.meta_key = '_wl_main_ingredient_jsonld'
					SET e.about_jsonld = tm.meta_value
					WHERE e.content_type = 1"
		);

	}

	private function delete_legacy_fields_from_meta() {
		global $wpdb;

		$wpdb->query(
			"DELETE
			FROM $wpdb->postmeta
			WHERE meta_key = '_wl_main_ingredient_jsonld'"
		);

		$wpdb->query(
			"DELETE
			FROM $wpdb->termmeta
			WHERE meta_key = '_wl_main_ingredient_jsonld'"
		);

	}

}
