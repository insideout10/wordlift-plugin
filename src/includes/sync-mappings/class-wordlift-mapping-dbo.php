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
	 * Returns a list of mapping item rows
	 * @return Array List of Mapping items
	 */
	public function get_mapping_items() {
		$mapping_table_name = $this->wpdb->prefix . WL_MAPPING_TABLE_NAME;
		return $this->wpdb->get_results( 
			"SELECT * FROM $mapping_table_name",
			ARRAY_A
		);
	}
	/**
	 * Insert new mapping item with title
	 *
	 * @param String $title Title of the mapping item.
	 * @return Int Id of the inserted mapping item.
	 */
	public function insert_mapping_item( $title ) {
		$mapping_table_name = $this->wpdb->prefix . WL_MAPPING_TABLE_NAME;
		$this->wpdb->insert(
			$mapping_table_name,
			array( 'mapping_title' => $title )
		);
		return $this->wpdb->insert_id;
	}

	/**
	 * Update mapping item with new title
	 *
	 * @param Array $mapping_data Array of the mapping data.
	 * @return Int Id of the inserted mapping item
	 */
	public function insert_or_update_mapping_item( $mapping_data ) {
		$mapping_table_name = $this->wpdb->prefix . WL_MAPPING_TABLE_NAME;
		$this->wpdb->replace(
			$mapping_table_name,
			$mapping_data
		);
		return $this->wpdb->insert_id;
	}

	/**
	 * Updates rule item.
	 *
	 * @param Array $rule_item_data   The rule_item_data, should contain rule_id.
	 * @return Int $rule_id The inserted rule id.
	 */
	public function insert_or_update_rule_item( $rule_item_data ) {
		$rule_table_name = $this->wpdb->prefix . WL_RULE_TABLE_NAME;
		$this->wpdb->replace(
			$rule_table_name,
			$rule_item_data
		);
		return $this->wpdb->insert_id;
	}
	/**
	 * If a rule group exists doesn't do anything, but if rule group
	 * didn't exist then it inserts a new entry.
	 *
	 * @param Int $mapping_id Primary key for mapping table.
	 * @return Int The inserted rule group id.
	 */
	public function insert_rule_group( $mapping_id ) {
		$rule_group_table_name = $this->wpdb->prefix . WL_RULE_GROUP_TABLE_NAME;
		$this->wpdb->insert(
			$rule_group_table_name,
			array(
				'mapping_id' => $mapping_id,
			)
		);
		return $this->wpdb->insert_id;
	}

	/**
	 * Deletes a rule item by rule_id from rule and rule group table.
	 *
	 * @param Int $rule_id Primary key for rule table.
	 * @return void
	 */
	public function delete_rule_item( $rule_id ) {
		$rule_table_name       = $this->wpdb->prefix . WL_RULE_TABLE_NAME;
		$this->wpdb->delete( $rule_table_name, array( 'rule_id' => $rule_id ) );
	}
	/**
	 * Deletes a mapping item by mapping_id
	 *
	 * @param Int $mapping_id Primary key for mapping table.
	 * @return void
	 */
	public function delete_mapping_item( $mapping_id ) {
		$mapping_table_name = $this->wpdb->prefix . WL_MAPPING_TABLE_NAME;
		$this->wpdb->delete( $mapping_table_name, array( 'mapping_id' => $mapping_id ) );
	}


	/**
	 * Insert/Update property item.
	 *
	 * @param Int   $mapping_id Primary key for mapping table.
	 * @param Array $propery_data Property row from table/ui.
	 * @return Int Inserted Property Id.
	 */
	public function insert_or_update_property( $mapping_id, $propery_data ) {
		$property_table_name        = $this->wpdb->prefix . WL_PROPERTY_TABLE_NAME;
		$propery_data['mapping_id'] = $mapping_id;
		$this->wpdb->replace( $property_table_name, $propery_data );
		return $this->wpdb->insert_id;
	}

	/**
	 * Delete property item.
	 *
	 * @param Int $property_id Primary key for property table.
	 * @return void
	 */
	public function delete_property( $property_id ) {
		$property_table_name = $this->wpdb->prefix . WL_PROPERTY_TABLE_NAME;		
		return $this->wpdb->delete( $property_table_name, array( 'property_id' => $property_id ) );
	}

}
