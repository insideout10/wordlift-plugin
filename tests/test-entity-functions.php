<?php
/**
 * Test Entity functions.
 */

require_once 'functions.php';

/**
 * Class EntityTest
 */
class EntityFunctionsTest extends WP_UnitTestCase
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
     * Check entity URI building.
     */
    function testEntityURIforAPost() {

        $post_id = wl_create_post('', 'test', 'This is a test');

        $expected_uri = wl_config_get_dataset_base_uri() . '/post/This_is_a_test';
        $this->assertEquals($expected_uri, wl_build_entity_uri( $post_id ) );

    }

    /**
     * Check entity URI building.
     */
    function testEntityURIforAnEntity() {

        $post_id = wl_create_post('', 'test', 'This is a test', 'draft', 'entity');

        $expected_uri = wl_config_get_dataset_base_uri() . '/entity/This_is_a_test';
        $this->assertEquals($expected_uri, wl_build_entity_uri( $post_id ) );

    }
    
    /**
     * Tests the *wl_get_meta_value* function
     */
    function testEntityGetMetaValue() {

        $place_id = wl_create_post( "Entity 1 Text", 'entity-1', "Entity 1 Title", 'publish', 'entity' );
        wl_set_entity_main_type( $place_id, 'http://schema.org/Place' );
        add_post_meta( $place_id, WL_CUSTOM_FIELD_GEO_LATITUDE, 40.12, true );
        add_post_meta( $place_id, WL_CUSTOM_FIELD_GEO_LONGITUDE, 72.3, true );

        $event_id = wl_create_post( "Entity 2 Text", 'entity-2', "Entity 2 Title", 'publish', 'entity' );
        wl_set_entity_main_type( $event_id, 'http://schema.org/Event' );
        add_post_meta( $event_id, WL_CUSTOM_FIELD_CAL_DATE_START, '2014-10-21', true );
        add_post_meta( $event_id, WL_CUSTOM_FIELD_CAL_DATE_END, '2015-10-21', true );
        
        // Positive tests
        $value = wl_get_meta_value( 'latitude', $place_id );
        $this->assertEquals( 40.12, $value[0] );
        $value = wl_get_meta_value( 'longitude', $place_id );
        $this->assertEquals( 72.3, $value[0] );
        $value = wl_get_meta_value( WL_CUSTOM_FIELD_GEO_LATITUDE, $place_id );
        $this->assertEquals( 40.12, $value[0] );
        $value = wl_get_meta_value( WL_CUSTOM_FIELD_GEO_LONGITUDE, $place_id );
        $this->assertEquals( 72.3, $value[0] );
        $value = wl_get_meta_value( 'startDate', $event_id );
        $this->assertEquals( '2014-10-21', $value[0] );
        $value = wl_get_meta_value( 'endDate', $event_id );
        $this->assertEquals( '2015-10-21', $value[0] );
        $value = wl_get_meta_value( WL_CUSTOM_FIELD_CAL_DATE_START, $event_id );
        $this->assertEquals( '2014-10-21', $value[0] );
        $value = wl_get_meta_value( WL_CUSTOM_FIELD_CAL_DATE_END, $event_id );
        $this->assertEquals( '2015-10-21', $value[0] );
        
        // Negative tests
        $value = wl_get_meta_value( null, $place_id );
        $this->assertEquals( null, $value );
        $value = wl_get_meta_value( 'latitude', $event_id );
        $this->assertEquals( null, $value );        
    }
    
    /**
     * Tests the *wl_get_meta_type* function
     */
    function testEntityGetMetaType() {
        
        $type = wl_get_meta_type( WL_CUSTOM_FIELD_GEO_LATITUDE );
        $this->assertEquals( 'double', $type );
        $type = wl_get_meta_type( 'latitude' );
        $this->assertEquals( 'double', $type );
        
        $type = wl_get_meta_type( WL_CUSTOM_FIELD_CAL_DATE_START );
        $this->assertEquals( 'double', $type );
        $type = wl_get_meta_type( 'startDate' );
        $this->assertEquals( 'double', $type );        

        $type = wl_get_meta_type( WL_CUSTOM_FIELD_LOCATION );
        $this->assertEquals( 'uri', $type );
        $type = wl_get_meta_type( 'location' );
        $this->assertEquals( 'uri', $type );
    }
    
    /**
     * Tests the *wl_get_meta_constraints* function
     */
    function testEntityGetMetaConstraints() {
   
    }
}

