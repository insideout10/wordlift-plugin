<?php

/**
 * Class FieldShortcodeTest
 * @group widget
 */
class FieldShortcodeTest extends Wordlift_Unit_Test_Case {

	/**
	 * Create:
	 *  * 1 Post
	 *  * 3 Place entities referenced by the Post
	 *  * 1 Person entity reference by the Post
	 *
	 */
	function testFieldShortcode() {

		$place_id = wl_create_post( "Entity 1 Text", 'entity-1', "Entity 1 Title", 'publish', 'entity' );
		wl_set_entity_main_type( $place_id, 'http://schema.org/Place' );
		add_post_meta( $place_id, Wordlift_Schema_Service::FIELD_GEO_LATITUDE, 40.12, true );
		add_post_meta( $place_id, Wordlift_Schema_Service::FIELD_GEO_LONGITUDE, 72.3, true );

		// Correct use
		$result = do_shortcode( "[wl_field id=$place_id name='latitude']" );
		$this->assertContains( '40.12', $result );

		// Implicit ID (like we inserted the shortcode in the entity editor)
		$GLOBALS['post'] = get_post( $place_id );   // Set manually post_id
		$result          = do_shortcode( "[wl_field name='latitude']" );
		$this->assertEquals( '40.12', $result );
		// Invalid ID (will ignore it)
		$result = do_shortcode( "[wl_field id='yea' name='latitude']" );
		$this->assertEquals( '40.12', $result );
		unset( $GLOBALS['post'] );

		// Invalid property name
		$result = do_shortcode( "[wl_field id=$place_id name='tuhdaaaa!']" );
		$this->assertEquals( '', $result );
	}
}
