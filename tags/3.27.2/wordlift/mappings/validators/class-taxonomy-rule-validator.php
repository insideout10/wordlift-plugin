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

		$taxonomy  = $operand_1;
		$term_slug = $operand_2;

		$is_object_in_term = is_object_in_term( $post_id, $taxonomy, $term_slug );

		if ( is_wp_error( $is_object_in_term ) ) {
			return false;
		}

		return ( $is_object_in_term && self::IS_EQUAL_TO === $operator )
		       || ( ! $is_object_in_term && self::IS_NOT_EQUAL_TO === $operator );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_label() {
		return __( 'Taxonomy', 'wordlift' );
	}
}
