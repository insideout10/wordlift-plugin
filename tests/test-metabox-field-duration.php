<?php
/**
 * Tests: Duration Field Metabox Test.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

use Wordlift\Metabox\Field\Wordlift_Metabox_Field_Duration;

/**
 * Test the {@link Wordlift_Metabox_Field_Duration} class.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 * @group metabox
 */
class Wordlift_Metabox_Field_Duration_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test sanitization usually done during updated. Value should match
	 * the regex.
	 *
	 * @since 3.14.0
	 * @group metabox
	 */
	function test_sanitize_data_filter() {

		// Pass null since it is not needed for the test.
		$field = new Wordlift_Metabox_Field_Duration( array(), null, Wordlift_Property_Getter::POST  );

		// Simple minutes value should pass.
		$this->assertEquals( '10', $field->sanitize_data_filter( '10' ) );

		// Hour minutes combo value should pass.
		$this->assertEquals( '12:40', $field->sanitize_data_filter( '12:40' ) );

		// Try to extract valid values for misformated text.
		$this->assertEquals( '35', $field->sanitize_data_filter( '35 min' ) );

		// Fail on garbage.
		$this->assertNull( $field->sanitize_data_filter( 'some minutes' ) );

	}

}
