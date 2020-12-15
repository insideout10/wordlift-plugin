<?php
/**
 * Tests: Schema.org Sync Service Test.
 *
 * Test the {@link Wordlift_Schemaorg_Sync_Service} class.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Wordlift_Schemaorg_Sync_Service_Test class.
 *
 * @since 3.20.0
 * @group backend
 */
class Wordlift_Schemaorg_Sync_Service_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test the `load_from_file` function.
	 *
	 * @since 3.20.0
	 */
	public function test_load_from_file() {

		$result = Wordlift_Schemaorg_Sync_Service::get_instance()
		                                         ->load_from_file();

		$this->assertTrue( $result );

	}

}
