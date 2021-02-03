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

    public function test_when_not_equal_to_operator_on_non_term_page_should_return_false() {
        $result = $this->instance->is_valid( null,
            Rule_Validator::IS_NOT_EQUAL_TO,
            'post_taxonomy',
            'category',
            Jsonld_Converter::POST
        );
        $this->assertFalse( $result );
    }

    public function test_when_custom_post_type_rule_given_should_return_false() {
        register_post_type( 'foo', null );
        $post_id                  = $this->factory()->post->create(array('post_type' => 'foo'));
		$result                   = $this->instance->is_valid( $post_id,
			Rule_Validator::IS_EQUAL_TO,
			'post_taxonomy',
			'category',
			Jsonld_Converter::POST
		);
		$this->assertFalse( $result );
	}

}
