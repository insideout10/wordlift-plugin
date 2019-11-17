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
	 * Test getting all the countries.
	 *
	 * @since 3.20.0
	 */
	public function test_get_countries_any_language() {

		$countries = Wordlift_Countries::get_countries();
		$this->assertCount( 61, $countries, 'Expect 60 items.' );

	}

	/**
	 * Test getting the countries for `english`.
	 *
	 * @since 3.20.0
	 */
	public function test_get_countries_english() {

		$countries = Wordlift_Countries::get_countries( 'en' );
		$this->assertCount( 52, $countries, 'Expect 52 items.' );

	}

	/**
	 * Test getting the countries for `italian`.
	 *
	 * @since 3.20.0
	 */
	public function test_get_countries_italian() {

		$countries = Wordlift_Countries::get_countries( 'it' );
		$this->assertCount( 40, $countries, 'Expect 40 items.' );

	}

	/**
	 * Test the codes.
	 *
	 * @since 3.20.0
	 */
	public function test_get_codes() {

		$this->assertNotEmpty( Wordlift_Countries::get_codes(), 'Array must not be empty.' );

	}

}
