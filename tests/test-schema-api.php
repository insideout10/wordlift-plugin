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

        $place_id = wl_create_post( 'Entity 1 Text', 'entity-1', 'Entity 1 Title', 'publish', 'entity' );
        wl_schema_set_types( $place_id, 'Place');
        wl_schema_add_value( $place_id, 'sameAs', 'http://dbpedia.org/resource/my-place' );
        wl_schema_add_value( $place_id, 'sameAs', 'http://rdf.freebase.com/my-place' );
        wl_schema_set_value( $place_id, 'latitude', 40.12 );

        $event_id = wl_create_post("Entity 2 Text", 'entity-2', "Entity 2 Title", 'publish', 'entity');
        wl_schema_set_types( $event_id, 'Event' );
        wl_schema_set_value( $event_id, 'sameAs', array(
            'http://rdf.freebase.com/my-event',
            'http://dbpedia.org/resource/my-event'
        ));
        wl_schema_set_value( $event_id, 'startDate', '2014-10-21');

        // Positive tests
        $value = wl_schema_get_value( $place_id, 'sameAs' );
        $this->assertEquals( array('http://rdf.freebase.com/my-place', 'http://dbpedia.org/resource/my-place') , $value );
        $value = wl_schema_get_value( $place_id, 'latitude' );
        $this->assertEquals( array( 40.12 ) , $value );
        
        $value = wl_schema_get_value( $event_id, 'sameAs' );
        $this->assertEquals( array('http://rdf.freebase.com/my-event', 'http://dbpedia.org/resource/my-event') , $value );
        $value = wl_schema_get_value( $event_id, 'startDate' );
        $this->assertEquals( array( '2014-10-21' ) , $value );

        // Negative tests
        $value = wl_schema_get_value( $place_id, null );
        $this->assertEquals( null, $value );
        $value = wl_schema_get_value( $place_id, 'startDate' );
        $this->assertEquals( null, $value );
        $value = wl_schema_get_value( $place_id, 'http://invented_url/something' );
        $this->assertEquals( null, $value );
        
        //TODO: manage case of multiple values per property.
    }

    /**
     * Test set- and get- methods for schema types
     */
    function testSchemaType() {
        
        // Create entity
        $place_id = wl_create_post("Entity 1 Text", 'entity-1', "Entity 1 Title", 'publish', 'entity');

        // Type not specified, so it must be a schema.org/Thing
        $type = wl_schema_get_types( $place_id );
        $this->assertEquals( 1, count($type) );
        $this->assertEquals( 'http://schema.org/Thing', $type[0] );
        
        // Assign a non supported type
        wl_schema_set_types( $place_id, 'Ulabadoola' );
        
        // Verify still a Thing
        $type = wl_schema_get_types( $place_id );
        $this->assertEquals( 1, count($type) );
        $this->assertEquals( 'http://schema.org/Thing', $type[0] );
        
        // Assign supported type
        wl_schema_set_types( $place_id, 'Place' );
        
        // Verify it is now a Place
        $type = wl_schema_get_types( $place_id );
        $this->assertEquals( array( 'http://schema.org/Place' ), $type );
    }

    /**
     * Tests the *wl_schema_get_type_properties* method
     */
    function testSchemaTypeProperties() {      
        
        // Invalid calls
        $properties = wl_schema_get_type_properties( 'Yuuuuuuppidooo' );
        $this->assertEquals( array(), $properties );
        $properties = wl_schema_get_type_properties( '' );
        $this->assertEquals( array(), $properties );
        $properties = wl_schema_get_type_properties( null );
        $this->assertEquals( array(), $properties );
        
        // Valid call
        $properties = wl_schema_get_type_properties( 'LocalBusiness' );
        
        // Check properties for LocalBusiness ( as a side effect we also test inheritance! )
        $this->assertContains( 'sameAs', $properties );
        $this->assertContains( 'streetAddress', $properties );
        $this->assertContains( 'latitude', $properties );
        $this->assertContains( 'founder', $properties );
        $this->assertNotContains( 'startDate', $properties );
        
        // Valid call alternative
        $properties = wl_schema_get_type_properties( 'http://schema.org/LocalBusiness' );
        $this->assertContains( 'sameAs', $properties );
        $this->assertContains( 'streetAddress', $properties );
        $this->assertContains( 'latitude', $properties );
        $this->assertContains( 'founder', $properties );
        $this->assertNotContains( 'startDate', $properties );
    }

    /**
     * Tests the *wl_schema_get_property_expected_type* method
     */
    function testSchemaExpectedType() {

        // Test properties expecting a simple tye
        // TODO: add tests for integer and boolean types (we have no examples right now)
        $this->assertEquals( array( Wordlift_Schema_Service::DATA_TYPE_URI ), wl_schema_get_property_expected_type( 'sameAs' ) );
        $this->assertEquals( array( Wordlift_Schema_Service::DATA_TYPE_DATE ), wl_schema_get_property_expected_type( 'endDate' ) );
        //$this->assertEquals( array( WL_DATA_TYPE_INTEGER ), wl_schema_get_property_expected_type( 'xxxxxx' ) );
        $this->assertEquals( array( Wordlift_Schema_Service::DATA_TYPE_DOUBLE ), wl_schema_get_property_expected_type( 'latitude' ) );
        //$this->assertEquals( array( WL_DATA_TYPE_BOOLEAN ), wl_schema_get_property_expected_type( 'xxxxxx' ) );
        $this->assertEquals( array( Wordlift_Schema_Service::DATA_TYPE_STRING ), wl_schema_get_property_expected_type( 'streetAddress' ) );
        
        // Test properties expecting a schema type
        $this->assertEquals( array( 'http://schema.org/Person' ), wl_schema_get_property_expected_type( 'founder' ) );
        $this->assertEquals( array( 'http://schema.org/Place' ), wl_schema_get_property_expected_type( 'location' ) );
        
        // Negative tests
        $this->assertEquals( null, wl_schema_get_property_expected_type( 'Yuppidoooo' ) );
        $this->assertEquals( null, wl_schema_get_property_expected_type( null ) );
    }
}
