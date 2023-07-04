<?php
/**
 * This file defines the Rule Groups Validator.
 *
 * By passing a post ID and an array of Rule Groups (each Rule Group contains Rule which must all satisfy the
 * requirements) it will check that at least one Rule Group passes.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.25.0
 * @package Wordlift\Mappings\Validators
 */

namespace Wordlift\Mappings\Validators;

/**
 * Class Rule_Groups_Validator
 *
 * @package Wordlift\Mappings\Validators
 */
class Rule_Groups_Validator {

	/**
	 * The {@link Rule_Validators_Registry} instance.
	 *
	 * @var Rule_Validators_Registry $rule_validators_registry The {@link Rule_Validators_Registry} instance.
	 */
	private $rule_validators_registry;

	/**
	 * Rule_Groups_Validator constructor.
	 *
	 * @param Rule_Validators_Registry $rule_validators_registry
	 */
	public function __construct( $rule_validators_registry ) {

		$this->rule_validators_registry = $rule_validators_registry;

	}

	/**
	 * Check whether the specified post passes at least one group of rules.
	 *
	 * @param int                      $identifier The post id or term id.
	 * @param array                    $rule_groups An array of rules' groups.
	 * @param $type string Post or term
	 *
	 * @return bool Whether the post passes at least one rule group.
	 */
	public function is_valid( $identifier, $rule_groups, $type ) {

		// Validate each group. Return true as soon as one group is validated (all rules).
		foreach ( (array) $rule_groups as $rule_group ) {
			foreach ( $rule_group['rules'] as $rule ) {
				$rule_field_one   = $rule['rule_field_one'];
				$rule_logic_field = $rule['rule_logic_field'];
				$rule_field_two   = $rule['rule_field_two'];

				$rule_validator = $this->rule_validators_registry->get_rule_validator( $rule_field_one );
				// Skip to the next Rule Group if a rule isn't valid.
				if ( ! $rule_validator->is_valid( $identifier, $rule_logic_field, $rule_field_one, $rule_field_two, $type ) ) {
					continue 2;
				}
			}

			// If we got here it means that all the rules have been validated (or the rules' group has no rules).
			return true;
		}

		return false;
	}

}
