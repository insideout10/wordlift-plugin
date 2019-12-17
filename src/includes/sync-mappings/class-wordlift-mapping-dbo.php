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
	 * @param Array $mapping_data Array of the mapping data.
	 */
	public function insert_or_update_mapping_item( $mapping_data ) {
		$mapping_table_name = $this->wpdb->prefix . WL_MAPPING_TABLE_NAME;
		$this->wpdb->replace(
			$mapping_table_name,
			$mapping_data
		);
	}

	/**
	 * Updates rule item.
	 *
	 * @param Array $rule_item_data   The rule_item_data, should contain rule_id.
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
	 *
	 * @param Int $rule_id Primary key for rule table.
	 */
	public function insert_rule_group( $mapping_id ) {
		$rule_group_table_name = $this->wpdb->prefix . WL_RULE_GROUP_TABLE_NAME;
		$this->wpdb->insert(
			$rule_group_table_name,
			array(
				'mapping_id' => $mapping_id,
			)
		);

	}

	/**
	 * Deletes a rule item by rule_id from rule and rule group table.
	 *
	 * @param Int $rule_id Primary key for rule table.
	 */
	public function delete_rule_item( $rule_id ) {
		$rule_table_name       = $this->wpdb->prefix . WL_RULE_TABLE_NAME;
		$rule_group_table_name = $this->wpdb->prefix . WL_RULE_GROUP_TABLE_NAME;
		// Delete from both tables.
		$this->wpdb->delete( $rule_table_name, array( 'rule_id' => $rule_id ) );
		$this->wpdb->delete( $rule_group_table_name, array( 'rule_id' => $rule_id ) );
	}

	/**
	 * Insert/Update property item.
	 *
	 * @param Int   $mapping_id Primary key for mapping table.
	 *
	 * @param Array $propery_data Property row from table/ui.
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
	 */
	public function delete_property( $property_id ) {
		$property_table_name = $this->wpdb->prefix . WL_PROPERTY_TABLE_NAME;		
		return $this->wpdb->delete( $property_table_name, array( 'property_id' => $property_id ) );
	}

}
