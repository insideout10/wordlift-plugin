<?php
require_once 'functions.php';

/**
 * Class ChordShortcodeTest
 */
class ChordShortcodeTest extends WP_UnitTestCase
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

        // Check that entities and posts have been deleted.
        $this->assertEquals( 0, count( get_posts( array(
            'posts_per_page' => -1,
            'post_type'      => 'post',
            'post_status'    => 'any'
        ) ) ) );
        $this->assertEquals( 0, count( get_posts( array(
            'posts_per_page' => -1,
            'post_type'      => 'entity',
            'post_status'    => 'any'
        ) ) ) );

    }

    function testChordShortcodeOutput() {

		// Creating 4 fake entities 
		
        $uri         = 'http://example.org/entity1';
        $label       = 'Entity1';
        $type        = 'http://schema.org/Thing';
        $description = 'An example entity.';
        $images      = array();
        $same_as     = array();
        wl_save_entity( $uri, $label, $type, $description, array(), $images, null, $same_as );
        
        $uri         = 'http://example.org/entity2';
        $label       = 'Entity2';
        $type        = 'http://schema.org/Thing';
        $description = 'An example entity.';
        $images      = array();
        $same_as     = array();
        wl_save_entity( $uri, $label, $type, $description, array(), $images, null, $same_as );
        
        $uri         = 'http://example.org/entity3';
        $label       = 'Entity3';
        $type        = 'http://schema.org/Thing';
        $description = 'An example entity.';
        $images      = array();
        $same_as     = array();
        wl_save_entity( $uri, $label, $type, $description, array(), $images, null, $same_as );
        
        
        // Creating a fake post_author
        
        $content = "This is a fake post. Ohhh yeah";
        $slug = "yeah";
        $title = "Yeah";
        wl_create_post( $content, $slug, $title);
        
        //wl_add_related_entities
        
        //write_log("checkEntity [ post id :: $post->ID ][ uri :: $uri ]");
    }
    
    function testChordShortcodeAJAX() {
    
    }
    
    function testChordShortcodeMostRelatedEntity() {
    
    }

}
