<?php

use Wordlift\Mappings\Validators\Rule_Validator;
use Wordlift\Mappings\Validators\Post_Taxonomy_Term_Rule_Validator;
use Wordlift\Mappings\Jsonld_Converter;

/**
 * This test checks if the post taxonomy term validator is correct.
 * @group mappings
 */
class Post_Taxonomy_Term_Rule_Validator_Test extends Wordlift_Unit_Test_Case {

	/**
	 * @var Taxonomy_Term_Rule_Validator
	 */
	private $instance;

	public function setUp() {
		$this->instance = new Post_Taxonomy_Term_Rule_Validator();
	}

    public function test_when_taxonomy_not_post_type() {
        global $wp_query;
        $term                     = wp_create_term(
            'uncategorized',
            'category'
        );
        $term_id                  = $term['term_id'];
        $wp_query->queried_object = get_term( $term_id );
        $result = $this->instance->is_valid( $term_id,
            Rule_Validator::IS_EQUAL_TO,
            'taxonomy',
            'category',
            Jsonld_Converter::TERM
        );
        $this->assertFalse( $result );
    }

	public function test_when_invalid_rule_given_return_false() {
		$result = $this->instance->is_valid( null,
			Rule_Validator::IS_EQUAL_TO,
			'taxonomy',
			'category',
			Jsonld_Converter::TERM
		);
		$this->assertFalse( $result );
	}

	public function test_when_valid_rule_given_should_return_true() {
		global $wp_query;
		$term                     = wp_create_term(
			'uncategorized',
			'category'
		);
		$term_id                  = $term['term_id'];
		$wp_query->queried_object = get_term( $term_id );
		$result                   = $this->instance->is_valid( $term_id,
			Rule_Validator::IS_EQUAL_TO,
			'taxonomy',
			'category',
			Jsonld_Converter::TERM
		);
		$this->assertTrue( $result );
	}

    public function test_when_not_equal_to_operator_on_non_term_page_should_return_false() {
		$result = $this->instance->is_valid( null,
			Rule_Validator::IS_NOT_EQUAL_TO,
			'taxonomy',
			'category',
			Jsonld_Converter::TERM
		);
		$this->assertFalse( $result );
	}

	public function test_when_not_equal_to_operator_on_term_page_should_return_true() {
		global $wp_query;
		register_taxonomy( 'foo', null );
		$term                     = wp_create_term(
			'bar',
			'foo'
		);
		$term_id                  = $term['term_id'];
		$wp_query->queried_object = get_term( $term_id );
		// The term bar dont belong to category
		$result = $this->instance->is_valid( $term_id,
			Rule_Validator::IS_NOT_EQUAL_TO,
			'taxonomy',
			'category',
			Jsonld_Converter::TERM
		);
		$this->assertTrue( $result );
	}

}
