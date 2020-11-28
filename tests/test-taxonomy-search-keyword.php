<?php
/**
 * Tests: Search Keyword Taxonomy Test.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Wordlift_Search_Keyword_Taxonomy_Test class.
 *
 * @since 3.20.0
 * @group taxonomy
 */
class Wordlift_Search_Keyword_Taxonomy_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test that the taxonomy is initialized.
	 *
	 * @since 3.20.0
	 */
	public function test_init() {

		$result = get_taxonomy( Wordlift_Search_Keyword_Taxonomy::TAXONOMY_NAME );
		$this->assertNotFalse( $result, 'The taxonomy myst exist.' );

	}

	/**
	 * Test that calling delete, makes a remote request.
	 *
	 * @since 3.20.0
	 */
	public function test_delete() {

		add_filter( 'pre_http_request', array( $this, '__pre_http_request__test_delete' ), 10, 3 );

		Wordlift_Search_Keyword_Taxonomy::get_instance()->delete( 0, 0, array( 'name' => 'Lorem Ipsum' ) );

		remove_filter( 'pre_http_request', array( $this, '__pre_http_request__test_delete' ) );

	}

	/**
	 * @param bool   $preempt The previous `$preempt` value.
	 * @param array  $r A request array.
	 * @param string $url A request URL.
	 *
	 * @return bool The `$preempt` value.
	 */
	public function __pre_http_request__test_delete( $preempt, $r, $url ) {

		$this->assertStringEndsWith( '/keywords/Lorem%20Ipsum', $url, 'URL must end with the `/keywords/Lorem%20Ipsum` suffix.' );
		$this->assertArraySubset( array( 'method' => 'DELETE' ), $r );

		return $preempt;
	}

}
