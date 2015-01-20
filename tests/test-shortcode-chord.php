<?php
require_once 'functions.php';

/**
 * Class ChordShortcodeTest
 */
class ChordShortcodeTest extends WP_UnitTestCase
{
	private static $FIRST_POST_ID;
	private static $MOST_CONNECTED_ENTITY_ID;

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
		
		
		// Creating 2 fake entities 
		$entities = array();
		
        $uri         = 'http://example.org/entity1';
        $label       = 'Entity1';
        $type        = 'http://schema.org/Thing';
        $description = 'An example entity.';
        $images      = array();
        $same_as     = array();
        $ent = wl_save_entity( $uri, $label, $type, $description, array(), $images, $same_as );
		$entities[] = $ent->ID;
        
        $uri         = 'http://example.org/entity2';
        $label       = 'Entity2';
        $type        = 'http://schema.org/Thing';
        $description = 'An example entity.';
        $images      = array();
        $same_as     = array();
		$ent = wl_save_entity( $uri, $label, $type, $description, array(), $images, $same_as );
		$entities[] = $ent->ID;
                
        
        // Creating a fake post
        $content = 'This is a fake post. Ohhh yeah';
        $slug = 'yeah';
        $title = 'Yeah';
		$status = 'publish';
		$type = 'post';
		self::$FIRST_POST_ID = wl_create_post( $content, $slug, $title, $status, $type);
        
        wl_add_referenced_entities( self::$FIRST_POST_ID, $entities );
		
		// Creating another fake post and entity (the most connected one)
		
		// Creating a fake post
        $content = 'This is another fake post. Ohhh yeah';
        $slug = 'yeah';
        $title = 'Yeah';
		$status = 'publish';
		$type = 'post';
		$new_post = wl_create_post( $content, $slug, $title, $status, $type);
		
		$uri         = 'http://example.org/entity3';
        $label       = 'Entity3';
        $type        = 'http://schema.org/Thing';
        $description = 'Another example entity only related to an entity.';
        $images      = array();
        $same_as     = array();
        $ent = wl_save_entity( $uri, $label, $type, $description, array(), $images, $same_as );
		self::$MOST_CONNECTED_ENTITY_ID = $ent->ID;
		
		wl_add_referenced_entities( $new_post, self::$MOST_CONNECTED_ENTITY_ID );
		wl_add_referenced_entities( self::$FIRST_POST_ID, self::$MOST_CONNECTED_ENTITY_ID);
    }

    function testChordShortcodeOutput() {
    	$GLOBALS['post'] = self::$FIRST_POST_ID;
		$markup = wl_shortcode_chord( array() );
		$this->assertNotNull($markup);
    }
    
    function testChordShortcodeAJAX() {
		$chord = wl_shortcode_chord_get_relations(self::$FIRST_POST_ID, 10);
		
		// Check there is a result
		$this->assertNotEmpty($chord);
		$this->assertNotEmpty($chord['entities']);
		$this->assertNotEmpty($chord['relations']);
		
		//wl_write_log("chordShortcodeAJAX [ chord data :: " . print_r($chord, true) . "]");
    }
    
    function testChordShortcodeMostConnectedEntity() {
    		
		// Check there is a number
    	$e = wl_shortcode_chord_most_referenced_entity_id();
		$this->assertNotNull($e);
		$this->assertEquals(self::$MOST_CONNECTED_ENTITY_ID, $e);
		
		//wl_write_log("chordShortcodeMostConnectedEntity [ post id :: $e ]");
    }

    /**
     * Test the *wl_shortcode_chord_get_relations* method and the depth parameter.
     */
    function testGetRelations() {
        // TODO: finalize the following test

        // Create a couple of sample posts and entities.
        $post_1   = wl_create_post( '', 'post-1', 'Post 1' );
        $post_2   = wl_create_post( '', 'post-2', 'Post 2' );

        $entity_1 = wl_create_post( '', 'entity-1', 'Entity 1', 'draft', 'entity' );
        $entity_2 = wl_create_post( '', 'entity-2', 'Entity 2', 'draft', 'entity' );

        // Reference entity 1 and 2 from post 1.
        wl_add_referenced_entities( $post_1, array( $entity_1, $entity_2 ) );

        // Reference entity 1 from post 2.
        wl_add_referenced_entities( $post_2, array( $entity_1 ) );

        var_dump( wl_shortcode_chord_get_relations( $post_1, 0 ) );
    }

}
