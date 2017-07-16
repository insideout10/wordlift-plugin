<?php
require_once( dirname( __FILE__ ) . '/../src/admin/WL_Metabox/WL_Metabox.php' );
require_once( dirname( __FILE__ ) . '/../src/admin/WL_Metabox/WL_Metabox_Field_duration.php' );

/**
 * Test the {@link WL_Metabox_Field_duration} class.
 *
 * @since 3.14.0
 */
class WL_Metabox_Field_duration_Test  extends Wordlift_Unit_Test_Case {

	/**
	 * Test sanitization usually done during updated. Value should match
	 * the regex
	 */
	function test_sanitize_data_filter() {
		$field = new WL_Metabox_Field_duration( array() );

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
