<?php
/**
 * Class ChordShortcodeTest
 * @group widget
 */
class ChordShortcodeTest extends Wordlift_Unit_Test_Case {

	private static $FIRST_POST_ID;
	private static $MOST_CONNECTED_ENTITY_ID;

	/**
	 * Set up the test.
	 */
	function setUp() {
		parent::setUp();

		// Creating 2 fake entities
		$entities = array(
			wl_create_post( 'content', 'entity1', 'title1', 'publish', 'entity' ),
			wl_create_post( 'content', 'entity2', 'title2', 'publish', 'entity' ),
		);

		// Creating a fake post
		self::$FIRST_POST_ID = wl_create_post( 'content', 'post1', 'title1', 'publish', 'post' );

		wl_core_add_relation_instances( self::$FIRST_POST_ID, WL_WHAT_RELATION, $entities );

		// Creating another fake post and entity (the most connected one)

		// Creating a fake post
		$new_post = wl_create_post( 'content', 'post2', 'title2', 'publish', 'post' );

		// Create the most connected entity
		self::$MOST_CONNECTED_ENTITY_ID = wl_create_post( 'content', 'entity2', 'title2', 'publish', 'entity' );

		wl_core_add_relation_instance( $new_post, WL_WHAT_RELATION, self::$MOST_CONNECTED_ENTITY_ID );
		wl_core_add_relation_instance( self::$FIRST_POST_ID, WL_WHAT_RELATION, self::$MOST_CONNECTED_ENTITY_ID );
	}

	function testChordShortcodeOutput() {
		$GLOBALS['post'] = self::$FIRST_POST_ID;

		$chord = new Wordlift_Chord_Shortcode();

		$markup = $chord->render( array() );
		$this->assertNotNull( $markup );
	}

	function testChordShortcodeAJAX() {
		$chord = wl_shortcode_chord_get_relations( self::$FIRST_POST_ID, 10 );

		// Check there is a result
		$this->assertNotEmpty( $chord );
		$this->assertNotEmpty( $chord['entities'] );
		$this->assertNotEmpty( $chord['relations'] );

	}

	function testChordShortcodeMostConnectedEntity() {

		// Check there is a number
		$e = wl_shortcode_chord_most_referenced_entity_id();
		$this->assertNotNull( $e );
		$this->assertEquals( self::$MOST_CONNECTED_ENTITY_ID, $e );

	}

}
