<?php
/**
 * Tests: Linked Data Test.
 *
 * @since 3.19.4
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Wordlift_Linked_Data_Test class.
 *
 * @since 3.19.4
 * @group sparql
 */
class Wordlift_Linked_Data_Test extends WP_UnitTestCase {

	/**
	 * Test the `wl_save_entity` function.
	 *
	 * @since 3.19.4
	 */
	public function test_save_entity() {

		// An array with a `label` with the UTF-8 BOM sequence.
		$args = array(
			'label'       => "A label with a \xEF\xBB\xBF UTF-8 BOM.", // The entity label.
			'uri'         => 'http://example.org/lorem-ipsum',
			'main_type'   => 'http://schema.org/Thing',
			'description' => 'Lorem Ipsum',
		);

		// Save the entity.
		$post = wl_save_entity( $args );

		// Ensure the UTF-8 BOM sequence has been removed.
		$this->assertEquals( 'A label with a  UTF-8 BOM.', $post->post_title, 'The UTF-8 BOM sequence must not be there.' );
		$this->assertEquals( 0, preg_match( '/\xEF\xBB\xBF/', $post->post_title ), 'The UTF-8 BOM sequence must not be there.' );
		$this->assertEquals( 0, preg_match( '/\xEF\xBB\xBF/', $post->post_name ), 'The UTF-8 BOM sequence must not be there.' );

	}

}
