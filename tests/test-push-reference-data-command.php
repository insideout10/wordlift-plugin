<?php
/**
 * Tests: Push Reference Data Command Test.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Wordlift_Push_Reference_Data_Command_Test class.
 *
 * @since 3.20.0
 */
class Wordlift_Push_Reference_Data_Command_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Push_Reference_Data_Command} to test.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var \Wordlift_Push_Reference_Data_Command $command The {@link Wordlift_Push_Reference_Data_Command} to test.
	 */
	private $command;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		$this->command = new Wordlift_Push_Reference_Data_Command(
			Wordlift_Relation_Service::get_instance(),
			Wordlift_Entity_Service::get_instance(),
			Wordlift_Sparql_Service::get_instance(),
			Wordlift_Configuration_Service::get_instance(),
			Wordlift_Entity_Type_Service::get_instance()
		);

	}

	/**
	 * Test the `__invoke` function.
	 *
	 * @since 3.20.0
	 */
	public function test_invoke() {

		// @@todo
		$this->markTestSkipped( 'Implement test.' );

	}

}
