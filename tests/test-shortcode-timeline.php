<?php
require_once 'functions.php';

/**
 * Class TimelineShortcodeTest
 */
class TimelineShortcodeTest extends WP_UnitTestCase
{
	private static $FIRST_POST_ID;
	private static $MOST_CONNECTED_ENTITY_ID;
	
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
     *  * 3 Event entities of which 2 referenced by the Post
     *  * 1 Person entity reference by the Post
     *
     * Check that only the first 2 Event entities are returned when calling *wl_set_referenced_entities*.
     *
     * @uses wl_set_referenced_entities to retrieve the events referenced by a post.
     */
    function testGetEvents() {

        $post_id = wl_create_post( '', 'post-1', 'Post 1', 'publish', 'post' );

        $entity_1_id = wl_create_post( 'Entity 1 Text', 'entity-1', 'Entity 1', 'publish', 'entity' );
        wl_set_entity_main_type( $entity_1_id, 'http://schema.org/Event' );
        add_post_meta( $entity_1_id, WL_CUSTOM_FIELD_CAL_DATE_START, '2014-01-01', true );
        add_post_meta( $entity_1_id, WL_CUSTOM_FIELD_CAL_DATE_END, '2014-01-07', true );

        $entity_2_id = wl_create_post( 'Entity 2 Text', 'entity-2', 'Entity 2', 'publish', 'entity' );
        wl_set_entity_main_type( $entity_2_id, 'http://schema.org/Event' );
        add_post_meta( $entity_2_id, WL_CUSTOM_FIELD_CAL_DATE_START, '2014-01-02', true );
        add_post_meta( $entity_2_id, WL_CUSTOM_FIELD_CAL_DATE_END, '2014-01-08', true );

        $entity_3_id = wl_create_post( '', 'entity-3', 'Entity 3', 'publish', 'entity' );
        wl_set_entity_main_type( $entity_2_id, 'http://schema.org/Event' );
        add_post_meta( $entity_3_id, WL_CUSTOM_FIELD_CAL_DATE_START, '2014-01-03', true );
        add_post_meta( $entity_3_id, WL_CUSTOM_FIELD_CAL_DATE_END, '2014-01-09', true );

        $entity_4_id = wl_create_post( '', 'entity-4', 'Entity 4', 'publish', 'entity' );
        wl_set_entity_main_type( $entity_2_id, 'http://schema.org/Person' );

        write_log( "[ entity 1 ID :: $entity_1_id ][ entity 2 ID :: $entity_2_id ][ entity 3 ID :: $entity_3_id ][ entity 4 ID :: $entity_4_id ]" );

        wl_set_referenced_entities( $post_id, array(
            $entity_1_id,
            $entity_2_id,
            $entity_4_id
        ) );

        $events = wl_shortcode_timeline_get_events( $post_id );
        $this->assertCount( 2, $events );

        $event_ids = array_map( function ( $item ) { return $item->ID; }, $events );
        $this->assertContains( $entity_1_id, $event_ids );
        $this->assertContains( $entity_2_id, $event_ids );

        $json = wl_shortcode_timeline_to_json( $events );

        $response = json_decode( $json );

        $this->assertTrue( isset( $response->timeline ) );
        $this->assertCount( 2, $response->timeline->date );
        $this->assertEquals( 'default', $response->timeline->type );

        echo $json;
    }
}
