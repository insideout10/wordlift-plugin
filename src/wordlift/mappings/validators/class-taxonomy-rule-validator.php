<?php
/**
 * This file defines the taxonomy validator.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.25.0
 * @package Wordlift\Mappings\Validators
 */

namespace Wordlift\Mappings\Validators;

/**
 * Class Taxonomy_Rule_Validator
 *
 * @package Wordlift\Mappings\Validators
 */
class Taxonomy_Rule_Validator implements Rule_Validator {

	/**
	 * {@inheritdoc}
	 */
	public function is_valid( $post_id, $operator, $operand_1, $operand_2 ) {

		$taxonomy       = $operand_1;
		$terms          = get_the_terms( $post_id, $taxonomy );
		$terms_id_array = array();
		if ( is_wp_error( $terms ) || 0 === count( $terms ) ) {
			// If no terms present then the rule is invalid.
			return false;
		}
		foreach ( $terms as $term ) {
			array_push( $terms_id_array, (string) $term->term_id );
		}
		if ( self::IS_EQUAL_TO === $operator ) {
			// Rule is made to check if the term is present for post, so
			// do in_array.
			return in_array( (string) $operand_2, $terms_id_array, true );
		} elseif ( self::IS_NOT_EQUAL_TO === $operator ) {
			// The term  should not be present in the post terms.
			return ! in_array( (string) $operand_2, $terms_id_array, true );
		}

		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_label() {
		return __( 'Taxonomy', 'wordlift' );
	}
}
