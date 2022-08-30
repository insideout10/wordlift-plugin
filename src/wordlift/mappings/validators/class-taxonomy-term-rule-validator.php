<?php
/**
 * This file defines the Taxonomy_Term_Rule_Validator.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.27.0
 * @package Wordlift\Mappings\Validators
 */

namespace Wordlift\Mappings\Validators;

use Wordlift\Mappings\Jsonld_Converter;

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

	public function is_valid( $identifier, $operator, $operand_1, $taxonomy, $type ) {
		if ( Jsonld_Converter::TERM !== $type ) {
			return false;
		}
		$current_term = get_term( $identifier );
		// If it is not a term page, then return false for two operators.
		if ( ! $current_term instanceof \WP_Term ) {
			return false;
		}
		$terms = get_terms( $taxonomy, array( 'get' => 'all' ) );
		$terms = array_map(
			function ( $term ) {
				/**
				 * @var $term \WP_Term
				 */
				return $term->term_id;
			},
			$terms
		);
		if ( Rule_Validator::IS_EQUAL_TO === $operator ) {
			// if we dont have term id, then skip the flow.
			// If we are in term page, then we need to check if the current
			// term belongs to the taxonomy
			return in_array( $current_term->term_id, $terms, true );
		}

		if ( Rule_Validator::IS_NOT_EQUAL_TO === $operator ) {
			return ! in_array( $current_term->term_id, $terms, true );
		}

	}
}
