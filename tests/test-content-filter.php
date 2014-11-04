<?php

/**
 * This file covers tests related to the microdata printing routines.
 */

require_once 'functions.php';

class ContentFilterTest extends WP_UnitTestCase
{

    /**
     * Set up the test.
     */
    function setUp()
    {
        parent::setUp();

        wl_configure_wordpress_test();
        wl_empty_blog();
        rl_empty_dataset();
    }

    function testColorCodingOnFrontEnd() {

        $entity_id  = wl_create_post( '', 'entity-1', 'Entity 1', 'draft', 'entity' );
        $entity_uri = wl_get_entity_uri( $entity_id );
        wl_set_entity_main_type( $entity_id, 'http://schema.org/Event' );

//        # Prepare the props array simulating what a $_POST would give us.
//        $props = array(
//            '@id'                                      => array( $entity_uri ),
//            'http://linkedevents.org/ontology/atPlace' => array( 'http://example.org/a-place' ),
//            'http://linkedevents.org/ontology/atTime'  => array( 'http://example.org/a-time' ),
//            'http://schema.org/image'                  => array( 'http://example.org/an-image' ),
//            'http://www.w3.org/2002/12/cal#dtend'      => array( '2014-05-05' ),
//            'http://www.w3.org/2002/12/cal#dtstart'    => array( '2014-05-04' ),
//            'http://www.w3.org/2002/12/cal#uid'        => array( 'fafba0aa-1617-4d18-ba13-081d5965cdf9' )
//        );
//
//        # Save the props.
//        wl_entity_props_save( $entity_uri,  $props );

        # Create a test entity post.
        $content    = <<<EOF
This post is referencing the sample <span id="urn:enhancement-4f0e0fbc-e981-7852-9521-f4718eafa13f" class="textannotation highlight wl-event" itemid="$entity_uri">Entity 1</span>.
EOF;

        $post_id    = wl_create_post( $content, 'post-1', 'Post 1', 'publish', 'post' );
        $post       = get_post( $post_id );

        $this->setColorCode( 'no' );        
        $this->assertNotContains( 'class="wl-event"', wl_content_embed_item_microdata( $post->post_content, $entity_uri ) );
        $this->setColorCode( 'yes' );
        $this->assertContains( 'class="wl-event"', wl_content_embed_item_microdata( $post->post_content, $entity_uri ) );

    }

    function setColorCode( $value ) {

        // Set the default as index.
        $options = get_option( WL_OPTIONS_NAME );
        $options[WL_CONFIG_ENABLE_COLOR_CODING_ON_FRONTEND_NAME] = $value;
        update_option( WL_OPTIONS_NAME, $options );
    }
    
    /*
     * Test the high-level function in charge of printing schema.org microdata
     */
    function testContentEmbedMicrodata() {
        
        // Step 1: Create entities and add properties
        $entities = $this->create_dummy_entities();
        wl_write_log('piedo ' . json_encode($entities));
        // Step 2: Create an annotated post containing the entities
        // Step 3: Verify correct markup
    }
    
    /*
     * Function to collect the microdata regarding a single entity
     * Used by *wl_content_embed_microdata*
     */
    function testContentEmbedItemMicrodata() {
        // Create entity and add properties
        // Verify correct markup
        
    }
    
    /*
     * Function to compile the microdata_template with the entity properties' values
     * Used by *wl_content_embed_item_microdata*
     */
    function testContentEmbedCompileMicrodataTemplate() {
        // Create entity and properties
        // Verify microdata_template compiling
    }
    
    function create_dummy_entities() {
                
        // A place
        $place_id = wl_create_post( 'Place', 'place', 'Place', 'publish', 'entity' );
        wl_set_entity_main_type( $place_id, 'http://schema.org/Place' );
        add_post_meta( $place_id, WL_CUSTOM_FIELD_GEO_LATITUDE, 40.12, true );
        add_post_meta( $place_id, WL_CUSTOM_FIELD_GEO_LONGITUDE, 72.3, true );
        
        // An Event having as location the place above
        $event_id = wl_create_post( 'Event', 'event', 'Event', 'publish', 'entity' );
        wl_set_entity_main_type( $event_id, 'http://schema.org/Event' );
        add_post_meta( $event_id, WL_CUSTOM_FIELD_CAL_DATE_START, '2014-10-21', true );
        add_post_meta( $event_id, WL_CUSTOM_FIELD_CAL_DATE_END, '2015-10-21', true );
        add_post_meta( $event_id, WL_CUSTOM_FIELD_LOCATION, $place_id, true );
        
        return array( $place_id, $event_id );
    }
}

