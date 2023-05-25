<?php
/**
 * This file provides the Post_Taxonomy_Term_Rule_Validator which validates whether a post type matches or not the given type.
 *
 * @author Navdeep Singh <navdeep@wordlift.io>
 * @since 3.27.8
 * @package Wordlift\Mappings\Validators
 */

namespace Wordlift\Mappings\Validators;

/**
 * Define the Post_Taxonomy_Term_Rule_Validator class.
 *
 * @package Wordlift\Mappings\Validators
 */
class Post_Taxonomy_Term_Rule_Validator implements Rule_Validator {
	/**
	 * @since 3.25.0
	 * Enum for the post taxonomy type rule validator.
	 */
	const POST_TAXONOMY = 'post_taxonomy';

	/**
	 * Post_Taxonomy_Term_Rule_Validator constructor.
	 *
	 * When initializing the class hooks to `wl_mappings_rule_validators`.
	 */
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

		$value[ self::POST_TAXONOMY ] = $this;

		return $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_label() {
		return __( 'Post Taxonomy', 'wordlift' );
	}

	/**
	 * {@inheritdoc}
	 */
	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function is_valid( $identifier, $operator, $operand_1, $operand_2, $type ) {
		$taxonomy  = $operand_1;
		$term_slug = $operand_2;
		if ( get_post_type( $identifier ) !== 'post' ) {
			return false;
		}
		$is_object_in_term = is_object_in_term( $identifier, $taxonomy, $term_slug );
		if ( is_wp_error( $is_object_in_term ) ) {
			return false;
		}

		return ( $is_object_in_term && self::IS_EQUAL_TO === $operator )
			   || ( ! $is_object_in_term && self::IS_NOT_EQUAL_TO === $operator );
	}
}
