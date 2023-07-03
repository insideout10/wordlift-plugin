<?php

namespace Wordlift\Mappings;

/**
 * Define the {@link Mappings_DBO} class.
 * Used for CRUD on a mapping item tables
 *
 * @since      3.25.0
 * @package    Wordlift
 * @subpackage Wordlift\Mappings
 */
final class Mappings_DBO {

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
	 * Returns an array of mappings.
	 *
	 * Each mapping contains one or more rule groups which in turn contain one or more rules.
	 *
	 * @return array An array of mappings.
	 */
	public function get_mappings() {
		global $wpdb;
		return $this->wpdb->get_results(
			"SELECT * FROM {$wpdb->prefix}wl_mapping",
			ARRAY_A
		);
	}

	/**
	 * Returns an array of active mappings.
	 *
	 * Each mapping contains one or more rule groups which in turn contain one or more rules.
	 *
	 * @return array An array of mappings.
	 */
	public function get_active_mappings() {
		global $wpdb;
		return $this->wpdb->get_results(
			"SELECT * FROM {$wpdb->prefix}wl_mapping WHERE mapping_status = 'active'",
			ARRAY_A
		);
	}

	/**
	 * Returns a list of property rows
	 *
	 * @param int $mapping_id Primary key of mapping table.
	 *
	 * @return array List of property items belong to $mapping_id.
	 */
	public function get_properties( $mapping_id ) {
		global $wpdb;
		$property_rows = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wl_mapping_property WHERE mapping_id=%d", $mapping_id ),
			ARRAY_A
		);

