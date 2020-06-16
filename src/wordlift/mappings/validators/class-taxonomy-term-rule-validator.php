<?php

class Taxonomy_Term_Rule_Validator implements \Wordlift\Mappings\Validators\Rule_Validator {

	public function get_label() {
		return __( 'TaxonomyTerm', 'wordlift' );
	}

	public function is_valid( $post_id, $operator, $operand_1, $operand_2 ) {
		// TODO: Implement is_valid() method.
	}
}
