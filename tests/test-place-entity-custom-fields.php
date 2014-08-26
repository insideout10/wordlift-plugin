<?php

/**
 * Test methods to change Place entities custom fields (latitude and longitude)
 */

require_once 'functions.php';

class PlaceEntityCustomFieldsTest extends WP_UnitTestCase
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

    // Test <input> fields are echoed and in the right page.
    function testHtmlFormEchoedInEditor() {
        
        // Create a Place entity
        $place_entity_id = wl_create_post( "Place", 'place', "Place Title", 'publish', 'entity' );
        wl_set_entity_main_type( $place_entity_id, 'http://schema.org/Place' );
        add_post_meta( $place_entity_id, WL_CUSTOM_FIELD_GEO_LATITUDE, 40.12, true );
        add_post_meta( $place_entity_id, WL_CUSTOM_FIELD_GEO_LONGITUDE, 72.3, true );       
        
        // Create an Event entity
        $event_entity_id = wl_create_post( "Event", 'event', "Event Title", 'publish', 'entity' );
        wl_set_entity_main_type( $event_entity_id, 'http://schema.org/Event' );
        add_post_meta( $event_entity_id, WL_CUSTOM_FIELD_CAL_DATE_START, '2014-01-01', true );
        add_post_meta( $event_entity_id, WL_CUSTOM_FIELD_CAL_DATE_END, '2014-01-07', true );
        
        // Get eference to WP meta box register and empty it.
        global $wp_meta_boxes;
        $wp_meta_boxes = null;
        
        // Meta box should not be added for an event
        $GLOBALS['post'] = $event_entity_id;
        wl_admin_add_entities_meta_box( WL_ENTITY_TYPE_NAME );
        $serial_wp_meta_boxes = json_encode( $wp_meta_boxes );
        $this->assertNotContains( 'wordlift_place_entities_box', $serial_wp_meta_boxes );
        
        // Meta box should be added for a place
        $GLOBALS['post'] = $place_entity_id;
        wl_admin_add_entities_meta_box( WL_ENTITY_TYPE_NAME );
        $serial_wp_meta_boxes = json_encode( $wp_meta_boxes );
        $this->assertContains( 'wordlift_place_entities_box', $serial_wp_meta_boxes );
        
        // Assert meta box actually echoes HTML
        $entity = wl_get_post( $place_entity_id );
        ob_start();
        wl_place_entities_box_content( $entity );
        $HTML = ob_get_contents();
        ob_end_clean();
        $this->assertContains( 'wl_place_coords_map', $HTML);
    }
    
    function testWrongCoordsAreRejected() {     
        // Create a Place entity
        $place_entity_id = wl_create_post( "Place", 'place', "Place Title", 'publish', 'entity' );
        wl_set_entity_main_type( $place_entity_id, 'http://schema.org/Place' );
        add_post_meta( $place_entity_id, WL_CUSTOM_FIELD_GEO_LATITUDE, 40.12, true );
        add_post_meta( $place_entity_id, WL_CUSTOM_FIELD_GEO_LONGITUDE, 72.3, true ); 
        
        // Update with this values
        $latitude = $_POST['wl_place_lat'] = '2014wetwert11';
        $longitude = $_POST['wl_place_lon'] = '';
        
        // Security bypass
        $_POST['wordlift_place_entity_box_nonce'] = wp_create_nonce( 'wordlift_place_entity_box' );
        
        // Save method hooked on editor
        wl_place_entity_type_save_coordinates( $place_entity_id );
        $savedLatitude = get_post_meta( $place_entity_id, WL_CUSTOM_FIELD_GEO_LATITUDE, true );
        $savedLongitude = get_post_meta( $place_entity_id, WL_CUSTOM_FIELD_GEO_LONGITUDE, true );
        
        // Dates ahould not have been changed
        $this->assertNotEquals( $latitude, $savedLatitude);
        $this->assertNotEquals( $longitude, $savedLongitude );
    }
    
    function testRightCoordsAreSaved() {
        // Create a Place entity
        $place_entity_id = wl_create_post( "Place", 'place', "Place Title", 'publish', 'entity' );
        wl_set_entity_main_type( $place_entity_id, 'http://schema.org/Place' );
        add_post_meta( $place_entity_id, WL_CUSTOM_FIELD_GEO_LATITUDE, 40.12, true );
        add_post_meta( $place_entity_id, WL_CUSTOM_FIELD_GEO_LONGITUDE, 72.3, true ); 
        
        // Update with this values
        $latitude = $_POST['wl_place_lat'] = '40.2334';
        $longitude = $_POST['wl_place_lon'] = '12.3454';
        
        // Security bypass
        $_POST['wordlift_place_entity_box_nonce'] = wp_create_nonce( 'wordlift_place_entity_box' );
        
        // Save method hooked on editor
        wl_place_entity_type_save_coordinates( $place_entity_id );
        $savedLatitude = get_post_meta( $place_entity_id, WL_CUSTOM_FIELD_GEO_LATITUDE, true );
        $savedLongitude = get_post_meta( $place_entity_id, WL_CUSTOM_FIELD_GEO_LONGITUDE, true );
        
        // Dates ahould not have been changed
        $this->assertEquals( $latitude, $savedLatitude);
        $this->assertEquals( $longitude, $savedLongitude );
    }
}