		return $property_rows;
	}

	/**
	 * Check if the row exists in the table
	 *
	 * @param string $table_name The table name you want to query, completely escaped value.
	 * @param string $primary_key_name The primary key you want to query, should be escaped before passing.
	 * @param int    $primary_key_value The primary key value, no need to escape.
	 *
	 * @return bool Returns true if the row exists, false if it does not
	 */
	private function check_if_row_exists( $table_name, $primary_key_name, $primary_key_value ) {
		global $wpdb;
		$primary_key_value = (int) $primary_key_value;
		$count             = (int) $this->wpdb->get_var(
			$wpdb->prepare(
				'SELECT COUNT(%1s) from %2s where %3s = %4d', // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
				$primary_key_name,
				$table_name,
				$primary_key_name,
				$primary_key_value
			)
		);

		return $count > 0;
	}

	/**
	 * Insert new mapping item with title
	 *
	 * @param string $title Title of the mapping item.
	 *
	 * @return int Id of the inserted mapping item.
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
	 * @param array $mapping_data Array of the mapping data.
	 *
	 * @return int Id of the inserted mapping item
	 */
	public function insert_or_update_mapping_item( $mapping_data ) {
		$mapping_table_name = $this->wpdb->prefix . WL_MAPPING_TABLE_NAME;
		$mapping_id         = array_key_exists( 'mapping_id', $mapping_data ) ? (int) $mapping_data['mapping_id'] : null;
		if ( $this->check_if_row_exists( $mapping_table_name, 'mapping_id', $mapping_data['mapping_id'] ) ) {
			$this->wpdb->update(
				$mapping_table_name,
				$mapping_data,
				array( 'mapping_id' => $mapping_id )
			);
		} else {
			$this->wpdb->insert(
				$mapping_table_name,
				$mapping_data
			);
			$mapping_id = (int) $this->wpdb->insert_id;
		}

		return $mapping_id;
	}

	/**
	 * Updates rule item.
	 *
	 * @param array $rule_item_data The rule_item_data, should contain rule_id.
	 *
	 * @return int $rule_id The inserted rule id.
	 */
	public function insert_or_update_rule_item( $rule_item_data ) {
		$rule_table_name = $this->wpdb->prefix . WL_RULE_TABLE_NAME;
		$rule_id         = array_key_exists( 'rule_id', $rule_item_data ) ? $rule_item_data['rule_id'] : null;
		if ( $this->check_if_row_exists( $rule_table_name, 'rule_id', $rule_id ) ) {
			$this->wpdb->update( $rule_table_name, $rule_item_data, array( 'rule_id' => $rule_id ) );
		} else {
			$this->wpdb->insert( $rule_table_name, $rule_item_data );
			$rule_id = $this->wpdb->insert_id;
		}

		return $rule_id;
	}

	/**
	 * If a rule group exists doesn't do anything, but if rule group
	 * didn't exist then it inserts a new entry.
	 *
	 * @param int $mapping_id Primary key for mapping table.
	 *
	 * @return int The inserted rule group id.
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
	 * @param int $rule_id Primary key for rule table.
	 *
	 * @return void
	 */
	public function delete_rule_item( $rule_id ) {
		$rule_table_name = $this->wpdb->prefix . WL_RULE_TABLE_NAME;
		$this->wpdb->delete( $rule_table_name, array( 'rule_id' => $rule_id ) );
	}

	/**
	 * Deletes a rule group item by rule_group_id from rule group table.
	 *
	 * @param int $rule_group_id Primary key for rule table.
	 *
	 * @return void
	 */
	public function delete_rule_group_item( $rule_group_id ) {
		$rule_group_table_name = $this->wpdb->prefix . WL_RULE_GROUP_TABLE_NAME;
		$this->wpdb->delete( $rule_group_table_name, array( 'rule_group_id' => $rule_group_id ) );
	}

	/**
	 * Deletes a mapping item by mapping_id
	 *
	 * @param int $mapping_id Primary key for mapping table.
	 *
	 * @return void
	 */
	public function delete_mapping_item( $mapping_id ) {
		$mapping_table_name = $this->wpdb->prefix . WL_MAPPING_TABLE_NAME;
		$this->wpdb->delete( $mapping_table_name, array( 'mapping_id' => $mapping_id ) );
	}

	/**
	 * Gets a list of rule group items.
	 *
	 * @param int $mapping_id Primary key for mapping table.
	 *
	 * @return array Get list of rule group items.
	 */
	public function get_rule_group_list( $mapping_id ) {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT rule_group_id FROM {$wpdb->prefix}wl_mapping_rule_group WHERE mapping_id=%d",
				$mapping_id
			),
			ARRAY_A
		);
	}

	/**
	 * Gets a list of rule group items.
	 *
	 * @param int $mapping_id Primary key for mapping table.
	 *
	 * @return array Get list of rule group items.
	 */
	public function get_rule_groups_by_mapping( $mapping_id ) {
		global $wpdb;
		$rule_group_rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT rule_group_id FROM {$wpdb->prefix}wl_mapping_rule_group WHERE mapping_id=%d",
				$mapping_id
			),
			ARRAY_A
		);

		// List of all rule group items.
		$rule_groups = array();
		foreach ( $rule_group_rows as $rule_group_row ) {
			$rule_groups[] = array(
				'rule_group_id' => $rule_group_row['rule_group_id'],
				'rules'         => $this->get_rules_by_rule_group( $rule_group_row['rule_group_id'] ),
			);
		}

		return $rule_groups;
	}

	/**
	 * Gets a list of rule items belong to rule_group_id.
	 *
	 * @param int $rule_group_id Indicates which group the item belongs to.
	 *
	 * @return array Get list of rule items.
	 */
	public function get_rules_by_rule_group( $rule_group_id ) {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}wl_mapping_rule WHERE rule_group_id=%d",
				$rule_group_id
			),
			ARRAY_A
		);
	}

	/**
	 * Insert/Update property item.
	 *
	 * @param array $property_data Property row from table/ui.
	 *
	 * @return int Inserted Property Id.
	 */
	public function insert_or_update_property( $property_data ) {
		$property_table_name = $this->wpdb->prefix . WL_PROPERTY_TABLE_NAME;
		$property_id         = array_key_exists( 'property_id', $property_data ) ? $property_data['property_id'] : null;

		if ( $this->check_if_row_exists( $property_table_name, 'property_id', $property_id ) ) {
			$this->wpdb->update(
				$property_table_name,
				$property_data,
				array( 'property_id' => $property_id )
			);
		} else {
			$this->wpdb->insert( $property_table_name, $property_data );
			$property_id = $this->wpdb->insert_id;
		}

		return $property_id;
	}

	/**
	 * Gets a single mapping item row.
	 *
	 * @param int $mapping_id Primary key of mapping table.
	 *
	 * @return array Returns single mapping table row..
	 */
	public function get_mapping_item_data( $mapping_id ) {
		global $wpdb;
		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}wl_mapping WHERE mapping_id=%d",
				$mapping_id
			),
			ARRAY_A
		);
	}

	/**
	 * Delete property item.
	 *
	 * @param int $property_id Primary key for property table.
	 *
	 * @return int|false The number of rows updated, or false on error.
	 */
	public function delete_property( $property_id ) {
		$property_table_name = $this->wpdb->prefix . WL_PROPERTY_TABLE_NAME;

		return $this->wpdb->delete( $property_table_name, array( 'property_id' => $property_id ) );
	}

}
