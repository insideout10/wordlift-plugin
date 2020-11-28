<?php
/**
 * Tests: Url Property Service Test.
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/850.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Wordlift_Url_Property_Service_Test class.
 *
 * @since 3.20.0
 * @group entity
 */
class Wordlift_Url_Property_Service_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test `get`.
	 *
	 * @since 3.20.0
	 */
	public function test_get() {

		$property = new Wordlift_Url_Property_Service();

		$post_id = $this->factory()->post->create();
		$values  = $property->get( $post_id, 'abc123' );

		$this->assertTrue( is_array( $values ), '`$values` must be an array, instead got ' . var_export( $values, true ) );
		$this->assertEquals( array( get_permalink( $post_id ) ), $values, '`$values` only contain the post permalink.' );

	}

	/**
	 * Test `get` with a filter hook.
	 *
	 * @since 3.20.0
	 */
	public function test_get_filtered() {

		add_filter( 'wl_production_permalink', array( $this, '_production_permalink' ) );

		$property = new Wordlift_Url_Property_Service();

		$post_id = $this->factory()->post->create();

		$values = $property->get( $post_id, 'abc123' );

		$this->assertTrue( is_array( $values ), '`$values` must be an array.' );
		$this->assertEquals( array( 'http://example.org/production_permalink' ), $values, '`$values` only contain the post permalink.' );

		remove_filter( 'wl_production_permalink', array( $this, '_production_permalink' ) );

	}

	/**
	 * Filter to the `wl_production_permalink` hook.
	 *
	 * @since 3.20.0
	 *
	 * @param string $permalink The default permalink.
	 *
	 * @return string The new permalink.
	 */
	public function _production_permalink( $permalink ) {

		return 'http://example.org/production_permalink';
	}

}
