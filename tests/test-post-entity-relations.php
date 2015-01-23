<?php
require_once 'functions.php';

class PostEntityRelationsTest extends WP_UnitTestCase {

	/**
	 * Set up the test.
	 */
	function setUp() {
		parent::setUp();

		wl_configure_wordpress_test();

		wl_empty_blog();
	}

	function testFindByURI() {

		$entity_post_id = wl_create_post( '', 'test_entity', 'Test Entity', 'draft', 'entity' );
		$entity_uri     = wl_get_entity_uri( $entity_post_id );
		wl_set_same_as( $entity_post_id, 'http://example.org/entity/test_entity' );

		$same_as_array = wl_get_same_as( $entity_post_id );
		$this->assertTrue( is_array( $same_as_array ) );
		$this->assertEquals( 'http://example.org/entity/test_entity', $same_as_array[0] );

		wl_set_same_as( $entity_post_id, array(
			'http://example.org/entity/test_entity',
			'http://data.example.org/entity/test_entity'
		) );

		$same_as_array = wl_get_same_as( $entity_post_id );
		$this->assertTrue( is_array( $same_as_array ) );
		$this->assertEquals( 'http://example.org/entity/test_entity', $same_as_array[0] );
		$this->assertEquals( 'http://data.example.org/entity/test_entity', $same_as_array[1] );

		$post = wl_get_entity_post_by_uri( 'http://example.org/entity/test_entity' );
		$this->assertNotNull( $post );

		$post = wl_get_entity_post_by_uri( 'http://data.example.org/entity/test_entity' );
		$this->assertNotNull( $post );

		$same_as_uri = 'http://example.org/entity/test_entity2';

		$entity_post_id = wl_create_post( '', 'test_entity_2', 'Test Entity 2', 'draft', 'entity' );
		$entity_uri     = wl_get_entity_uri( $entity_post_id );
		wl_set_same_as( $entity_post_id, $same_as_uri );

		$same_as_array = wl_get_same_as( $entity_post_id );
		$this->assertTrue( is_array( $same_as_array ) );
		$this->assertEquals( $same_as_uri, $same_as_array[0] );

		$post = wl_get_entity_post_by_uri( 'http://example.org/entity/test_entity' );
		$this->assertNotNull( $post );

	}

	/**
	 * Test *related* methods.
	 */
	function testRelated() {

		$post_id        = wl_create_post( '', 'post-1', 'Post 1' );
		$entity_post_id = wl_create_post( '', 'entity-1', 'Entity 1', 'draft', 'entity' );

		$related_entities = wl_get_referenced_entity_ids( $post_id );
		$this->assertEquals( 0, count( $related_entities ) );

		$related_posts = wl_get_related_post_ids( $entity_post_id );
		$this->assertEquals( 0, count( $related_posts ) );

		wl_add_referenced_entities( $post_id, $entity_post_id );
		$this->assertEquals( 1, count( wl_get_referenced_entity_ids( $post_id ) ) );

		wl_add_related_posts( $entity_post_id, $post_id );
		$this->assertEquals( 1, count( wl_get_related_post_ids( $entity_post_id ) ) );
	}

	/**
	 * Test the wl_get_referencing_posts method.
	 */
	function testReferencingPosts() {

		// Create a couple of sample posts and entities.
		$post_1   = wl_create_post( '', 'post-1', 'Post 1' );
		$post_2   = wl_create_post( '', 'post-2', 'Post 2' );
		$entity_1 = wl_create_post( '', 'entity-1', 'Entity 1', 'draft', 'entity' );
		$entity_2 = wl_create_post( '', 'entity-2', 'Entity 2', 'draft', 'entity' );

		// Reference entity 1 and 2 from post 1.
		wl_add_referenced_entities( $post_1, array( $entity_1, $entity_2 ) );

		// Reference entity 1 from post 2.
		wl_add_referenced_entities( $post_2, $entity_1 );

		// Check that references are returned correctly.
		$posts_referencing_entity_1 = wl_get_referencing_posts( $entity_1 );
		$this->assertCount( 2, $posts_referencing_entity_1 );
		$post_ids = array_map( function ( $post ) {
			return $post->ID;
		}, $posts_referencing_entity_1 );
		$this->assertTrue( in_array( $post_1, $post_ids ) );
		$this->assertTrue( in_array( $post_2, $post_ids ) );

		// Check that references are returned correctly.
		$posts_referencing_entity_2 = wl_get_referencing_posts( $entity_2 );
		$this->assertCount( 1, $posts_referencing_entity_2 );
		$this->assertEquals( $post_1, $posts_referencing_entity_2[0]->ID );
                
	}
        
