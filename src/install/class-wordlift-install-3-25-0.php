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
	 * Reference to global $wpdb instance.
	 *
	 * @var $wpdb
	 * */
	private $wpdb;

	/** Constructor for 3_25_0 installer. */
	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
	}
	/**
	 * @inheritdoc
	 */
	public function install() {
		$this->create_mappings_table();
		$this->create_rule_group_table();
		$this->create_rule_table();
		$this->create_property_table();
	}

	/**
	 * Install mappings table
	 *
	 * @since 3.25.0
	 *
	 * @return void
	 */
	public function create_mappings_table() {
		$table_name      = $this->wpdb->prefix . WL_MAPPING_TABLE_NAME;
		$charset_collate = $this->wpdb->get_charset_collate();
		$sql             = <<<EOF
        CREATE TABLE IF NOT EXISTS $table_name (
			mapping_id INT(11) NOT NULL AUTO_INCREMENT, 
			mapping_title VARCHAR(255) NOT NULL,
			mapping_status VARCHAR(255) NOT NULL DEFAULT 'active',
			PRIMARY KEY  (mapping_id)
        ) $charset_collate;
EOF;
		// Execute the query for mappings table.
		dbDelta( $sql );
	}


	/**
	 * Install rule table
	 *
	 * @since 3.25.0
	 *
	 * @return void
	 */
	public function create_rule_table() {
		$table_name            = $this->wpdb->prefix . WL_RULE_TABLE_NAME;
		$rule_group_table_name = $this->wpdb->prefix . WL_RULE_GROUP_TABLE_NAME;
		$charset_collate       = $this->wpdb->get_charset_collate();
		$sql                   = <<<EOF
        CREATE TABLE IF NOT EXISTS $table_name (
				rule_id INT(11) NOT NULL AUTO_INCREMENT,
				rule_field_one VARCHAR(255) NOT NULL,
				rule_logic_field VARCHAR(255) NOT NULL,
				rule_field_two VARCHAR(255) NOT NULL,
				rule_group_id INT(11) NOT NULL,
				FOREIGN KEY (rule_group_id) REFERENCES $rule_group_table_name(rule_group_id)
				ON DELETE CASCADE,
				PRIMARY KEY  (rule_id)
        ) $charset_collate;
EOF;
		// Execute the query for mappings table.
		$this->wpdb->query( $sql );
	}

	/**
	 * Install rule group table, should run after creating mapping and
	 * rule table due to foreign key reference.
	 *
	 * @since 3.25.0
	 *
	 * @return void
	 */
	public function create_rule_group_table() {
		$table_name      = $this->wpdb->prefix . WL_RULE_GROUP_TABLE_NAME;
		$charset_collate = $this->wpdb->get_charset_collate();

		$mapping_table_name = $this->wpdb->prefix . WL_MAPPING_TABLE_NAME;
		$rule_table_name    = $this->wpdb->prefix . WL_RULE_TABLE_NAME;
		$sql                = <<<EOF
        CREATE TABLE IF NOT EXISTS $table_name (
                rule_group_id INT(11) NOT NULL AUTO_INCREMENT,
                mapping_id INT(11) NOT NULL,
                PRIMARY KEY  (rule_group_id),
                FOREIGN KEY (mapping_id) REFERENCES $mapping_table_name(mapping_id)
                ON DELETE CASCADE
        ) $charset_collate;
EOF;
		// Execute the query for rule group table, we cant use db delta
		// due to lack of support for foreign keys.
		$this->wpdb->query( $sql );
	}


	/**
	 * Install property table, should run afer mapping table due to
	 * foreign key reference.
	 *
	 * @since 3.25.0
	 *
	 * @return void
	 */
	public function create_property_table() {
		$table_name      = $this->wpdb->prefix . WL_PROPERTY_TABLE_NAME;
		$charset_collate = $this->wpdb->get_charset_collate();

		$mapping_table_name = $this->wpdb->prefix . WL_MAPPING_TABLE_NAME;
		$sql                = <<<EOF
        CREATE TABLE IF NOT EXISTS $table_name (
                property_id INT(11) NOT NULL AUTO_INCREMENT,
                mapping_id INT(11) NOT NULL,
				property_name VARCHAR(255) NOT NULL,
				field_type VARCHAR(255) NOT NULL,
				field_name VARCHAR(255) NOT NULL,
				transform_function VARCHAR(255) NOT NULL,
				property_status  VARCHAR(255) NOT NULL DEFAULT 'active',
                PRIMARY KEY  (property_id),
                FOREIGN KEY (mapping_id) REFERENCES $mapping_table_name(mapping_id)
                ON DELETE CASCADE
        ) $charset_collate;
EOF;
		// Execute the query for property table, we cant use db delta
		// due to lack of support for foreign keys.
		$this->wpdb->query( $sql );
	}

}
