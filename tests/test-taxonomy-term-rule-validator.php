<?php
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


}