        function testPost4W() {
            
            // Create a post.
            $post_id = wl_create_post( '', 'post', 'post', 'draft', 'post' );

            // Create the 4W entities.
            // WHAT
            $what_id = wl_create_post( '', 'what', 'Entity What', 'draft', 'entity' );
            
            // WHO
            $who_id = wl_create_post( '', 'who', 'Entity Who', 'draft', 'entity' );
            
            // WHEN
            $when_id = wl_create_post( '', 'when', 'Entity When', 'draft', 'entity' );
            
            // WHERE
            $where_id = wl_create_post( '', 'where', 'Entity Where', 'draft', 'entity' );
                        
            // Bind the 4W to the post.
            wl_add_referenced_entities( $post_id, $what_id, WL_CUSTOM_FIELD_WHAT_ENTITIES );
            wl_add_referenced_entities( $post_id, $when_id, WL_CUSTOM_FIELD_WHAT_ENTITIES );  // another entity on the same W
            wl_add_referenced_entities( $post_id, array( $who_id, $what_id), WL_CUSTOM_FIELD_WHO_ENTITIES );    // assign more than one at the same time
            wl_add_referenced_entities( $post_id, $where_id, WL_CUSTOM_FIELD_WHERE_ENTITIES );
            wl_add_referenced_entities( $post_id, $when_id, WL_CUSTOM_FIELD_WHEN_ENTITIES );

            // Check associations.
            
            // The 4W in an associative array, handcoded
            $w4ByHand = array(
                WL_CUSTOM_FIELD_WHAT_ENTITIES => wl_get_referenced_entity_ids( $post_id, WL_CUSTOM_FIELD_WHAT_ENTITIES ),
                WL_CUSTOM_FIELD_WHERE_ENTITIES => wl_get_referenced_entity_ids( $post_id, WL_CUSTOM_FIELD_WHERE_ENTITIES ),
                WL_CUSTOM_FIELD_WHEN_ENTITIES => wl_get_referenced_entity_ids( $post_id, WL_CUSTOM_FIELD_WHEN_ENTITIES ),
                WL_CUSTOM_FIELD_WHO_ENTITIES => wl_get_referenced_entity_ids( $post_id, WL_CUSTOM_FIELD_WHO_ENTITIES )
            );
            
            // Test WL-generated 4W's associative array
            $w4 = wl_get_post_4w_entities( 279469238 );  // non existent post
            $this->assertEquals( $w4, array() );
            
            $w4 = wl_get_post_4w_entities( $post_id );   // real post
            $this->assertEquals( $w4ByHand, $w4 );
            
            $this->assertEquals( array( $what_id, $when_id ), $w4[WL_CUSTOM_FIELD_WHAT_ENTITIES] );
            $this->assertEquals( array( $who_id, $what_id ), $w4[WL_CUSTOM_FIELD_WHO_ENTITIES] );
            $this->assertEquals( array( $where_id ), $w4[WL_CUSTOM_FIELD_WHERE_ENTITIES] );
            $this->assertEquals( array( $when_id ), $w4[WL_CUSTOM_FIELD_WHEN_ENTITIES] );
            
            
            // Check complmentary associations
            
            // The posts referenced by the 4W in an associative array, handcoded
            /*$w4ByHand = array(
                WL_CUSTOM_FIELD_IS_WHAT_FOR_POSTS => wl_get_related_post_ids( $post_id, WL_CUSTOM_FIELD_IS_WHAT_FOR_POSTS),
                WL_CUSTOM_FIELD_IS_WHERE_FOR_POSTS => wl_get_related_post_ids( $post_id, WL_CUSTOM_FIELD_IS_WHERE_FOR_POSTS),
                WL_CUSTOM_FIELD_IS_WHEN_FOR_POSTS => wl_get_related_post_ids( $post_id, WL_CUSTOM_FIELD_IS_WHEN_FOR_POSTS),
                WL_CUSTOM_FIELD_IS_WHO_FOR_POSTS => wl_get_related_post_ids( $post_id, WL_CUSTOM_FIELD_IS_WHO_FOR_POSTS)
            );*/
            
            // Test WL-generated 4W's associative array
            $w4 = wl_get_entity_is_4w_for_posts( 279469238 );  // non existent post
            $this->assertEquals( $w4, array() );
            
            wl_write_log( 'piedo 4w ' . var_export( wl_get_entity_is_4w_for_posts( $what_id ), true));
            
            $w4 = wl_get_entity_is_4w_for_posts( $post_id );   // real post
            $this->assertEquals( $w4ByHand, $w4 );
            
            $this->assertEquals( array( $what_id, $when_id ), $w4[WL_CUSTOM_FIELD_WHAT_ENTITIES] );
            $this->assertEquals( array( $who_id, $what_id ), $w4[WL_CUSTOM_FIELD_WHO_ENTITIES] );
            $this->assertEquals( array( $where_id ), $w4[WL_CUSTOM_FIELD_WHERE_ENTITIES] );
            $this->assertEquals( array( $when_id ), $w4[WL_CUSTOM_FIELD_WHEN_ENTITIES] );         
    }   
}