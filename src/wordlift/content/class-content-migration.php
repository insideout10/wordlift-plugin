<?php

namespace Wordlift\Content;

class Content_Migration {

	public function __construct() {
		add_action( 'init', array( $this, 'migrate' ), 100 );
	}

	public function migrate() {
		if ( get_option( '_wl_content_migration__migrated' )
			 || version_compare( get_option( 'wl_db_version', '0.0.0' ), '3.33.9', '<' ) ) {
			return;
		}

		$this->migrate_entity_url();
		$this->delete_legacy_fields_from_postmeta();

		update_option( '_wl_content_migration__migrated', true, true );
	}

	private function migrate_entity_url() {
		global $wpdb;

		$wpdb->query(
			"INSERT INTO {$wpdb->prefix}wl_entities( content_id, content_type, rel_uri, rel_uri_hash ) 
			SELECT post_id AS content_id, 0 AS content_type,
			    SUBSTR( meta_value, LENGTH( SUBSTRING_INDEX( meta_value, '/', 4 ) ) + 2 ) AS rel_uri,
			    SHA1( SUBSTR( meta_value, LENGTH( SUBSTRING_INDEX( meta_value, '/', 4 ) ) + 2 ) ) AS rel_uri_hash   
			FROM $wpdb->postmeta
			WHERE meta_key = 'entity_url' AND post_id IN (SELECT ID FROM $wpdb->posts)
			ON DUPLICATE KEY UPDATE rel_uri = VALUES( rel_uri ), rel_uri_hash = VALUES( rel_uri_hash );"
		);

	}

	private function delete_legacy_fields_from_postmeta() {
		global $wpdb;

		$wpdb->query(
			"DELETE
			FROM $wpdb->postmeta
			WHERE meta_key = 'entity_url';"
		);

	}

}
