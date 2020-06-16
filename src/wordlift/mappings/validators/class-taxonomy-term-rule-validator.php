<?php
/**
 * This file defines the Taxonomy_Term_Rule_Validator.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.27.0
 * @package Wordlift\Mappings\Validators
 */

namespace Wordlift\Mappings\Validators;

/**
 * Class Taxonomy_Term_Rule_Validator helps to
 * validate on term pages which belongs to a specific taxonomy
 */
class Taxonomy_Term_Rule_Validator implements Rule_Validator {

	const TAXONOMY = 'taxonomy';

	public function __construct() {
		add_filter( 'wl_mappings_rule_validators', array( $this, 'wl_mappings_rule_validators' ) );
	}

	/**
	 * Hook to `wl_mappings_rule_validators` to register ourselves.
	 *
	 * @param array $value An array with validators.
	 *
	 * @return array An array with validators plus ours.
	 */
	public function wl_mappings_rule_validators( $value ) {
		$value[ self::TAXONOMY ] = $this;

		return $value;
	}


	public function get_label() {
		return __( 'TaxonomyTerm', 'wordlift' );
	}

	public function is_valid( $post_id, $operator, $operand_1, $operand_2 ) {
		/*
		 * post_id should not be used since we validate this for term pages.
		 */
		$current_term_id = get_query_var( 'term' );

		if ( $operator === Rule_Validator::IS_EQUAL_TO ) {

			// if we dont have term id, then skip the flow.
			if ( ! is_numeric( $current_term_id ) ) {
				return false;
			}
			// If we have term id, check if the term belongs to taxonomy.

		}

	}
}
