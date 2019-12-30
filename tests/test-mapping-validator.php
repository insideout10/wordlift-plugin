<?php
/**
 * Tests: Mappings Test.
 *
 * @since 3.25.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Wordlift_Mapping_Validator_Test class.
 *
 * @since 3.25.0
 */
class Wordlift_Mapping_Validator_Test extends WP_UnitTestCase {

	/**
	 * The {@link Wordlift_Mapping_Validator} instance to test.
	 *
	 * @since  3.25.0
	 * @access private
	 * @var \Wordlift_Mapping_Validator $wordlift_mapping_validator_instance The {@link Wordlift_Mapping_Validator} instance to test.
	 */
	private $wordlift_mapping_validator_instance;

	/** Check if validator class can be initalised */
	public function test_can_initialize_validator() {
		$this->assertNotNull( new Wordlift_Mapping_Validator() );
	}
}