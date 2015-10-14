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
        
        $args = array(
            // TODO
        );
        
        $metabox = new WL_Metabox();
    }
    
    /*
     * Test the WL_Metabox obj print correctly html
     */
    function testWL_Metabox_html() {
        
    }
    
    /*
     * Test the WL_Metabox loads and saves data correcly
     */
    function testWL_Metabox_data() {
        
    }
    
    /*
     * Test the WL_Metabox_Field obj is built properly
     */
    function testWL_Metabox_Field_constructor() {
        
    }
    
    /*
     * Test the WL_Metabox_Field obj print correctly html
     */
    function testWL_Metabox_Field_html() {
        
    }
    
    /*
     * Test the WL_Metabox_Field loads and saves data correcly
     */
    function testWL_Metabox_Field_data() {
        
    }
}