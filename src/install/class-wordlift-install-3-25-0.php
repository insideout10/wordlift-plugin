<?php
/**
 * Installs: Install Version 3.25.0.
 *
 * @since      3.25.0
 * @package    Wordlift
 * @subpackage Wordlift/install
 */

/**
 * Define the {@link Wordlift_Install_3_25_0} interface.
 *
 * @since      3.25.0
 * @package    Wordlift
 * @subpackage Wordlift/install
 */
class Wordlift_Install_3_25_0 extends Wordlift_Install {
	/**
	 * @inheritdoc
	 */
    protected static $version = '3.25.0';

	/**
	 * @inheritdoc
	 */
	public function install() {

	}

	/**
	 * Install mappings table
	 *
	 * @since 3.25.0
	 *
	 * @return void
	 */
	private function create_mappings_table() {
		global $wpdb;
		$table_name      = $wpdb->prefix . WL_DB_RELATION_INSTANCES_TABLE_NAME;
		$charset_collate = $wpdb->get_charset_collate();
	}




}
