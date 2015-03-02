<?php

/**
 * Test Entity functions.
 */
require_once 'functions.php';

/**
 * Class EntityTest
 */
class SchemaApiTest extends WP_UnitTestCase {

    /**
     * Set up the test.
     */
    function setUp() {
        parent::setUp();

        // Configure WordPress with the test settings.
        wl_configure_wordpress_test();

        // Empty the blog.
        wl_empty_blog();
    }

    /**
     * Test set- and get- methods for schema properties
     */
    function testSchemaProperty() {

        $place_id = wl_create_post("Entity 1 Text", 'entity-1', "Entity 1 Title", 'publish', 'entity');
        wl_set_entity_main_type($place_id, 'http://schema.org/Place');
        add_post_meta($place_id, WL_CUSTOM_FIELD_GEO_LATITUDE, 40.12, true);
        add_post_meta($place_id, WL_CUSTOM_FIELD_GEO_LONGITUDE, 72.3, true);

        $event_id = wl_create_post("Entity 2 Text", 'entity-2', "Entity 2 Title", 'publish', 'entity');
        wl_set_entity_main_type($event_id, 'http://schema.org/Event');
        add_post_meta($event_id, WL_CUSTOM_FIELD_CAL_DATE_START, '2014-10-21', true);
        add_post_meta($event_id, WL_CUSTOM_FIELD_CAL_DATE_END, '2015-10-21', true);

        // Positive tests
        $value = wl_get_meta_value('latitude', $place_id);
        $this->assertEquals(40.12, $value[0]);
        $value = wl_get_meta_value('longitude', $place_id);
        $this->assertEquals(72.3, $value[0]);
        $value = wl_get_meta_value('http://schema.org/latitude', $place_id);
        $this->assertEquals(40.12, $value[0]);
        $value = wl_get_meta_value('http://schema.org/longitude', $place_id);
        $this->assertEquals(72.3, $value[0]);
        $value = wl_get_meta_value(WL_CUSTOM_FIELD_GEO_LATITUDE, $place_id);
        $this->assertEquals(40.12, $value[0]);
        $value = wl_get_meta_value(WL_CUSTOM_FIELD_GEO_LONGITUDE, $place_id);
        $this->assertEquals(72.3, $value[0]);

        $value = wl_get_meta_value('startDate', $event_id);
        $this->assertEquals('2014-10-21', $value[0]);
        $value = wl_get_meta_value('endDate', $event_id);
        $this->assertEquals('2015-10-21', $value[0]);
        $value = wl_get_meta_value('http://schema.org/startDate', $event_id);
        $this->assertEquals('2014-10-21', $value[0]);
        $value = wl_get_meta_value('http://schema.org/endDate', $event_id);
        $this->assertEquals('2015-10-21', $value[0]);
        $value = wl_get_meta_value(WL_CUSTOM_FIELD_CAL_DATE_START, $event_id);
        $this->assertEquals('2014-10-21', $value[0]);
        $value = wl_get_meta_value(WL_CUSTOM_FIELD_CAL_DATE_END, $event_id);
        $this->assertEquals('2015-10-21', $value[0]);

        // Negative tests
        $value = wl_get_meta_value(null, $place_id);
        $this->assertEquals(null, $value);
        $value = wl_get_meta_value('latitude', $event_id);
        $this->assertEquals(null, $value);
        $value = wl_get_meta_value('http://invented_url/endDate', $event_id);
        $this->assertEquals(null, $value);
    }

    /**
     * Test set- and get- methods for schema types
     */
    function testSchemaType() {
        
        // Create entity
        $place_id = wl_create_post("Entity 1 Text", 'entity-1', "Entity 1 Title", 'publish', 'entity');

        // Since it has no specified type, it is a Thing
        $type = wl_schema_get_types( $place_id );
        $this->assertEquals( 1, count($type) );
        $type = $type[0];
        $this->assertEquals( 'Thing', $type );
        
        // Assign a non supported type
        wl_schema_set_types( $place_id, 'Ulabadoola' );
        
        // Verify it is still a Thing
        $type = wl_schema_get_types( $place_id );
        $this->assertEquals( 1, count($type) );
        $type = $type[0];
        $this->assertEquals( 'Thing', $type );
        
        // Assign supported type
        wl_schema_set_types( $place_id, 'Place' );
        
        // Verify it is now a Place
        $type = wl_schema_get_types( $place_id );
        $type = $type[0];
        $this->assertEquals( 'Place', $type );
    }

    /**
     * Tests the *wl_schema_get_type_properties* method
     */
    function testSchemaTypeProperties() {
        
    }

    /**
     * Tests the *wl_schema_get_property_expected_type* method
     */
    function testSchemaExpectedType() {
        
    }

}
