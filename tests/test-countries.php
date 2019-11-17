<?php
/**
 * Tests: Countries.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Wordlift_Countries_Test class.
 *
 * @since 3.20.0
 */
class Wordlift_Countries_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test getting all the codes
	 *
	 * @since 3.22.5.1
	 */
	function test_given_invalid_json_file_name_should_return_empty_array() {

		// no codes would be populated since an invalid file name is passed
		// so it should return a empty array
		$codes = Wordlift_Countries::get_codes_from_json_file( "invalid_codes_file.json" );
		$this->assertEquals( Wordlift_Countries::get_codes(), array() );

	}

	/**
	 * Test getting all the codes for invalid json file format
	 *
	 * @since 3.22.5.1
	 */
	function test_given_invalid_json_file_format_should_return_empty_array() {

		// no codes would be populated since an invalid file name is passed
		// so it should return a empty array
		$codes = Wordlift_Countries::get_codes_from_json_file( __DIR__."/assets/invalid_country_code_json.json" );
		$this->assertEquals( $codes, array() );

	}
	

}
