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
     * Function to compile the microdata_template with the entity properties' values
     * Used by *wl_content_embed_item_microdata*
     */
    function testContentEmbedCompileMicrodataTemplate() {
        // Create entity and properties
        $entities = $this->create_dummy_entities();
        
        // Take away one property value to check for nulls
        update_post_meta( $entities[1], WL_CUSTOM_FIELD_CAL_DATE_END, null );
        
        $template_place = wl_entity_get_type( $entities[0] );
        $template_place = $template_place['microdata_template'];
        $template_event = wl_entity_get_type( $entities[1] );
        $template_event = $template_event['microdata_template'];
        
        // Compile microdata_template
        $compiled_template_place = wl_content_embed_compile_microdata_template( $entities[0], $template_place );
        $compiled_template_event = wl_content_embed_compile_microdata_template($entities[1], $template_event);
        
        // Verify microdata_template compiling, property by property
        $this->assertContains( '<span itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">', $compiled_template_place );
        $this->assertContains( '<span itemprop="latitude" content="40.12"></span>', $compiled_template_place );
        $this->assertContains( '<span itemprop="longitude" content="72.3"></span>', $compiled_template_place );

        $this->assertNotContains( '<span itemprop="endDate" content="2015-10-26"></span>', $compiled_template_event );
        $this->assertContains( '<span itemprop="startDate" content="2014-10-21"></span>', $compiled_template_event );
        $this->assertContains( '<span itemprop="location" itemscope itemtype="http://schema.org/Place" itemid="http://data.redlink.io/161/test/entity/Place">', $compiled_template_event );
        $this->assertContains( '<span itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">', $compiled_template_event );
        $this->assertContains( '<span itemprop="latitude" content="40.12"></span>', $compiled_template_event );
        $this->assertContains( '<span itemprop="longitude" content="72.3"></span>', $compiled_template_event );
        $this->assertContains( '<link itemprop="url" href="http://example.org/?entity=place" />', $compiled_template_event );
        $this->assertContains( '<span itemprop="name" content="Place"></span></span>', $compiled_template_event );
    }
    
    /*
     * Test the high-level function in charge of printing schema.org microdata
     */
    function testContentEmbedMicrodata() {
        
        // Create entities and add properties
        $entities = $this->create_dummy_entities();
        
        $place_uri = wl_get_entity_uri( $entities[0] );
        $event_uri = wl_get_entity_uri( $entities[1] );       
        
        // Create an annotated post containing the entities
        $place_annotation = '<span itemscope itemid="' . $place_uri . '">Velletri</span>';
        $event_annotation = '<span itemscope itemid="' . $event_uri . '">Sagra delle cipolle</span>';
        $content = 'We are going to ' . $place_annotation . ', where we will attend the ' . $event_annotation;
        $post_id = wl_create_post( $content, 'post', 'A post', 'publish', 'post' );

        // Obtain markup
        $markup = _wl_content_embed_microdata( $post_id, $content );
        $right_markup = 'We are going to <span itemscope itemtype="http://schema.org/Place" class="wl-place" itemid="' . $place_uri . '">'
                    . '<span itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">'
                        . '<span itemprop="latitude" content="40.12"></span>'
                        . '<span itemprop="longitude" content="72.3"></span>'
                    . '</span>'
                    . '<link itemprop="url" href="http://example.org/?entity=place" />'
                    . '<span itemprop="name" content="Velletri">Velletri</span>'
                . '</span>'
                . ', where we will attend the <span itemscope itemtype="http://schema.org/Event" class="wl-event" itemid="' . $event_uri . '">'
                        . '<span itemprop="startDate" content="2014-10-21"></span>'
                        . '<span itemprop="endDate" content="2015-10-26"></span>'
                        . '<span itemprop="location" itemscope itemtype="http://schema.org/Place" itemid="http://data.redlink.io/161/test/entity/Place">'
                           . '<span itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">'
                                . '<span itemprop="latitude" content="40.12"></span>'
                                . '<span itemprop="longitude" content="72.3"></span>'
                            . '</span>'
                        . '<link itemprop="url" href="http://example.org/?entity=place" />'
                        . '<span itemprop="name" content="Place"></span>'
                    . '</span>'
                    . '<link itemprop="url" href="http://example.org/?entity=event" />'
                    . '<span itemprop="name" content="Sagra delle cipolle">Sagra delle cipolle</span>'
                . '</span>';
        
        // Take away empty spaces from both
        $empty_regex = '/\s+/';
        $markup = preg_replace( $empty_regex, '', $markup );
        $right_markup = preg_replace( $empty_regex, '', $right_markup );
        
        // Verify correct markup
        $this->assertEquals( $markup, $right_markup );
    }
    
    /*
     * Function to collect the microdata regarding a single entity
     * Used by *wl_content_embed_microdata*
     */
    function testContentEmbedItemMicrodata() {
   
        // Create an entity and add properties
        $entities = $this->create_dummy_entities();
        $event_uri = wl_get_entity_uri( $entities[1] );
        
        // Create an annotated post containing the entity
        $event_annotation = '<span itemscope itemid="' . $event_uri . '">Sagra delle cipolle</span>';
        $content = 'We will attend the ' . $event_annotation;
        wl_create_post( $content, 'post', 'A post', 'publish', 'post' );

        /* Obtain markup, test 1
         * Note that the Event has a 'location' property which expects a Place entity,
         * so also the $itemprop of *wl_content_embed_item_microdata* parameter is tested.
         */
        $markup = wl_content_embed_item_microdata( $content, $event_uri );
        $right_markup = 'We will attend the <span itemscope itemtype="http://schema.org/Event" class="wl-event" itemid="' . $event_uri . '">'
                        . '<span itemprop="startDate" content="2014-10-21"></span>'
                        . '<span itemprop="endDate" content="2015-10-26"></span>'
                        . '<span itemprop="location" itemscope itemtype="http://schema.org/Place" itemid="http://data.redlink.io/161/test/entity/Place">'
                            .'<span itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">'
                                . '<span itemprop="latitude" content="40.12"></span>'
                                . '<span itemprop="longitude" content="72.3"></span>'
                            . '</span>'
                            . '<link itemprop="url" href="http://example.org/?entity=place" />'
                            . '<span itemprop="name" content="Place"></span>'
                        . '</span>'
                    . '<link itemprop="url" href="http://example.org/?entity=event" />'
                    . '<span itemprop="name" content="Sagra delle cipolle">Sagra delle cipolle</span>'
                . '</span>';
        
        // Take away empty spaces from both
        $empty_regex = '/\s+/';
        $markup = preg_replace( $empty_regex, '', $markup );
        $right_markup = preg_replace( $empty_regex, '', $right_markup );
        
        // Verify correct markup
        $this->assertEquals( $markup, $right_markup );
        
        
        /* Obtain markup, test 2
         * This time we will stop the recursion on step 1. Location custom properties must not be present.
         */
        $GLOBALS['wl_content_embed_item_microdata_recursion_count'] = WL_MAX_NUM_RECURSIONS_WHEN_PRINTING_MICRODATA - 1;
        $markup = wl_content_embed_item_microdata( $content, $event_uri, 'testProperty' );
        $right_markup = 'We will attend the <span itemprop="testProperty" itemscope itemtype="http://schema.org/Event" itemid="' . $event_uri . '">'
                    . '<span itemprop="startDate" content="2014-10-21"></span>'
                    . '<span itemprop="endDate" content="2015-10-26"></span>'
                    . '<span itemprop="location" itemscope itemtype="http://schema.org/Place" itemid="http://data.redlink.io/161/test/entity/Place">'
                        . '<link itemprop="url" href="http://example.org/?entity=place"/>'
                        . '<span itemprop="name" content="Place"></span>'   
                    . '</span>'
                    . '<link itemprop="url" href="http://example.org/?entity=event" />'
                    . '<span itemprop="name" content="Sagra delle cipolle"></span>'
                . '</span>';
        
        // Take away empty spaces from both
        $empty_regex = '/\s+/';
        $markup = preg_replace( $empty_regex, '', $markup );
        $right_markup = preg_replace( $empty_regex, '', $right_markup );
        
        // Verify correct markup
        $this->assertEquals( $markup, $right_markup );
        
        
        /* Obtain markup, test 3
         * This time we will stop the recursion on step 0. No microdata except for the Event uri and name.
         */
        $GLOBALS['wl_content_embed_item_microdata_recursion_count'] = WL_MAX_NUM_RECURSIONS_WHEN_PRINTING_MICRODATA;
        $markup = wl_content_embed_item_microdata( $content, $event_uri, 'testProperty' );
        $right_markup = 'We will attend the <span itemprop="testProperty" itemscope itemtype="http://schema.org/Event" itemid="' . $event_uri . '"><link itemprop="url" href="http://example.org/?entity=event" /><span itemprop="name" content="Sagra delle cipolle"></span></span>';
        
        // Verify correct markup
        $this->assertEquals( $markup, $right_markup );
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
        add_post_meta( $event_id, WL_CUSTOM_FIELD_CAL_DATE_END, '2015-10-26', true );
        add_post_meta( $event_id, WL_CUSTOM_FIELD_LOCATION, $place_id, true );
        
        return array( $place_id, $event_id );
    }
}

