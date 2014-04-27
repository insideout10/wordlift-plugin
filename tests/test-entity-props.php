<?php

require_once 'functions.php';

/**
 * Class EntityPropsTest
 */
class EntityPropsTest extends WP_UnitTestCase
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

    function test() {

        $entity_uri = 'http://rdf.salzburgerland.com/events/fafba0aa-1617-4d18-ba13-081d5965cdf9';

        # Create a test entity post.
        $content    = <<<EOF
This October don't miss the <span class="textannotation highlight wl-event" id="urn:enhancement-4f0e0fbc-e981-7852-9521-f4718eafa13f" itemid="http://rdf.salzburgerland.com/magazine/entity/Florianifeier" itemscope="itemscope">Florianifeier</span>, we'll have fun as usual.
EOF;

        $post_id    = wl_create_post( $content, 'test', 'test', 'draft', 'entity' );

        # Set the entity URI.
        wl_set_entity_uri( $post_id, $entity_uri );

        # Check the entity URI to be correct.
        $this->assertEquals( $entity_uri, wl_get_entity_uri( $post_id ) );

        # Check that the entity post ID found by its URI.
        $this->assertEquals( $post_id, wl_get_entity_post_by_uri( $entity_uri )->ID );

        # Prepare the props array simulating what a $_POST would give us.
        $props = array(
            '@id'                                      => array('http://rdf.salzburgerland.com/events/fafba0aa-1617-4d18-ba13-081d5965cdf9'),
            'http://linkedevents.org/ontology/atPlace' => array('http://rdf.salzburgerland.com/places/4b394edc-a0e8-4d85-8160-223b679f66a1'),
            'http://linkedevents.org/ontology/atTime'  => array('http://rdf.salzburgerland.com/events/fafba0aa-1617-4d18-ba13-081d5965cdf9-time'),
            'http://schema.org/image'                  => array('http://interface.deskline.net/Handlers/Document?code=SBG&amp;id=2d7666c8-9a84-4dde-a068-8dc051e51304'),
            'http://www.w3.org/2002/12/cal#dtend'      => array('2014-05-05'),
            'http://www.w3.org/2002/12/cal#dtstart'    => array('2014-05-04'),
            'http://www.w3.org/2002/12/cal#uid'        => array('fafba0aa-1617-4d18-ba13-081d5965cdf9')
        );

        # Save the props.
        wl_entity_props_save( $entity_uri,  $props );

        $this->assertEquals( array('2014-05-04'), get_post_meta( $post_id, WL_CUSTOM_FIELD_CAL_DATE_START ) );
        $this->assertEquals( array('2014-05-05'), get_post_meta( $post_id, WL_CUSTOM_FIELD_CAL_DATE_END ) );

        # Relate the entity to itself.
        wl_set_related_entities( $post_id, array( $post_id) );

        $content_with_microdata = _wl_content_embed_microdata( $post_id, $content );
    }
}

