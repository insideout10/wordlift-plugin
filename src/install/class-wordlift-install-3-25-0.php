<?php
/**
 * Installs: Install Version 3.25.0.
 *
 * @since      3.25.0
 * @package    Wordlift
 * @subpackage Wordlift/install
 */

// @see: https://codex.wordpress.org/Creating_Tables_with_Plugins
require_once ABSPATH . 'wp-admin/includes/upgrade.php';

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
		self::create_mappings_table();
		self::create_rule_table();
		self::create_rule_group_table();
		self::create_property_table();
	}

	/**
	 * Install mappings table
	 *
	 * @since 3.25.0
	 *
	 * @return void
	 */
	public static function create_mappings_table() {
		global $wpdb;
		$table_name      = $wpdb->prefix . WL_MAPPING_TABLE_NAME;
		$charset_collate = $wpdb->get_charset_collate();
		$sql             = <<<EOF
        CREATE TABLE $table_name (
			mapping_id INT(11) NOT NULL AUTO_INCREMENT, 
			mapping_title VARCHAR(255) NOT NULL, 
			PRIMARY KEY  (mapping_id)
        ) $charset_collate;
EOF;
		// Execute the query for mappings table.
		dbDelta( $sql );
	}


	/**
	 * Install rule table
	 *
	 *
	 * @since 3.25.0
	 *
	 * @return void
	 */
	public static function create_rule_table() {
		global $wpdb;
		$table_name            = $wpdb->prefix . WL_RULE_TABLE_NAME;
		$rule_group_table_name = $wpdb->prefix . WL_RULE_GROUP_TABLE_NAME;
		$charset_collate       = $wpdb->get_charset_collate();
		$sql                   = <<<EOF
        CREATE TABLE $table_name (
				rule_id INT(11) NOT NULL AUTO_INCREMENT,
				rule_field_one VARCHAR(255) NOT NULL,
				rule_logic_field VARCHAR(255) NOT NULL,
				rule_field_two VARCHAR(255) NOT NULL,
				rule_group_id INT(11) NOT NULL,
				FOREIGN KEY (rule_group_id) REFERENCES $rule_group_table_name(rule_group_id)
                PRIMARY KEY (rule_id)
        ) $charset_collate;
EOF;
		// Execute the query for mappings table.
		dbDelta( $sql );
	}

	/**
	 * Install rule group table, should run after creating mapping and
	 * rule table due to foreign key reference.
	 *
	 * @since 3.25.0
	 *
	 * @return void
	 */
	public static function create_rule_group_table() {
		global $wpdb;
		$table_name      = $wpdb->prefix . WL_RULE_GROUP_TABLE_NAME;
		$charset_collate = $wpdb->get_charset_collate();

		$mapping_table_name = $wpdb->prefix . WL_MAPPING_TABLE_NAME;
		$rule_table_name    = $wpdb->prefix . WL_RULE_TABLE_NAME;
		$sql                = <<<EOF
        CREATE TABLE $table_name (
                rule_group_id INT(11) NOT NULL AUTOINCREMENT,
                mapping_id INT(11) NOT NULL,
                PRIMARY KEY  (rule_group_id),
                FOREIGN KEY (mapping_id) REFERENCES $mapping_table_name(mapping_id)
                ON DELETE CASCADE
        ) $charset_collate;
EOF;
		// Execute the query for rule group table, we cant use db delta
		// due to lack of support for foreign keys.
		$wpdb->query( $sql );
	}


	/**
	 * Install property table, should run afer mapping table due to
	 * foreign key reference.
	 *
	 * @since 3.25.0
	 *
	 * @return void
	 */
	public static function create_property_table() {
		global $wpdb;
		$table_name      = $wpdb->prefix . WL_PROPERTY_TABLE_NAME;
		$charset_collate = $wpdb->get_charset_collate();

		$mapping_table_name = $wpdb->prefix . WL_MAPPING_TABLE_NAME;
		$sql                = <<<EOF
        CREATE TABLE $table_name (
                property_id INT(11) NOT NULL AUTO_INCREMENT,
                mapping_id INT(11) NOT NULL,
				property_help_text VARCHAR(255) NOT NULL,
				field_type_help_text VARCHAR(255) NOT NULL,
				field_help_text VARCHAR(255) NOT NULL,
				transform_help_text VARCHAR(255) NOT NULL,
                PRIMARY KEY  (property_id),
                FOREIGN KEY (mapping_id) REFERENCES $mapping_table_name(mapping_id)
                ON DELETE CASCADE
        ) $charset_collate;
EOF;
		// Execute the query for rule group table, we cant use db delta
		// due to lack of support for foreign keys
		$wpdb->query( $sql );
	}

	/**
	 * @inheritdoc
	 */
	public function must_install() {
		return true;
	}


}
