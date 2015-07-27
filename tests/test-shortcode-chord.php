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
        $ent = wl_save_entity( array(
            'uri'               => 'http://example.org/entity1',
            'label'             => 'Entity1',
            'main_type_uri'     => 'http://schema.org/Thing',
            'description'       => 'An example entity.',
            'type_uris'         => array(),
            'related_post_id'   => null,
            'images'            => array(),
            'same_as'           => array()
        ));
	$entities[] = $ent->ID;
        
        $ent = wl_save_entity( array(
            'uri'               => 'http://example.org/entity2',
            'label'             => 'Entity2',
            'main_type_uri'     => 'http://schema.org/Thing',
            'description'       => 'An example entity.',
            'type_uris'         => array(),
            'related_post_id'   => null,
            'images'            => array(),
            'same_as'           => array()
        ));
	$entities[] = $ent->ID;
                
        
        // Creating a fake post
        $content = 'This is a fake post. Ohhh yeah';
        $slug = 'yeah';
        $title = 'Yeah';
		$status = 'publish';
		$type = 'post';
		self::$FIRST_POST_ID = wl_create_post( $content, $slug, $title, $status, $type);
        
        wl_core_add_relation_instances( self::$FIRST_POST_ID, WL_WHAT_RELATION, $entities );
		
		// Creating another fake post and entity (the most connected one)
		
	// Creating a fake post
        $content = 'This is another fake post. Ohhh yeah';
        $slug = 'yeah';
        $title = 'Yeah';
		$status = 'publish';
		$type = 'post';
		$new_post = wl_create_post( $content, $slug, $title, $status, $type);
		
        $ent = wl_save_entity( array(
            'uri'               => 'http://example.org/entity3',
            'label'             => 'Entity3',
            'main_type_uri'     => 'http://schema.org/Thing',
            'description'       => 'Another example entity only related to an entity.',
            'type_uris'         => array(),
            'related_post_id'   => null,
            'images'            => array(),
            'same_as'           => array()
        ));    
           
        self::$MOST_CONNECTED_ENTITY_ID = $ent->ID;

        wl_core_add_relation_instance( $new_post, WL_WHAT_RELATION, self::$MOST_CONNECTED_ENTITY_ID );
        wl_core_add_relation_instance( self::$FIRST_POST_ID, WL_WHAT_RELATION, self::$MOST_CONNECTED_ENTITY_ID );
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
		
    }
    
    function testChordShortcodeMostConnectedEntity() {
    		
		// Check there is a number
    	$e = wl_shortcode_chord_most_referenced_entity_id();
		$this->assertNotNull($e);
		$this->assertEquals(self::$MOST_CONNECTED_ENTITY_ID, $e);
		
    }

}
