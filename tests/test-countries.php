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

	private static $empty_array = array(
		'country_code_name_map'     => array(),
		'country_code_language_map' => array(),
	);
	/**
	 * Method to run before every test, we reset the codes value before running test.
	 *
	 * @return void
	 */
	public function setUp() {
		Wordlift_Countries::reset_codes_and_country_codes();
	}
	/**
	 * Test getting all the codes
	 *
	 * @since 3.22.5.1
	 */
	public function test_given_invalid_json_file_name_should_return_empty_array() {

		// no codes would be populated since an invalid file name is passed
		// so it should return a empty array.
		$codes       = Wordlift_Countries::get_codes_from_json_file( 'invalid_codes_file.json' );
		$this->assertEquals( $codes, self::$empty_array );

	}

	/**
	 * Test getting all the codes for invalid json file format
	 *
	 * @since 3.22.5.1
	 */
	public function test_given_invalid_json_file_format_should_return_empty_array() {

		// no codes would be populated since an invalid file name is passed
		// so it should return a empty array.
		$codes = Wordlift_Countries::get_codes_from_json_file( __DIR__ . 'assets/invalid_country_code_json.json' );
		$this->assertEquals( $codes, self::$empty_array );

	}


	/**
	 * Test when a valid json file is given it should return proper array format.
	 *
	 * @since 3.22.5.1
	 */
	public function test_given_valid_json_data_return_proper_array_format() {

		// passing a valid json file.
		$codes = Wordlift_Countries::get_codes_from_json_file( __DIR__ . '/assets/supported-countries.json' );
		// the method should return array of length > 0 on successful parse.
		$this->assertNotEquals( count($codes), 0 );
		$this->assertTrue( array_key_exists( 'country_code_language_map', $codes ) );
		$this->assertTrue( array_key_exists( 'country_code_name_map', $codes ) );

	}
	/**
	 * Create_valid_json_file in tests/assets/ folder
	 *
	 * @return string the file name which was created by this function.
	 */
	private function create_valid_json_file() {
		// we are going to create a valid json file in assets/ folder with one entity
		// and remove it at the end of the test.
		$valid_json                                 = array();
		$valid_json['au']['supportedLang']          = array( 'en' );
		$valid_json['au']['defaultLoc']['loc_name'] = 'austraila';
		$valid_json                                 = json_encode( $valid_json );
		// write this to assets/ folder.
		$new_file_name = __DIR__ . '/assets/valid_countries_json_file.json';
		file_put_contents( $new_file_name, $valid_json);
		return $new_file_name;
	}
	/**
	 * Test when a valid json file is present should return correct country in valid json.
	 *
	 * @since 3.22.5.1
	 */
	public function test_when_valid_json_file_present_should_return_correct_country() {
		$new_file_name = $this->create_valid_json_file();
		// now the file has been written to assets folder.
		// make a call to get_countries, it should return only australia.
		$country_array = Wordlift_Countries::get_countries( 'en', $new_file_name );
		$this->assertTrue( array_key_exists( 'au', $country_array ) );
		// clean up the file at the end of the test.
		if ( file_exists( $new_file_name ) ) {
			unlink( $new_file_name );
		}
	}

	/**
	 * Test lazy load on get_countries method, it should not read the file for
	 * the next call, it should use the old data.
	 *
	 * @since 3.22.5.1
	 */
	public function test_lazy_load_valid_json_file() {
		$new_file_name = $this->create_valid_json_file();
		// make a call to get_countries, now the file was read and
		// stored to static variable Wordlift_Countries::$code.
		Wordlift_Countries::get_countries( 'en', $new_file_name );
		// delete the file.
		if ( file_exists( $new_file_name ) ) {
			unlink( $new_file_name );
		}
		// Since the file is no more, but the values are present, so it should get old value.
		$country_array = Wordlift_Countries::get_countries( 'en', $new_file_name );
		$this->assertTrue( array_key_exists( 'au', $country_array ) );
		// clean up the file at the end of the test.
		if ( file_exists( $new_file_name ) ) {
			unlink( $new_file_name );
		}
	}

}
