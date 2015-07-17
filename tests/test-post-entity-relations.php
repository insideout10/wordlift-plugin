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
		wl_schema_set_value( $entity_post_id, 'sameAs', 'http://example.org/entity/test_entity' );

		$same_as_array = wl_schema_get_value( $entity_post_id, 'sameAs' );
		$this->assertTrue( is_array( $same_as_array ) );
		$this->assertEquals( 'http://example.org/entity/test_entity', $same_as_array[0] );

		wl_schema_set_value( $entity_post_id, 'sameAs', array(
			'http://example.org/entity/test_entity',
			'http://data.example.org/entity/test_entity'
		) );

		$same_as_array = wl_schema_get_value( $entity_post_id, 'sameAs' );
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
		wl_schema_set_value( $entity_post_id, 'sameAs', $same_as_uri );

		$same_as_array = wl_schema_get_value( $entity_post_id, 'sameAs' );
		$this->assertTrue( is_array( $same_as_array ) );
		$this->assertEquals( $same_as_uri, $same_as_array[0] );

		$post = wl_get_entity_post_by_uri( 'http://example.org/entity/test_entity' );
		$this->assertNotNull( $post );

	}
        
        /*
         * Test *related* methods
         */
        
        function testAddRelationInstance() {
            
            // Create a post and an entity
            $post_id = wl_create_post( '', 'post1', 'A post');
            $entity_id = wl_create_post( '', 'entity1', 'An Entity', 'draft', 'entity' );
            
            // Stress method with strange parmeters
            $result = wl_core_add_relation_instance( '', WL_WHAT_RELATION, $entity_id );
            $this->assertFalse( $result );
            $result = wl_core_add_relation_instance( $post_id, WL_WHAT_RELATION, null );
            $this->assertFalse( $result );
            $result = wl_core_add_relation_instance( $post_id, 'ulabadula', $entity_id );
            $this->assertFalse( $result );
            
            // Nothing has been inserted as relation so far.
            $result = wl_core_get_related_entity_ids( $post_id );
            $this->assertTrue( is_array( $result ) );
            $this->assertEmpty( $result );
            
            // Insert relation and verify it
            $result = wl_core_add_relation_instance( $post_id, WL_WHAT_RELATION, $entity_id );
            $this->assertTrue( is_numeric( $result ) ); // The methods return a record id
            $result = wl_core_get_related_entity_ids( $post_id );
            $this->assertEquals( array( $entity_id ), $result );
        }
        
        function testAddRelationInstances() {
            
            // Create a post and 2 entities
            $post_1_id = wl_create_post( '', 'post1', 'A post');
            $entity_1_id = wl_create_post( '', 'entity1', 'An Entity', 'draft', 'entity' );
            $entity_2_id = wl_create_post( '', 'entity2', 'An Entity', 'draft', 'entity' );
            
            // Stress method with strange parmeters
            $result = wl_core_add_relation_instances( '', WL_WHAT_RELATION, array( $entity_1_id, $entity_2_id ) );
            $this->assertFalse( $result );
            $result = wl_core_add_relation_instances( $post_1_id, WL_WHAT_RELATION, null );
            $this->assertFalse( $result );
            $result = wl_core_add_relation_instances( $post_1_id, WL_WHAT_RELATION, array() );
            $this->assertFalse( $result );
            $result = wl_core_add_relation_instances( $post_1_id, 'ulabadula',  array( $entity_1_id, $entity_2_id )  );
            $this->assertFalse( $result );
            $result = wl_core_add_relation_instances( $post_1_id, 'ulabadula', array() );
            $this->assertFalse( $result );
            
            // Nothing has been inserted as relation so far.
            $result = wl_core_get_related_entity_ids( $post_1_id );
            $this->assertTrue( is_array( $result ) );
            $this->assertEmpty( $result );
            
            // Insert relation and verify it
            $result = wl_core_add_relation_instances( $post_1_id, WL_WHAT_RELATION, array( $entity_1_id, $entity_2_id ) );
            $this->assertTrue( is_numeric( $result[0] ) ); // The methods return an array of record ids
            $this->assertTrue( is_numeric( $result[1] ) ); // The methods return an array of record ids
            $this->assertCount( 2, $result );
            $result = wl_core_get_related_entity_ids( $post_1_id );
            $this->assertEquals( array( $entity_1_id, $entity_2_id ), $result );
        }
        
        function testGetRelatedEntitiesIds() {
            
            // Create a post and 2 entities
            $post_1_id = wl_create_post( '', 'post1', 'A post');
            $entity_1_id = wl_create_post( '', 'entity1', 'An Entity', 'draft', 'entity' );
            $entity_2_id = wl_create_post( '', 'entity2', 'An Entity', 'draft', 'entity' );
            
            // Stress method with strange parmeters
            $result = wl_core_get_related_entity_ids( '' );
            $this->assertEmpty( $result );
            $result = wl_core_get_related_entity_ids( null );
            $this->assertEmpty( $result );
            $result = wl_core_get_related_entity_ids( $post_1_id, 'ulabadula' );
            $this->assertEmpty( $result );
            
            // Nothing has been inserted as relation so far.
            $result = wl_core_get_related_entity_ids( $post_1_id );
            $this->assertTrue( is_array( $result ) );
            $this->assertEmpty( $result );
            
            // Insert relations
            $result = wl_core_add_relation_instances( $post_1_id, WL_WHAT_RELATION, array( $entity_1_id, $entity_2_id ) );
            $result = wl_core_add_relation_instance( $post_1_id, WL_WHERE_RELATION, $entity_1_id );
            $result = wl_core_add_relation_instance( $post_1_id, WL_WHO_RELATION, $entity_2_id );
            
            
            // TODO:
            $result = wl_core_get_related_entity_ids( $post_1_id );
            //$this->assertEquals( array( $entity_1_id, $entity_2_id ), $result );
            
            $result = wl_core_get_related_entity_ids( $post_1_id, WL_WHERE_RELATION );
            //$this->assertEquals( array( $entity_1_id ), $result );
            
            $result = wl_core_get_related_entity_ids( $post_1_id, WL_WHO_RELATION );
            //$this->assertEquals( array( $entity_2_id ), $result );
        }

    
