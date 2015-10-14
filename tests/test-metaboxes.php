<?php
require_once 'functions.php';

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
    function testWL_Metabox_field_instantiation() {
       
        $metabox = new WL_Metabox();
        
        $$entity_id = 23; // TODO: create entity
        
        $metabox->instantiate_fields( $entity_id );
        // Verify the correct fields have been built.
    }
    
    
    /*
     * Test Fileds. The following are about the base class WL_Metabox_Filed.
     */
    
    
    /*
     * Test the WL_Metabox_Field obj is built properly
     */
    function testWL_Metabox_Field_constructor() {
        $args = array(
            // TODO
        );
        $field = new WL_Metabox_Field( $args );
        // Verify Field has been built correctly
    }
    
    /*
     * Test the WL_Metabox_Field obj print correctly html
     */
    function testWL_Metabox_Field_html() {
        
        // TODO: insert data into DB (also invalid data)
        
        $args = array(
            // TODO
        );
        $field = new WL_Metabox_Field( $args );
        
        // TODO: load data from DB
        
        // verify html methods
        $field->html_wrapper_open();
        $field->html();
        $field->html_input( 'aaaah' );
        $field->html_wrapper_close();
    }
    
    /*
     * Test the WL_Metabox_Field loads data correcly
     */
    function testWL_Metabox_Field_data() {
        
        // TODO: insert data into DB (also invalid data)
        
        $args = array(
            // TODO
        );
        $field = new WL_Metabox_Field( $args );
        
        $field->get_data();
        // Verify data is loaded correctly from DB
        
        $field->sanitize_data( array('', 12, 'aaaaioio') );
        // Verify only valid data pass
        
        $field->sanitize_data_filter( '' );
        // Verify data filter
        
        $field->save_data();
        // Verify new DB values
    }
}