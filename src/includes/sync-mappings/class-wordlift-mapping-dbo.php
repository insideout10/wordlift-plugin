<?php 
/**
 * Define the {@link Wordlift_Mapping_DBO} class.
 * Used for CRUD on a mapping item tables
 *
 * @since      3.25.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/sync-mappings
 */
final class Wordlift_Mapping_DBO {

	/**
	 * The {@link wpdb} instance.
	 *
	 * @since  3.25.0
	 * @access private
	 * @var \wpdb $wpdb The {@link wpdb} instance.
	 */
	private $wpdb = null;

	/**
	 * Construct DBO object.
	 */
	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;

	}

	/**
	 * Insert new mapping item with title
	 *
	 * @param String $title Title of the mapping item.
	 */
	public function insert_mapping_item( $title ) {
		$mapping_table_name = $this->wpdb->prefix . WL_MAPPING_TABLE_NAME;
		$this->wpdb->insert( $mapping_table_name, array( 'mapping_title' => $title ) );
		return $this->wpdb->insert_id;
	}

	/**
	 * Update mapping item with new title
	 *
	 * @param Int    $mapping_id Primary key for mapping table.
	 *
	 * @param String $title Title of the mapping item.
	 */
	public function update_mapping_item( $mapping_id, $title ) {
		$mapping_table_name = $this->wpdb->prefix . WL_MAPPING_TABLE_NAME;
		$this->wpdb->replace( 
			$mapping_table_name,
			array(
				'mapping_title' => $title,
				'mapping_id'    => $mapping_id,
			)
		);	
	}

	/**
	 * Inserts rule item.
	 *
	 * @param String $rule_field_one   The first rule field.
	 * @param String $rule_logic_field The Logic field.
	 * @param String $rule_field_two   The second rule field.
	 */
	public function insert_rule_item( $rule_field_one,
	$rule_logic_field, $rule_field_two ) {
		$rule_table_name = $this->wpdb->prefix . WL_RULE_TABLE_NAME;
		$this->wpdb->insert(
			$rule_table_name,
			array(
				'rule_field_one'   => $rule_field_one,
				'rule_field_two'   => $rule_field_two,
				'rule_logic_field' => $rule_logic_field,
			)
		);
		return $this->wpdb->insert_id;
	}

	/**
	 * Updates rule item.
	 *
	 * @param Array $rule_item_data   The rule_item_data, should contain rule_id.
	 */
	public function update_rule_item( $rule_item_data ) {
		$rule_table_name = $this->wpdb->prefix . WL_RULE_TABLE_NAME;
		$this->wpdb->replace(
			$rule_table_name,
			$rule_item_data,
		);
	}

}
