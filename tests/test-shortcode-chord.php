<?php
require_once 'functions.php';

/**
 * Class ChordShortcodeTest
 */
class ChordShortcodeTest extends WP_UnitTestCase
{
	private static $FIRST_POST_ID =  0;

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
		
		
		// Creating 4 fake entities 
		$entities = array();
		
        $uri         = 'http://example.org/entity1';
        $label       = 'Entity1';
        $type        = 'http://schema.org/Thing';
        $description = 'An example entity.';
        $images      = array();
        $same_as     = array();
        $ent = wl_save_entity( $uri, $label, $type, $description, array(), $images, null, $same_as );
		$entities[] = $ent->ID;
        
        $uri         = 'http://example.org/entity2';
        $label       = 'Entity2';
        $type        = 'http://schema.org/Thing';
        $description = 'An example entity.';
        $images      = array();
        $same_as     = array();
		$ent = wl_save_entity( $uri, $label, $type, $description, array(), $images, null, $same_as );
		$entities[] = $ent->ID;
        
        $uri         = 'http://example.org/entity3';
        $label       = 'Entity3';
        $type        = 'http://schema.org/Thing';
        $description = 'An example entity.';
        $images      = array();
        $same_as     = array();
        $ent = wl_save_entity( $uri, $label, $type, $description, array(), $images, null, $same_as );
		$entities[] = $ent->ID;
        
        
        // Creating a fake post
        $content = "This is a fake post. Ohhh yeah";
        $slug = "yeah";
        $title = "Yeah";
		self::$FIRST_POST_ID = wl_create_post( $content, $slug, $title);
        
        wl_add_related_entities( self::$FIRST_POST_ID, $entities );
		wl_add_related_entities( $entities[0], $entities[1] );
    }

    function testChordShortcodeOutput() {
    	$GLOBALS['post'] = self::$FIRST_POST_ID;
		$markup = wl_shortcode_chord( array() );
		$this->assertNotNull($markup);
    }
    
    function testChordShortcodeAJAX() {
		$chord = wl_ajax_related_entities(self::$FIRST_POST_ID, 10);
		
		// Check there is a result
		$this->assertNotEmpty($chord);
		
		write_log("chordShortcodeAJAX [ chord markup :: " . print_r($chord, true) . "]");
    }
    
    function testChordShortcodeMostConnectedEntity() {
    	
		// Check there is a number
    	$e = wl_get_most_connected_entity();
		$this->assertNotNull($e);
		$this->assertEquals(2222222222222222, $e);
		
		write_log("chordShortcodeMostConnectedEntity [ post id :: $e ]");
    }

}
