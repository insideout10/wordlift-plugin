<?php

require_once ABSPATH . 'wp-admin/includes/upgrade.php';

/**
 * Create a daily schedule for WLP for existing installs.
 *
 * @since 3.40.2
 */
class Wordlift_Install_3_41_0 extends Wordlift_Install {

	/**
	 * {@inheritdoc}
	 */
	protected static $version = '3.41.0';

	public function install() {

		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

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
			about_jsonld TEXT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY uq__content_id__content_type (content_id,content_type),
			UNIQUE KEY uq__rel_uri_hash (rel_uri_hash)
		) %1s;", // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
				$charset_collate
			)
		);

	}
}
