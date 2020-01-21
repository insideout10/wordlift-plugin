<?php

/**
 * Define the {@link Mappings_Validator} class.
 *
 * Validates the mapping for single post and return the  * schema.org properties mapped to ACF, custom field or text
 * to be used for JSON-LD output.
 *
 * @since      3.25.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/sync-mappings
 */

namespace Wordlift\Mappings;

use Wordlift\Mappings\Validators\Rule_Validators_Registry;

final class Mappings_Validator {
	const TRASH_CATEGORY = 'trash';
	const ACTIVE_CATEGORY = 'active';

	/**
	 * The {@link Mappings_DBO} instance to test.
	 *
	 * @since  3.25.0
	 * @access private
	 * @var Mappings_DBO $dbo The {@link Mappings_DBO} instance to test.
	 */
	private $dbo;

	/**
	 * The array of valid properties, loaded at validate() method,
	 *
	 * @since  3.25.0
	 * @access private
	 * @var array $valid_properties The properties which are from mapping items when the rule passes.
	 */
	private $valid_properties = array();

	/**
	 * @var Rule_Validators_Registry
	 */
	private $rule_validators_registry;

	/**
	 * Constructor for Wordlift_Mapping_Validator.
	 *
	 * @param Mappings_DBO $dbo The {@link Mappings_DBO} instance.
	 * @param Rule_Validators_Registry $rule_validators_registry
	 */
	public function __construct( $dbo, $rule_validators_registry ) {

		$this->dbo                      = $dbo;
		$this->rule_validators_registry = $rule_validators_registry;

	}

	/**
	 * Validates two values based on the passed logic
	 * a single rule passes the user defined logic.
	 *
	 * @param string $key The key which every object has mapped to our value.
	 * @param array $items The array of items.
	 * @param string $status The value which the items should have.
	 *
	 * @return array
	 */
	private static function get_item_by_status( $key, $items, $status ) {
		return array_filter(
			$items,
			function ( $item ) use ( $key, $status ) {
				return $item[ $key ] === (string) $status;
			}
		);
	}

	/**
	 * Validates a post id with a rule and check if
	 * a single rule passes the user defined logic.
	 *
	 * @param int $post_id The post id.
	 * @param array $rule_data The single rule data.
	 *
	 * @return bool
	 */
	private function is_single_rule_valid( $post_id, $rule_data ) {
		// Determine the rule field one and validate based on it.
		$rule_field_one   = $rule_data['rule_field_one'];
		$rule_logic_field = $rule_data['rule_logic_field'];
		$rule_field_two   = $rule_data['rule_field_two'];

		$rule_validator = $this->rule_validators_registry->get_rule_validator( $rule_field_one );

		return $rule_validator->is_valid( $post_id, $rule_logic_field, $rule_field_one, $rule_field_two );
	}

	/**
	 * Validates a post id with the list of rules and check if
	 * all rules passes the user defined logic.
	 *
	 * @param int $post_id The post id.
	 * @param array $rules The list of rules from a rule group.
	 *
	 * @return bool
	 */
	private function is_rules_valid( $post_id, $rules ) {
		foreach ( $rules as $rule ) {
			if ( ! $this->is_single_rule_valid( $post_id, $rule ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Validates a post id with the list of rule groups and check if
	 * a single rule group passes the user defined logic.
	 *
	 * @param int $post_id The post id.
	 * @param array $rule_groups The rule group data list.
	 *
	 * @return bool
	 */
	private function post_matches_rule_groups( $post_id, $rule_groups ) {

		foreach ( $rule_groups as $rule_group ) {
			$rules = $rule_group['rules'];

			// Return early.
			if ( ! empty( $rules ) && $this->is_rules_valid( $post_id, $rules ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Validates a post id with the list of active mapping items and check if
	 * a mapping can be applied.
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array
	 */
	public function validate( $post_id ) {
		// Reset the valid property items before making the validation.
		$properties = array();

		// Get active mappings.
		$mappings = $this->dbo->get_active_mappings();

		// Get all active rule groups for the mapping items.
		foreach ( $mappings as $mapping ) {
			$rule_groups = $this->dbo->get_rule_groups_by_mapping( (int) $mapping['mapping_id'] );

			$should_apply_mapping = $this->post_matches_rule_groups( $post_id, $rule_groups );

			if ( $should_apply_mapping ) {
				$mapping_item_properties = $this->dbo->get_properties( $mapping['mapping_id'] );
				$properties              = array_merge( $properties, $mapping_item_properties );
			}
		}

		return self::get_item_by_status(
			'property_status',
			$properties,
			self::ACTIVE_CATEGORY
		);
	}

}
