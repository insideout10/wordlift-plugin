<?php
/**
 * Installs: Install Version 3.25.0.
 *
 * wp_wl_mapping
 * +----------------+--------------+------+-----+---------+----------------+
 * | Field          | Type         | Null | Key | Default | Extra          |
 * +----------------+--------------+------+-----+---------+----------------+
 * | mapping_id     | int(11)      | NO   | PRI | NULL    | auto_increment |<----\-\
 * | mapping_title  | varchar(255) | NO   |     | NULL    |                |     | |
 * | mapping_status | varchar(255) | NO   |     | active  |                |     | |
 * +----------------+--------------+------+-----+---------+----------------+     | |
 *                                                                               | |
 * wp_wl_mapping_rule                                                            | |
 * +------------------+--------------+------+-----+---------+----------------+   | |
 * | Field            | Type         | Null | Key | Default | Extra          |   | |
 * +------------------+--------------+------+-----+---------+----------------+   | |
 * | rule_id          | int(11)      | NO   | PRI | NULL    | auto_increment |   | |
 * | rule_field_one   | varchar(255) | NO   |     | NULL    |                |   | |
 * | rule_logic_field | varchar(255) | NO   |     | NULL    |                |   | |
 * | rule_field_two   | varchar(255) | NO   |     | NULL    |                |   | |
 * | rule_group_id    | int(11)      | NO   | MUL | NULL    |                |-\ | |
 * +------------------+--------------+------+-----+---------+----------------+ | | |
 *                                                                             | | |
 * wp_wl_mapping_rule_group                                                    | | |
 * +---------------+---------+------+-----+---------+----------------+         | | |
 * | Field         | Type    | Null | Key | Default | Extra          |         | | |
 * +---------------+---------+------+-----+---------+----------------+         | | |
 * | rule_group_id | int(11) | NO   | PRI | NULL    | auto_increment |<--------/ | |
 * | mapping_id    | int(11) | NO   | MUL | NULL    |                |-----------/ |
 * +---------------+---------+------+-----+---------+----------------+             |
 *                                                                                 |
 * wp_wl_mapping_property                                                          |
 * +--------------------+--------------+------+-----+---------+----------------+   |
 * | Field              | Type         | Null | Key | Default | Extra          |   |
 * +--------------------+--------------+------+-----+---------+----------------+   |
 * | property_id        | int(11)      | NO   | PRI | NULL    | auto_increment |   |
 * | mapping_id         | int(11)      | NO   | MUL | NULL    |                |---/
 * | property_name      | varchar(255) | NO   |     | NULL    |                |
 * | field_type         | varchar(255) | NO   |     | NULL    |                |
 * | field_name         | varchar(255) | NO   |     | NULL    |                |
 * | transform_function | varchar(255) | NO   |     | NULL    |                |
 * | property_status    | varchar(255) | NO   |     | active  |                |
 * +--------------------+--------------+------+-----+---------+----------------+
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
	protected static $version = '3.25.3';

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
	 * Install mappings table.
	 *
	 * +----------------+--------------+------+-----+---------+----------------+
	 * | Field          | Type         | Null | Key | Default | Extra          |
	 * +----------------+--------------+------+-----+---------+----------------+
	 * | mapping_id     | int(11)      | NO   | PRI | NULL    | auto_increment |
	 * | mapping_title  | varchar(255) | NO   |     | NULL    |                |
	 * | mapping_status | varchar(255) | NO   |     | active  |                |
	 * +----------------+--------------+------+-----+---------+----------------+
	 *
	 * @return void
	 * @since 3.25.0
	 */
	public function create_mappings_table() {
		$table_name      = $this->wpdb->prefix . WL_MAPPING_TABLE_NAME;
		$charset_collate = $this->wpdb->get_charset_collate();
		// @@todo: is necessary to prefix the column names with `mapping_` ? we're the mappings table already.
		$sql = "
        CREATE TABLE $table_name (
			mapping_id INT(11) NOT NULL AUTO_INCREMENT, 
			mapping_title VARCHAR(255) NOT NULL,
			mapping_status VARCHAR(255) NOT NULL DEFAULT 'active',
			PRIMARY KEY  (mapping_id)
        ) $charset_collate;
";
		// Execute the query for mappings table.
		dbDelta( $sql );
	}

	/**
	 * Install rule table
	 *
	 * +------------------+--------------+------+-----+---------+----------------+
	 * | Field            | Type         | Null | Key | Default | Extra          |
	 * +------------------+--------------+------+-----+---------+----------------+
	 * | rule_id          | int(11)      | NO   | PRI | NULL    | auto_increment |
	 * | rule_field_one   | varchar(255) | NO   |     | NULL    |                |
	 * | rule_logic_field | varchar(255) | NO   |     | NULL    |                |
	 * | rule_field_two   | varchar(255) | NO   |     | NULL    |                |
	 * | rule_group_id    | int(11)      | NO   | MUL | NULL    |                |
	 * +------------------+--------------+------+-----+---------+----------------+
	 *
	 * @return void
	 * @since 3.25.0
	 */
	public function create_rule_table() {
		global $wpdb;

		$charset_collate = $this->wpdb->get_charset_collate();
		// @@todo: is necessary to prefix the column names with `rule_` ? we're the rules table already.

		// Execute the query for mappings table.
		$wpdb->query(
			$wpdb->prepare(
				"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wl_mapping_rule (
				rule_id INT(11) NOT NULL AUTO_INCREMENT,
				rule_field_one VARCHAR(255) NOT NULL,
				rule_logic_field VARCHAR(255) NOT NULL,
				rule_field_two VARCHAR(255) NOT NULL,
				rule_group_id INT(11) NOT NULL,
				FOREIGN KEY (rule_group_id) REFERENCES {$wpdb->prefix}wl_mapping_rule_group(rule_group_id)
				ON DELETE CASCADE,
				PRIMARY KEY  (rule_id)
        ) %1s;", // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
				$charset_collate
			)
		);
	}

	/**
	 * Install rule group table, should run after creating mapping and
	 * rule table due to foreign key reference.
	 *
	 * +---------------+---------+------+-----+---------+----------------+
	 * | Field         | Type    | Null | Key | Default | Extra          |
	 * +---------------+---------+------+-----+---------+----------------+
	 * | rule_group_id | int(11) | NO   | PRI | NULL    | auto_increment |
	 * | mapping_id    | int(11) | NO   | MUL | NULL    |                |
	 * +---------------+---------+------+-----+---------+----------------+
	 *
	 * @return void
	 * @since 3.25.0
	 */
	public function create_rule_group_table() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		// Execute the query for rule group table, we cant use db delta
		// due to lack of support for foreign keys.
		$wpdb->query(
			$wpdb->prepare(
				"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wl_mapping_rule_group (
                rule_group_id INT(11) NOT NULL AUTO_INCREMENT,
                mapping_id INT(11) NOT NULL,
                PRIMARY KEY  (rule_group_id),
                FOREIGN KEY (mapping_id) REFERENCES {$wpdb->prefix}wl_mapping(mapping_id)
                ON DELETE CASCADE
        ) %1s;", // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
				$charset_collate
			)
		);
	}

	/**
	 * Install property table, should run after mapping table due to
	 * foreign key reference.
	 *
	 * +--------------------+--------------+------+-----+---------+----------------+
	 * | Field              | Type         | Null | Key | Default | Extra          |
	 * +--------------------+--------------+------+-----+---------+----------------+
	 * | property_id        | int(11)      | NO   | PRI | NULL    | auto_increment |
	 * | mapping_id         | int(11)      | NO   | MUL | NULL    |                |
	 * | property_name      | varchar(255) | NO   |     | NULL    |                |
	 * | field_type         | varchar(255) | NO   |     | NULL    |                |
	 * | field_name         | varchar(255) | NO   |     | NULL    |                |
	 * | transform_function | varchar(255) | NO   |     | NULL    |                |
	 * | property_status    | varchar(255) | NO   |     | active  |                |
	 * +--------------------+--------------+------+-----+---------+----------------+
	 *
	 * @return void
	 * @since 3.25.0
	 */
	public function create_property_table() {
		global $wpdb;

		// Execute the query for property table, we cant use db delta
		// due to lack of support for foreign keys.
		$wpdb->query(
			$wpdb->prepare(
				"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wl_mapping_property (
                property_id INT(11) NOT NULL AUTO_INCREMENT,
                mapping_id INT(11) NOT NULL,
				property_name VARCHAR(255) NOT NULL,
				field_type VARCHAR(255) NOT NULL,
				field_name VARCHAR(255) NOT NULL,
				transform_function VARCHAR(255) NOT NULL,
				property_status  VARCHAR(255) NOT NULL DEFAULT 'active',
                PRIMARY KEY  (property_id),
                FOREIGN KEY (mapping_id) REFERENCES {$wpdb->prefix}wl_mapping(mapping_id)
                ON DELETE CASCADE
        ) %1s;", // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
				$wpdb->get_charset_collate()
			)
		);
	}

	public static function drop_tables() {
		global $wpdb;

		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wl_mapping_property" );
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wl_mapping_rule" );
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wl_mapping_rule_group" );
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wl_mapping" );
	}

}
