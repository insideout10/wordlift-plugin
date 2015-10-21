<?php
require_once 'functions.php';
require_once( dirname( __FILE__ ) . '/../src/admin/WL_Metabox/WL_Metabox.php' );

/**
 * Class MetaboxTest
 */
class MetaboxTest extends WP_UnitTestCase
{

    /**
     * Set up the test.
     */
    function setUp()
    {
        parent::setUp();

        // Configure WordPress with the test settings.
        wl_configure_wordpress_test();

        // Reset data on the remote dataset.
        rl_empty_dataset();

        // Empty the blog.
        wl_empty_blog();
    }

    /*
     * Test the WL_Metabox obj is built properly
     */
    function testWL_Metabox_constructor() {
       
        $metabox = new WL_Metabox();
        
        // Verify the object has been built (verify hooks, mainly)
    }
    
    /*
     * Test the WL_Metabox fields are built properly
     */
    function testWL_Metabox_fields_instantiation() {
       
        $metabox = new WL_Metabox();
        
        $entity_id = 23; // TODO: create entity
        
        $metabox->instantiate_fields( $entity_id );
        // Verify the correct fields have been built.
    }
    
    
    /*
     * Test Fields. The following are about the base class WL_Metabox_Filed.
     */
    
    // TODO: Base class :(
    
    
    
    
    /*
     * Test the WL_Metabox_Field_uri obj is built properly
     */
    function testWL_Metabox_Field_uri_constructor() {
        
        // Build a single Field
        $author_custom_field = $this->getSampleCustomField( WL_DATA_TYPE_URI );
        $field = new WL_Metabox_Field_uri( $author_custom_field );
        
        // Verify Field has been built correctly
        $this->assertEquals( WL_CUSTOM_FIELD_AUTHOR, $field->meta_name );
        $this->assertEquals( 'http://schema.org/author', $field->predicate );
        $this->assertEquals( 'author', $field->label );
        $this->assertEquals( WL_DATA_TYPE_URI, $field->expected_wl_type );
        $this->assertEquals( array('Person', 'Organization'), $field->expected_uri_type );
        $this->assertEquals( INF, $field->cardinality );
        
        // Stress the constructor with invalid data
        $field = new WL_Metabox_Field_uri( null );
        $emptyField = array(
            'meta_name' => null,
            'raw_custom_field' => null,
            'predicate' => null,
            'label' => null,
            'expected_wl_type' => null,
            'expected_uri_type' => null,
            'cardinality' => null,
            'data' => null
        );
        $this->assertEquals( $emptyField, (array) $field );
    }
    
    /*
     * Test the WL_Metabox_Field_uri obj print correctly html
     */
    function testWL_Metabox_Field_uri_html() {
        
        // TODO: insert data into DB (also invalid data)
        
        $args = $this->getSampleCustomField( WL_DATA_TYPE_URI );
        $field = new WL_Metabox_Field_uri( $args );
        
        // TODO: load data from DB
        
        // verify html methods
        $html = $field->html();
        
        $this->assertContains( 'class="wl-metabox"', $html );                                       // CSS class
        $this->assertContains( 'data-cardinality="INF"', $html );                                   // Cardinality
        $this->assertContains( 'data-expected-types="Person,Organization"', $html );                // Expected types
        $this->assertContains( 'name="wl_metaboxes[wl_author][]"', $html );                         // $_POST array
        $this->assertContains( 'button class="wl-remove-input"', $html );                           // Remove button
        $this->assertContains( 'button class="wl-add-input"', $html );                              // Add button
    }
    
    /*
     * Test the WL_Metabox_Field loads data correcly
     */
    function testWL_Metabox_Field_uri_data() {
        
        // Build a single Field
        $author_custom_field = $this->getSampleCustomField( WL_DATA_TYPE_URI );
        $field = new WL_Metabox_Field_uri( $author_custom_field );
        /*
        wl_write_log('piedo');
        wl_write_log( $field );
        
        $field->get_data();
        // Verify data is loaded correctly from DB
        
        $field->sanitize_data( array('', 12, 'aaaaioio') );
        // Verify only valid data pass
        
        $field->sanitize_data_filter( '' );
        // Verify data filter
        
        $field->save_data();
        // Verify new DB values
        
         */
    }
    
    function getSampleCustomField( $type ){
        
        if( $type == WL_DATA_TYPE_URI ){
            return array(
                WL_CUSTOM_FIELD_AUTHOR => array(
                    'predicate' => 'http://schema.org/author',
                    'type' => WL_DATA_TYPE_URI,
                    'export_type' => 'http://schema.org/Person',
                    'constraints' => array(
                        'uri_type' => array('Person', 'Organization'),
                        'cardinality' => INF
                    )
                ),
            );
        }
        
        if( $type == WL_DATA_TYPE_STRING ){
            return array(
                // TODO
            );
        }
    }
}