////////////////////////////////////////////////////////
//////////////////// OLD TESTS /////////////////////////
////////////////////////////////////////////////////////        
        
//	/**
//	 * Test *related* methods.
//	 */
//	function testRelatedAndReferencing() {
//
//		$post_id        = wl_create_post( '', 'post-1', 'Post 1' );
//		$entity_post_id = wl_create_post( '', 'entity-1', 'Entity 1', 'draft', 'entity' );
//
//		$related_entities = wl_get_referenced_entities( $post_id );
//		$this->assertEquals( 0, count( $related_entities ) );
//
//		$related_posts = wl_get_related_entities( $entity_post_id );
//		$this->assertEquals( 0, count( $related_posts ) );
//                
//                // reference is a directed relation: A --> B
//		wl_add_referenced_entities( $post_id, $entity_post_id );
//		$this->assertEquals( 1, count( wl_get_referenced_entities( $post_id ) ) );
//                $this->assertEquals( 1, count( wl_get_referencing_posts( $entity_post_id ) ) );
//                $results = wl_get_referenced_entities( $post_id );
//                $this->assertEquals( $entity_post_id, $results[0] );
//                $results = wl_get_referencing_posts( $entity_post_id );
//                $this->assertEquals( $post_id, $results[0] );
//
//                // related is a simmetric relation: A <--> B
//		wl_add_related_entities( $entity_post_id, $post_id );
//		$this->assertEquals( 1, count( wl_get_related_entities( $entity_post_id ) ) );
//                $this->assertEquals( 1, count( wl_get_related_entities( $post_id ) ) );
//                $results = wl_get_related_entities( $post_id );
//                $this->assertEquals( $entity_post_id, $results[0] );
//                $results = wl_get_related_entities( $entity_post_id );
//                $this->assertEquals( $post_id, $results[0] );
//	}
//
//	/**
//	 * Test the wl_get_referencing_posts method.
//	 */
//	function testReferencingInDetail() {
//
//		// Create a couple of sample posts and entities.
//		$post_1   = wl_create_post( '', 'post-1', 'Post 1' );
//		$post_2   = wl_create_post( '', 'post-2', 'Post 2' );
//		$entity_1 = wl_create_post( '', 'entity-1', 'Entity 1', 'draft', 'entity' );
//		$entity_2 = wl_create_post( '', 'entity-2', 'Entity 2', 'draft', 'entity' );
//
//		// Reference entity 1 and 2 from post 1.
//		wl_add_referenced_entities( $post_1, array( $entity_1, $entity_2 ) );
//
//		// Reference entity 1 from post 2.
//		wl_add_referenced_entities( $post_2, $entity_1 );
//                
//                // Check that references are returned correctly.
//                $this->assertEquals( array( $entity_1, $entity_2 ), wl_get_referenced_entities( $post_1 ) );
//                $this->assertEquals( array( $entity_1 ), wl_get_referenced_entities( $post_2 ) );
//
//                // Check the complementary relations ( 'is referenced by' )
//		$posts_referencing_entity_1 = wl_get_referencing_posts( $entity_1 );
//		$this->assertTrue( in_array( $post_1, $posts_referencing_entity_1 ) );
//		$this->assertTrue( in_array( $post_2, $posts_referencing_entity_1 ) );
//
//                // Check the complementary relations ( 'is referenced by' )
//		$posts_referencing_entity_2 = wl_get_referencing_posts( $entity_2 );
//		$this->assertCount( 1, $posts_referencing_entity_2 );
//		$this->assertEquals( $post_1, $posts_referencing_entity_2[0] );
//                
//	}
//        
//        function testPost4W() {
//            
//            // Create a post.
//            $post_id = wl_create_post( '', 'post', 'post', 'draft', 'post' );
//
//            // Create the 4W entities.
//            // WHAT
//            $what_id = wl_create_post( '', 'what', 'Entity What', 'draft', 'entity' );
//            
//            // WHO
//            $who_id = wl_create_post( '', 'who', 'Entity Who', 'draft', 'entity' );
//            
//            // WHEN
//            $when_id = wl_create_post( '', 'when', 'Entity When', 'draft', 'entity' );
//            
//            // WHERE
//            $where_id = wl_create_post( '', 'where', 'Entity Where', 'draft', 'entity' );
//                        
//            // Bind the 4W to the post.
//            wl_add_referenced_entities( $post_id, $what_id, WL_CUSTOM_FIELD_WHAT_ENTITIES );
//            wl_add_referenced_entities( $post_id, $when_id, WL_CUSTOM_FIELD_WHAT_ENTITIES );  // another entity on the same W
//            wl_add_referenced_entities( $post_id, array( $who_id, $what_id), WL_CUSTOM_FIELD_WHO_ENTITIES );    // assign more than one at the same time
//            wl_add_referenced_entities( $post_id, $where_id, WL_CUSTOM_FIELD_WHERE_ENTITIES );
//            // Note: no entities added as WHEN
//
//            // Check associations.
//            
//            // The 4W in an associative array, handcoded
//            $w4_by_hand = array(
//                WL_CUSTOM_FIELD_WHAT_ENTITIES => array( $what_id, $when_id ),
//                WL_CUSTOM_FIELD_WHERE_ENTITIES => array( $where_id ),
//                WL_CUSTOM_FIELD_WHEN_ENTITIES => array(),
//                WL_CUSTOM_FIELD_WHO_ENTITIES => array( $who_id, $what_id )
//            );
//            
//            // Check associations for the single W
//            $this->assertEquals( $w4_by_hand, array(
//                WL_CUSTOM_FIELD_WHAT_ENTITIES => wl_get_referenced_entities( $post_id, WL_CUSTOM_FIELD_WHAT_ENTITIES ),
//                WL_CUSTOM_FIELD_WHERE_ENTITIES => wl_get_referenced_entities( $post_id, WL_CUSTOM_FIELD_WHERE_ENTITIES ),
//                WL_CUSTOM_FIELD_WHEN_ENTITIES => wl_get_referenced_entities( $post_id, WL_CUSTOM_FIELD_WHEN_ENTITIES ),
//                WL_CUSTOM_FIELD_WHO_ENTITIES => wl_get_referenced_entities( $post_id, WL_CUSTOM_FIELD_WHO_ENTITIES )
//            ));
//            
//            // Test WL-generated 4W's associative array
//            $w4 = wl_get_post_4w_entities( 279469238 );  // non existent post
//            $this->assertEquals( $w4, array() );
//            
//            $w4 = wl_get_post_4w_entities( $post_id );   // real post
//            $this->assertEquals( $w4_by_hand, $w4 );
//            
//            $this->assertEquals( array( $what_id, $when_id ), $w4[WL_CUSTOM_FIELD_WHAT_ENTITIES] );
//            $this->assertEquals( array( $who_id, $what_id ), $w4[WL_CUSTOM_FIELD_WHO_ENTITIES] );
//            $this->assertEquals( array( $where_id ), $w4[WL_CUSTOM_FIELD_WHERE_ENTITIES] );
//            $this->assertEquals( array(), $w4[WL_CUSTOM_FIELD_WHEN_ENTITIES] );
//            
//            
//            
//            // Check complmentary associations
//            
//            // Test WL-generated WHAT associative array
//            $w4_is_for = wl_get_entity_is_4w_for_posts( 279469238 );  // non existent entity
//            $this->assertEquals( array(), $w4_is_for );
//            
//            // Check complmentary associations for each of the 4Ws
//            foreach( array( $what_id, $when_id, $where_id, $who_id ) as $entity_id ) {
//                // The posts referenced by the WHAT in an associative array, handcoded
//                $w4_is_for_by_hand = array(
//                    WL_CUSTOM_FIELD_IS_WHAT_FOR_POSTS => wl_get_referencing_posts( $entity_id, WL_CUSTOM_FIELD_IS_WHAT_FOR_POSTS),
//                    WL_CUSTOM_FIELD_IS_WHERE_FOR_POSTS => wl_get_referencing_posts( $entity_id, WL_CUSTOM_FIELD_IS_WHERE_FOR_POSTS),
//                    WL_CUSTOM_FIELD_IS_WHEN_FOR_POSTS => wl_get_referencing_posts( $entity_id, WL_CUSTOM_FIELD_IS_WHEN_FOR_POSTS),
//                    WL_CUSTOM_FIELD_IS_WHO_FOR_POSTS => wl_get_referencing_posts( $entity_id, WL_CUSTOM_FIELD_IS_WHO_FOR_POSTS)
//                );
//
//                $w4_is_for = wl_get_entity_is_4w_for_posts( $entity_id );   // real post
//                $this->assertEquals( $w4_is_for_by_hand, $w4_is_for );
//            }
//            
//            // Specific complementary associations
//            $this->assertEquals( array( $post_id ), wl_get_referencing_posts( $what_id, WL_CUSTOM_FIELD_IS_WHAT_FOR_POSTS) );
//            $this->assertEquals( array( $post_id ), wl_get_referencing_posts( $where_id, WL_CUSTOM_FIELD_IS_WHERE_FOR_POSTS) );
//            $this->assertEquals( array( $post_id ), wl_get_referencing_posts( $who_id, WL_CUSTOM_FIELD_IS_WHO_FOR_POSTS) );
//            $this->assertEquals( array(), wl_get_referencing_posts( $when_id, WL_CUSTOM_FIELD_IS_WHEN_FOR_POSTS) );
//
//            
//    }
}