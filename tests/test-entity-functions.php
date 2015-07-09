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

        $expected_uri = wl_configuration_get_redlink_dataset_uri() . '/post/This_is_a_test';
        $this->assertEquals($expected_uri, wl_build_entity_uri( $post_id ) );

    }

    /**
     * Check entity URI building.
     */
    function testEntityURIforAnEntity() {

        $post_id = wl_create_post('', 'test', 'This is a test', 'draft', 'entity');

        $expected_uri = wl_configuration_get_redlink_dataset_uri() . '/entity/This_is_a_test';
        $this->assertEquals($expected_uri, wl_build_entity_uri( $post_id ) );

    }
    
    /**
     * Tests the *wl_get_meta_type* function
     */
    function testEntityGetMetaType() {
        
        $type = wl_get_meta_type( WL_CUSTOM_FIELD_GEO_LATITUDE );
        $this->assertEquals( 'double', $type );
        $type = wl_get_meta_type( 'latitude' );
        $this->assertEquals( 'double', $type );
        $this->assertEquals( WL_DATA_TYPE_DOUBLE, $type );
        
        $type = wl_get_meta_type( WL_CUSTOM_FIELD_CAL_DATE_START );
        $this->assertEquals( 'date', $type );
        $type = wl_get_meta_type( 'startDate' );
        $this->assertEquals( 'date', $type );
        $this->assertEquals( WL_DATA_TYPE_DATE, $type );

        $type = wl_get_meta_type( WL_CUSTOM_FIELD_LOCATION );
        $this->assertEquals( 'uri', $type );
        $type = wl_get_meta_type( 'location' );
        $this->assertEquals( 'uri', $type );
        $this->assertEquals( WL_DATA_TYPE_URI, $type );
        
        $type = wl_get_meta_type( 'random_silly_name' );
        $this->assertEquals( null, $type );
    }
    
    
    /**
     * Tests the *wl_get_meta_constraints* function
     */
    function testWlEntityTaxonomyGetCustomFields() {
        // Create entity and get custom_fields by id
        $place_id = wl_create_post( "Entity 1 Text", 'entity-1', "Entity 1 Title", 'publish', 'entity' );
        wl_set_entity_main_type( $place_id, 'http://schema.org/Place' );
        
        $custom_fields = wl_entity_taxonomy_get_custom_fields( $place_id );
        
        $this->assertArrayHasKey( WL_CUSTOM_FIELD_GEO_LATITUDE, $custom_fields );
        $this->assertArrayHasKey( WL_CUSTOM_FIELD_GEO_LONGITUDE, $custom_fields );
        $this->assertArrayHasKey( WL_CUSTOM_FIELD_ADDRESS, $custom_fields );
        $this->assertArrayNotHasKey( WL_CUSTOM_FIELD_LOCATION, $custom_fields );    // Negative test
        $this->assertArrayHasKey( 'predicate', $custom_fields[WL_CUSTOM_FIELD_GEO_LATITUDE] );
        $this->assertArrayHasKey( 'type', $custom_fields[WL_CUSTOM_FIELD_GEO_LATITUDE] );
        $this->assertArrayHasKey( 'export_type', $custom_fields[WL_CUSTOM_FIELD_GEO_LATITUDE] );
        
        // Get all custom_fileds
        $custom_fields = wl_entity_taxonomy_get_custom_fields();
        $custom_fields = json_encode( $custom_fields ); // Stringify for brevity
        
        $this->assertContains( WL_CUSTOM_FIELD_GEO_LATITUDE, $custom_fields );
        $this->assertContains( WL_CUSTOM_FIELD_GEO_LONGITUDE, $custom_fields );
        $this->assertContains( WL_CUSTOM_FIELD_ADDRESS, $custom_fields );
        $this->assertContains( WL_CUSTOM_FIELD_CAL_DATE_START, $custom_fields );
        $this->assertContains( WL_CUSTOM_FIELD_CAL_DATE_END, $custom_fields );
        $this->assertContains( WL_CUSTOM_FIELD_LOCATION, $custom_fields );
    }
    
    function testWlEntityTaxonomyCustomFieldsInheritance() {
        
        // Create entity and set type
        $business_id = wl_create_post( "Entity 1 Text", 'entity-1', "Entity 1 Title", 'publish', 'entity' );
        wl_set_entity_main_type( $business_id, 'http://schema.org/LocalBusiness' );
        
        // Get custom fields
        $custom_fields = wl_entity_taxonomy_get_custom_fields( $business_id );
        
        // Check inherited custom fields:
        // sameAs from Thing
        $this->assertArrayHasKey( WL_CUSTOM_FIELD_SAME_AS, $custom_fields );
        // latitude from Place
        $this->assertArrayHasKey( WL_CUSTOM_FIELD_GEO_LATITUDE, $custom_fields );
        // founder from Organization
        $this->assertArrayHasKey( WL_CUSTOM_FIELD_FOUNDER, $custom_fields );
        // negative test
        $this->assertArrayNotHasKey( WL_CUSTOM_FIELD_CAL_DATE_START, $custom_fields ); 
    }
    
    function testWlEntityTaxonomyMicrodataTemplateInheritance() {
        
        // Create entity and set type
        $business_id = wl_create_post( "Entity 1 Text", 'entity-1', "Entity 1 Title", 'publish', 'entity' );
        wl_set_entity_main_type( $business_id, 'http://schema.org/LocalBusiness' );
        
        // Get microdata template
        $entity_type_details = wl_entity_type_taxonomy_get_type( $business_id );
        wl_write_log('piedo');
        wl_write_log($entity_type_details);
        $microdata_template = $entity_type_details['microdata_template'];
        
        // Check inherited microdata templates:
        // latitude from Place with 'itemtype="http://schema.org/GeoCoordinates"' markup
        $this->assertContains( 'itemtype="http://schema.org/GeoCoordinates"', $microdata_template );
        $this->assertContains( '{{latitude}}', $microdata_template );
        // founder from Organization
        $this->assertContains( '{{founder}}', $microdata_template );
        // negative test
        $this->assertNotContains( '{{startDate}}', $microdata_template ); 
    }
    
    /**
     * Tests the *wl_get_meta_constraints* function
     */
    function testEntityGetMetaConstraints() {
        
        // TODO: complete this test
        $fields = wl_entity_taxonomy_get_custom_fields();
    }
}

