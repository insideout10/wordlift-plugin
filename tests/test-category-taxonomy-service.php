<?php
/**
 * Tests: Category Taxonomy Service.
 *
 * @since   3.11.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Category_Taxonomy_Service_Test} class.
 *
 * @since   3.11.0
 * @package Wordlift
 */
class Wordlift_Category_Taxonomy_Service_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test string to array conversion.
	 *
	 * @since 3.11.0
	 */
	public function test_string_to_array_conversion() {

		$array = (array) "Test 1, Test 2";

		$this->assertCount( 1, $array );
		$this->assertEquals( "Test 1, Test 2", $array[0] );

	}

	public function test_query_var() {

		$query = new WP_Query( array(
			'post_type' => array( 'post', 'page', 'movie', 'book' ),
		) );

		$post_type = $query->get( 'post_type' );

		$this->assertTrue( is_array( $post_type ) );

	}

}
