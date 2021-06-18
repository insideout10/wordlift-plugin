<?php
/**
 * Tests: Select Field Metabox Test.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

use Wordlift\Metabox\Field\Wordlift_Metabox_Field_Select;

/**
 * Test the {@link Wordlift_Metabox_Field_Select} class.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 * @group metabox
 */
class Wordlift_Metabox_Field_Select_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test that the select field is rendered properly along with it's options.
	 *
	 * @since 3.18.0
	 * @group metabox
	 */
	function test_html_input() {

		$args = array(
			'wl_select' => array(
				'options' => array(
					'option_1_value' => 'Option 1',
					'option_2'       => 'Second option',
					'value3'         => '3',
				)
			),
		);

		// Initialize the field.
		$field = new Wordlift_Metabox_Field_Select( $args );

		// Get the HTML output.
		$output = $field->html_input( 'select' );

		// Get the expected html.
		$expected = file_get_contents( __DIR__ . '/assets/metabox-select.html' );

		$this->assertEquals( $output, $expected );

	}

}
