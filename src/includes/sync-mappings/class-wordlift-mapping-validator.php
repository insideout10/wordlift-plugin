<?php
/**
 * Define the {@link Wordlift_Mapping_Validator} class.
 * Validates the mapping for single post and return the
 * schema.org properties mapped to ACF, custom field or text
 * to be used for JSON-LD output.
 *
 * @since      3.25.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/sync-mappings
 */
final class Wordlift_Mapping_Validator {
	const TRASH_CATEGORY  = 'trash';
	const ACTIVE_CATEGORY = 'active';
	const POST_TYPE       = 'post_type';
	const IS_EQUAL_TO     = '===';
	const IS_NOT_EQUAL_TO = '!==';

	/**
	 * The {@link Wordlift_Mapping_DBO} instance to test.
	 *
	 * @since  3.25.0
	 * @access private
	 * @var \Wordlift_Mapping_DBO $dbo The {@link Wordlift_Mapping_DBO} instance to test.
	 */
	private $dbo;

	/**
	 * Constructor for Wordlift_Mapping_Validator.
	 */
	public function __construct() {
		$this->dbo = new Wordlift_Mapping_DBO();
	}
	/**
	 * Validates two values based on the passed logic
	 * a single rule passes the user defined logic.
	 *
	 * @param String $key The key which every object has mapped to our value.
	 * @param Array  $items The array of items.
	 * @param String $status The value which the items should have.
	 * @return Array
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
	 * Validates two values based on the passed logic
	 * a single rule passes the user defined logic.
	 *
	 * @param String $value_one The first value.
	 * @param String $logic The logic field.
	 * @param String $value_two The second value.
	 * @return Boolean
	 */
	private function is_logic_valid( $value_one, $logic, $value_two ) {
		switch ( $logic ) {
			case self::IS_EQUAL_TO:
				return (string) $value_one === (string) $value_two;
			case self::IS_NOT_EQUAL_TO:
				return (string) $value_one !== (string) $value_two;
		}
	}

	/**
	 * Validates a post id with a rule and check if
	 * a single rule passes the user defined logic.
	 *
	 * @param Int   $post_id The post id.
	 * @param Array $rule_data The single rule data.
	 * @return Boolean
	 */
	private function is_single_rule_valid( $post_id, $rule_data ) {
		// Determine the rule field one and validate based on it.
		$rule_field_one   = $rule_data['rule_field_one'];
		$rule_logic_field = $rule_data['rule_logic_field'];
		$rule_field_two   = $rule_data['rule_field_two'];

		switch ( $rule_field_one ) {
			case self::POST_TYPE:
				return $this->is_logic_valid(
					get_post_type( $post_id ),
					$rule_logic_field,
					$rule_field_two
				);
			default:
				$taxonomy       = $rule_field_one;
				$terms          = get_the_terms( $post_id, $taxonomy );
				$terms_id_array = array();
				if ( is_wp_error( $terms ) || 0 === count( $terms ) ) {
					// If no terms present then the rule is invalid.
					return false;
				}
				foreach ( $terms as $term ) {
					array_push( $terms_id_array, (string) $term->term_id );
				}
				if ( self::IS_EQUAL_TO === $rule_logic_field ) {
					// Rule is made to check if the term is present for post, so
					// do in_array.
					return in_array( (string) $rule_field_two, $terms_id_array, true );
				}
				elseif ( self::IS_NOT_EQUAL_TO === $rule_logic_field ) {
					// The term  should not be present in the post terms.
					return ! in_array( (string) $rule_field_two, $terms_id_array, true );
				}
		}
	}

	/**
	 * Validates a post id with the list of rules and check if
	 * all rules passes the user defined logic.
	 *
	 * @param Int   $post_id The post id.
	 * @param Array $rules The list of rules from a rule group.
	 * @return Boolean
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
	 * @param Int   $post_id The post id.
	 * @param Array $rule_group_data The rule group data list.
	 * @return Boolean
	 */
	private function validate_rule_group_with_rules_for_post_id( $post_id, $rule_group_data ) {
		// Atleast one of the rule group must be valid.
		$valid_rule_groups = array();
		foreach ( $rule_group_data as $rule_group ) {
			$single_rule_group_rules = $rule_group['rules'];
			// There should be atleast one rule present.
			if (
				0 !== count( $single_rule_group_rules ) &&
				$this->is_rules_valid( $post_id, $single_rule_group_rules )
			) {
				array_push( $valid_rule_groups, $rule_group );
			}
		}
		return 0 !== count( $valid_rule_groups );
	}

	/**
	 * Validates a post id with the list of active mapping items and check if
	 * a mapping can be applied.
	 *
	 * @param Int $post_id The post id.
	 * @return Boolean
	 */
	public function validate( $post_id ) {
		// Get all mapping items.
		$mapping_items        = $this->dbo->get_mapping_items();
		$active_mapping_items = self::get_item_by_status(
			'mapping_status',
			$mapping_items,
			self::ACTIVE_CATEGORY
		);
		$valid_mapping_items  = array();
		// Get all active rule groups for the mapping items.
		foreach ( $active_mapping_items as $mapping_item ) {
			$rule_groups               = $this->dbo->get_rule_group_list_with_rules(
				(int) $mapping_item['mapping_id']
			);
			$is_mapping_valid_for_post = $this->validate_rule_group_with_rules_for_post_id(
				$post_id,
				$rule_groups
			);
			if ( $is_mapping_valid_for_post ) {
				array_push(
					$valid_mapping_items,
					$mapping_item
				);
			}
		}
		// If atleast one mapping item is valid then it can be applied.
		return 0 !== count( $valid_mapping_items );
	}

}
