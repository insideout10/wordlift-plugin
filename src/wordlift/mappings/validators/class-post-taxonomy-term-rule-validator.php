<?php
/**
 * This file provides the Post_Type_Rule_Validator which validates whether a post type matches or not the given type.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.25.0
 * @package Wordlift\Mappings\Validators
 */

namespace Wordlift\Mappings\Validators;

use Wordlift\Mappings\Jsonld_Converter;

/**
 * Define the Post_Taxonomy_Rule_Validator class.
 *
 * @package Wordlift\Mappings\Validators
 */
class Post_Taxonomy_Term_Rule_Validator implements Rule_Validator {
	/**
	 * @since 3.25.0
	 * Enum for the post type rule validator.
	 */
	const POST_TAXONOMY = 'post_taxonomy';

	/**
	 * Post_Type_Rule_Validator constructor.
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

		$value[ self::POST ] = $this;

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
	public function is_valid ( $identifier, $operator, $operand_1, $operand_2, $type ) {
        $taxonomy  = $operand_1;
        $term_slug = $operand_2;

        $is_object_in_term = is_object_in_term( $identifier, $taxonomy, $term_slug );

        $taxonomy = get_taxonomy( $term_slug );

        if (!in_array('post', $taxonomy->object_type )) {
            return false;
        }

        if ( is_wp_error( $is_object_in_term ) ) {
            return false;
        }

        return true;
	}
}
