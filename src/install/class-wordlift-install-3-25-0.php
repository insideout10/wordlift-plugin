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
		$table_name      = $wpdb->prefix . WL_MAPPING_TABLE_NAME;
        $charset_collate = $wpdb->get_charset_collate();
		$sql             = <<<EOF
        CREATE TABLE $table_name (
                mapping_id int(11) NOT NULL AUTO_INCREMENT,
                title varchar(255) NOT NULL,
                PRIMARY KEY (id)
        ) $charset_collate;
EOF;
    }
    // TODO: need to obtain field names for rule table.
	/**
	 * Install rule table
     *
	 *
	 * @since 3.25.0
	 *
	 * @return void
	 */
    private function create_rule_table() {
		global $wpdb;
		$table_name      = $wpdb->prefix . WL_RULE_TABLE_NAME;
        $charset_collate = $wpdb->get_charset_collate();
		$sql             = <<<EOF
        CREATE TABLE $table_name (
                rule_id int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (rule_id)
        ) $charset_collate;
EOF;        
    }

	/**
	 * Install rule group table, should run after creating mapping and
     * rule table due to foreign key reference.
	 *
	 * @since 3.25.0
	 *
	 * @return void
	 */
	private function create_rule_group_table() {
		global $wpdb;
		$table_name      = $wpdb->prefix . WL_RULE_GROUP_TABLE_NAME;
        $charset_collate = $wpdb->get_charset_collate();

        $mapping_table_name = $wpdb->prefix . WL_MAPPING_TABLE_NAME;
        $rule_table_name    = $wpdb->prefix . WL_RULE_TABLE_NAME;
		$sql                = <<<EOF
        CREATE TABLE $table_name (
                rule_group_id int(11) NOT NULL AUTO_INCREMENT,
                mapping_id int(11) NOT NULL,
                rule_id int(11) NOT NULL,
                PRIMARY KEY (id),
                FOREIGN KEY (mapping_id) REFERENCES $mapping_table_name(mapping_id)
                ON DELETE CASCADE,
                FOREIGN KEY (rule_id) REFERENCES $rule_table_name(rule_id)
                ON DELETE CASCADE
        ) $charset_collate;
EOF;
	}




}
