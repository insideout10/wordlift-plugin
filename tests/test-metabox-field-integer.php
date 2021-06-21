<?php
/**
 * Tests: Integer Field Metabox Test.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

use Wordlift\Metabox\Field\Wordlift_Metabox_Field_Integer;

/**
 * Test the {@link Wordlift_Metabox_Field_Integer} class.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 * @group metabox
 */
class Wordlift_Metabox_Field_Integer_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test that the integer field is rendered properly
	 * along with all attributes and "Remove" button.
	 *
	 * @since 3.18.0
	 * @group metabox
	 */
	function test_html_input() {

		// Initialize the field.
		$field = new Wordlift_Metabox_Field_Integer( array( 'wl_integer' => array() ), null, Wordlift_Property_Getter::POST );

		// Get the HTML output.
		$output = $field->html_input( 'integer' );

		// Get the expected html.
		$expected = file_get_contents( __DIR__ . '/assets/metabox-integer.html' );

		$this->assertEquals( $output, $expected );

	}

}
