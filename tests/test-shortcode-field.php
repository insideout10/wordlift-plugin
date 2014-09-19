<?php
require_once 'functions.php';

class FieldShortcodeTest extends WP_UnitTestCase
{	
    /**
     * Set up the test.
     */
    function setUp()
    {
        parent::setUp();

        // Configure WordPress with the test settings.
        wl_configure_wordpress_test();

        // Empty the blog.
        wl_empty_blog();
    }

    /**
     * Create:
     *  * 1 Post
     *  * 3 Place entities referenced by the Post
     *  * 1 Person entity reference by the Post
     *
     * Check that only the first 2 entities are returned when calling *wl_set_referenced_entities*.
     *
     * @uses wl_set_referenced_entities to retrieve the entities referenced by a post.
     */
    function testFieldShortcode() {

        $place_id = wl_create_post( "Entity 1 Text", 'entity-1', "Entity 1 Title", 'publish', 'entity' );
        wl_set_entity_main_type( $place_id, 'http://schema.org/Place' );
        add_post_meta( $place_id, WL_CUSTOM_FIELD_GEO_LATITUDE, 40.12, true );
        add_post_meta( $place_id, WL_CUSTOM_FIELD_GEO_LONGITUDE, 72.3, true );
        
        // Correct use
        $result = do_shortcode( "[wl_field id=$place_id name='latitude']" );
        $this->assertContains( '40.12', $result );
        
        // Invalid property name
        $result = do_shortcode( "[wl_field id=$place_id name='tuhdaaaa!']" );
        $this->assertNull( $result );
        
        // Invalid ID (note that in entity editor the id can be omitted)
        $result = do_shortcode( "[wl_field name='latitude']" );
        $this->assertNull( $result );
    }
}
