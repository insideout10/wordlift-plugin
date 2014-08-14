<?php

/**
 * Test methods to change Event entities custom fields (start, end date)
 */

require_once 'functions.php';

class EventEntityCustomFieldsTest extends WP_UnitTestCase
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
        
        // Reference to WP meta box register
        global $wp_meta_boxes;
        
        // Meta box should not be added for a place
        $GLOBALS['post'] = $place_entity_id;
        wl_admin_add_entities_meta_box( WL_ENTITY_TYPE_NAME );
        $serial_wp_meta_boxes = json_encode( $wp_meta_boxes );
        $this->assertNotContains( 'wordlift_event_entities_box', $serial_wp_meta_boxes );
        
        // Meta box should be added for an event
        $GLOBALS['post'] = $event_entity_id;
        wl_admin_add_entities_meta_box( WL_ENTITY_TYPE_NAME );
        $serial_wp_meta_boxes = json_encode( $wp_meta_boxes );
        $this->assertContains( 'wordlift_event_entities_box', $serial_wp_meta_boxes );
        
        // Assert meta box actually echoes HTML
        $entity = wl_get_post( $event_entity_id );
        ob_start();
        wl_event_entities_box_content( $entity );
        $HTML = ob_get_contents();
        ob_end_clean();
        $this->assertContains( 'wl_datepicker', $HTML);
    }
    
    function testWrongDatesAreRejected() {
        
        // Create an Event entity
        $event_entity_id = wl_create_post( "Event", 'event', "Event Title", 'publish', 'entity' );
        wl_set_entity_main_type( $event_entity_id, 'http://schema.org/Event' );
        add_post_meta( $event_entity_id, WL_CUSTOM_FIELD_CAL_DATE_START, '2014-01-01', true );
        add_post_meta( $event_entity_id, WL_CUSTOM_FIELD_CAL_DATE_END, '2014-01-07', true );
        
        // Update with this values
        $start = $_POST['wl_event_start'] = '2014wetwert11';
        $end = $_POST['wl_event_end'] = '';
        
        // Security bypass
        $_POST['wordlift_event_entity_box_nonce'] = wp_create_nonce( 'wordlift_event_entity_box' );
        
        // Save method hooked on editor
        wl_event_entity_type_save_start_and_end_date( $event_entity_id );
        $savedStart = get_post_meta( $event_entity_id, WL_CUSTOM_FIELD_CAL_DATE_START, true );
        $savedEnd = get_post_meta( $event_entity_id, WL_CUSTOM_FIELD_CAL_DATE_END, true );
        
        // Dates ahould not have been changed
        $this->assertNotEquals( $start, $savedStart);
        $this->assertNotEquals( $end, $savedEnd );
    }
    
    function testRightDatesAreSaved() {
        // Create an Event entity
        $event_entity_id = wl_create_post( "Event", 'event', "Event Title", 'publish', 'entity' );
        wl_set_entity_main_type( $event_entity_id, 'http://schema.org/Event' );
        add_post_meta( $event_entity_id, WL_CUSTOM_FIELD_CAL_DATE_START, '2014-01-01', true );
        add_post_meta( $event_entity_id, WL_CUSTOM_FIELD_CAL_DATE_END, '2014-01-07', true );
        
        // Update with this values
        $start = $_POST['wl_event_start'] = '2014-10-11';
        $end = $_POST['wl_event_end'] = '2014-09-02';
        
        // Security bypass
        $_POST['wordlift_event_entity_box_nonce'] = wp_create_nonce( 'wordlift_event_entity_box' );
        
        // Save method hooked on editor
        wl_event_entity_type_save_start_and_end_date( $event_entity_id );
        $savedStart = get_post_meta( $event_entity_id, WL_CUSTOM_FIELD_CAL_DATE_START, true );
        $savedEnd = get_post_meta( $event_entity_id, WL_CUSTOM_FIELD_CAL_DATE_END, true );
        
        // Dates should have been changed
        $this->assertEquals( $start, $savedStart);
        $this->assertEquals( $end, $savedEnd );
    }
}