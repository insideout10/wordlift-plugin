<?php

require_once ABSPATH . 'wp-admin/includes/upgrade.php';

/**
 * @since 3.33.9
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Wordlift_Install_3_33_9 extends Wordlift_Install {

	/**
	 * {@inheritdoc}
	 */
	protected static $version = '3.33.9';

	public function install() {
		$this->create_entities_table();
		$this->delete_legacy_fields_from_postmeta();
	}

	private function create_entities_table() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$wpdb->query(
			$wpdb->prepare(
				"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wl_entities (
			id bigint(20) unsigned NOT NULL auto_increment,
			content_id bigint(20) unsigned NOT NULL,
			content_type tinyint(1) unsigned NOT NULL,
			rel_uri varchar(500) NOT NULL,
			rel_uri_hash char(40) CHARACTER SET ascii NOT NULL,
			jsonld_hash CHAR(40)  NULL,
			synced_gmt CHAR(19) NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY uq__content_id__content_type (content_id,content_type),
			UNIQUE KEY uq__rel_uri_hash (rel_uri_hash)
		) %1s;", // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
				$charset_collate
			)
		);

		dbDelta(
			$wpdb->prepare(
				"CREATE TABLE {$wpdb->prefix}wl_entities (
			id bigint(20) unsigned NOT NULL auto_increment,
			content_id bigint(20) unsigned NOT NULL,
			content_type tinyint(1) unsigned NOT NULL,
			rel_uri varchar(500) NOT NULL,
			rel_uri_hash char(40) CHARACTER SET ascii NOT NULL,
			jsonld_hash CHAR(40)  NULL,
			synced_gmt CHAR(19) NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY uq__content_id__content_type (content_id,content_type),
			UNIQUE KEY uq__rel_uri_hash (rel_uri_hash)
		) %1s;", // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
				$charset_collate
			)
		);

	}

	private function delete_legacy_fields_from_postmeta() {
		global $wpdb;

		$wpdb->query(
			"DELETE
			FROM $wpdb->postmeta
			WHERE meta_key IN ( '_wl_jsonld_hash', '_synced_gmt' );"
		);

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

}
