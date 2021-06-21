<?php
require_once( dirname( __FILE__ ) . '/../src/includes/properties/class-wordlift-simple-property-service.php' );
require_once( dirname( __FILE__ ) . '/../src/includes/properties/class-wordlift-duration-property-service.php' );

/**
 * Test the {@link Wordlift_Duration_Property_Service} class.
 *
 * @since 3.14.0
 * @group backend
 */
class Wordlift_Duration_Property_Service_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test conversions from possible valid values to json-LD expected values
	 */
	function test_sanitize_data_filter() {
		$converter = new Wordlift_Duration_Property_Service();

		// Create a fake post.
		$id = $this->factory->post->create( array(
			'post_title' => 'Test Duration Property Service test_sanitize_data_filter',
		) );

		// Set a "fake" meta with a number of minutes
		update_post_meta( $id, 'duration', '10' );
		$v = $converter->get( $id, 'duration', Wordlift_Property_Getter::POST );
		// Get return an array with one value.
		$this->assertEquals( 'PT10M', $v[0] );

		// Set a "fake" meta with a hh:mm format
		update_post_meta( $id, 'duration', '3:13' );
		$v = $converter->get( $id, 'duration', Wordlift_Property_Getter::POST );
		// Get return an array with one value.
		$this->assertEquals( 'PT3H13M', $v[0] );
	}
}
