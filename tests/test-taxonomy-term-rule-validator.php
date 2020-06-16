<?php

use Wordlift\Mappings\Validators\Rule_Validator;
use Wordlift\Mappings\Validators\Taxonomy_Term_Rule_Validator;

/**
 * This test checks if the taxonomy term validator is correct.
 */
class Taxonomy_Term_Rule_Validator_Test extends Wordlift_Unit_Test_Case {

	/**
	 * @var Taxonomy_Term_Rule_Validator
	 */
	private $instance;

	public function setUp() {
		$this->instance = new Taxonomy_Term_Rule_Validator();
	}

	public function test_when_invalid_rule_given_return_false() {
		$result = $this->instance->is_valid( null,
			Rule_Validator::IS_EQUAL_TO,
			'taxonomy',
			'category'
		);
		$this->assertFalse( $result );
	}

	public function test_when_valid_rule_given_should_return_true() {

	}

}
