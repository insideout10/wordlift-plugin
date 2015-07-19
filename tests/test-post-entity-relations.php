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

        function testWlCoreGetPosts() {

            // Prepare interaction with db
            global $wpdb;

            $wl_table_name = wl_core_get_relation_instances_table_name();

            // Case 1 - :related_to missing
            $args = array();
            $result = wl_core_get_posts( $args );
            $this->assertFalse( $result );

            // Case 2a - :related_to not numeric
            $args = array(
                'get' => 'posts',  
                'related_to' => 'not-a-numeric-value',
                'as' => 'subject',
                'post_type' => 'post', 
                );
            $result = wl_core_get_posts( $args );
            $this->assertFalse( $result );

            // Case 2b - :related_to string representing a number
            $args = array(
                'get' => 'post_ids',  
                'related_to' => '23',
                'as' => 'subject',
                'post_type' => 'post', 
                );
            $result = wl_core_get_posts( $args );
            $this->assertInternalType( 'array', $result );

            // Case 3 - invalid :get 
            $args = array(
                'get' => 'pippo',  
                'related_to' => 3,
                'as' => 'subject',
                'post_type' => 'post', 
                );
            $result = wl_core_get_posts( $args );
            $this->assertFalse( $result );

            // Case 4 - invalid :as 
            $args = array(
                'get' => 'posts',  
                'related_to' => 3,
                'as' => 'pippo',
                'post_type' => 'post', 
                );
            $result = wl_core_get_posts( $args );
            $this->assertFalse( $result );

            // Case 5 - invalid :post_type 
            $args = array(
                'get' => 'posts',  
                'related_to' => 3,
                'as' => 'subject',
                'post_type' => 'pippo', 
                );
            $result = wl_core_get_posts( $args );
            $this->assertFalse( $result );

            // Case 6 - invalid :with_predicate 
            $args = array(
                'get' => 'posts',  
                'related_to' => 3,
                'as' => 'subject',
                'post_type' => 'post',
                'with_predicate' => 'pippo' 
                );

            $result = wl_core_get_posts( $args );
            $this->assertFalse( $result );

        }

        function testWlCoreSqlQueryBuilder() {

            // Prepare interaction with db
            global $wpdb;

            $wl_table_name = wl_core_get_relation_instances_table_name();

            // Case 6 - Find all posts of type 'post' related to post / entity with ID 3 as subject
            $args = array(
                'get' => 'posts',  
                'related_to' => 3,
                'as' => 'subject',
                'post_type' => 'post',   
                );
            $expected_sql = <<<EOF
SELECT p.* FROM $wpdb->posts as p JOIN $wl_table_name as r ON p.id = r.subject_id AND p.post_type = 'post' AND r.object_id = 3 GROUP BY p.id;
EOF;
            $actual_sql = wl_core_sql_query_builder( $args );
            $this->assertEquals( $expected_sql, $actual_sql );
            // Try to perform query in order to see if there are errors on db side
            $wpdb->get_results( $actual_sql );
            $this->assertEmpty( $wpdb->last_error );

            // Case 7 - Find all post ids of type 'post' related to post / entity with ID 3 as subject
            $args = array(
                'get' => 'post_ids',  
                'related_to' => 3,
                'as' => 'subject',
                'post_type' => 'post',   
                );
            $expected_sql = <<<EOF
SELECT p.id FROM $wpdb->posts as p JOIN $wl_table_name as r ON p.id = r.subject_id AND p.post_type = 'post' AND r.object_id = 3 GROUP BY p.id;
EOF;
            $actual_sql = wl_core_sql_query_builder( $args );
            $this->assertEquals( $expected_sql, $actual_sql );
            // Try to perform query in order to see if there are errors on db side
            $wpdb->get_results( $actual_sql );
            $this->assertEmpty( $wpdb->last_error );

            // Case 8 - Find all relations of type 'post' related to post / entity with ID 3 as subject
            $args = array(
                'get' => 'relations',  
                'related_to' => 3,
                'as' => 'subject',
                'post_type' => 'post',   
                );
            $expected_sql = <<<EOF
SELECT r.* FROM $wl_table_name as r WHERE r.subject_id = 3;
EOF;
            $actual_sql = wl_core_sql_query_builder( $args );
            $this->assertEquals( $expected_sql, $actual_sql );
            // Try to perform query in order to see if there are errors on db side
            $wpdb->get_results( $actual_sql );
            $this->assertEmpty( $wpdb->last_error );

            // Case 9 - Find all relation ids of type 'post' related to post / entity with ID 3 as subject
            $args = array(
                'get' => 'relation_ids',  
                'related_to' => 3,
                'as' => 'subject',
                'post_type' => 'post',   
                );
            $expected_sql = <<<EOF
SELECT r.id FROM $wl_table_name as r WHERE r.subject_id = 3;
EOF;
            $actual_sql = wl_core_sql_query_builder( $args );
            $this->assertEquals( $expected_sql, $actual_sql );
            // Try to perform query in order to see if there are errors on db side
            $wpdb->get_results( $actual_sql );
            $this->assertEmpty( $wpdb->last_error );

            // Case 10 - Find first ten post ids of type 'post' related to post / entity with ID 3 as subject
            $args = array(
                'first' => 10,
                'get' => 'posts',  
                'related_to' => 3,
                'as' => 'subject',
                'post_type' => 'post',   
                );
            $expected_sql = <<<EOF
SELECT p.* FROM $wpdb->posts as p JOIN $wl_table_name as r ON p.id = r.subject_id AND p.post_type = 'post' AND r.object_id = 3 GROUP BY p.id LIMIT 10;
EOF;
            $actual_sql = wl_core_sql_query_builder( $args );
            $this->assertEquals( $expected_sql, $actual_sql );
            // Try to perform query in order to see if there are errors on db side
            $wpdb->get_results( $actual_sql );
            $this->assertEmpty( $wpdb->last_error );

            // Case 11 - Find first ten post ids of type 'post' related to post / entity with ID 3 as object
            $args = array(
                'first' => 10,
                'get' => 'posts',  
                'related_to' => 3,
                'as' => 'object',
                'post_type' => 'post',   
                );
            $expected_sql = <<<EOF
SELECT p.* FROM $wpdb->posts as p JOIN $wl_table_name as r ON p.id = r.object_id AND p.post_type = 'post' AND r.subject_id = 3 GROUP BY p.id LIMIT 10;
EOF;
            $actual_sql = wl_core_sql_query_builder( $args );
            $this->assertEquals( $expected_sql, $actual_sql );
            // Try to perform query in order to see if there are errors on db side
            $wpdb->get_results( $actual_sql );
            $this->assertEmpty( $wpdb->last_error );

            // Case 12 - Find first ten post ids of type 'post' related to post / entity with ID 3 as object with predicate what
            $args = array(
                'first' => 10,
                'get' => 'posts',  
                'related_to' => 3,
                'as' => 'object',
                'post_type' => 'post', 
                'with_predicate' => 'what'  
                );
            $expected_sql = <<<EOF
SELECT p.* FROM $wpdb->posts as p JOIN $wl_table_name as r ON p.id = r.object_id AND p.post_type = 'post' AND r.subject_id = 3 AND r.predicate = 'what' GROUP BY p.id LIMIT 10;
EOF;
            $actual_sql = wl_core_sql_query_builder( $args );
            $this->assertEquals( $expected_sql, $actual_sql );
            // Try to perform query in order to see if there are errors on db side
            $wpdb->get_results( $actual_sql );
            $this->assertEmpty( $wpdb->last_error );
        }

        function testWlCoreAddRelationInstance() {
            
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
        
        function testWlCoreAddRelationInstances() {
            
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

        function testWlCoreDeleteRelationInstance(){

            // Create a post and an entity
            $post_id = wl_create_post( '', 'post1', 'A post');
            $entity_id = wl_create_post( '', 'entity1', 'An Entity', 'draft', 'entity' );
            
            // No relations at this point
            $result = wl_core_get_relation_instances_for( $post_id );
            $this->assertCount( 0, $result );
            // Insert relation and verify it
            $result = wl_core_add_relation_instance( $post_id, WL_WHAT_RELATION, $entity_id );
            $this->assertTrue( is_numeric( $result ) ); // The methods return a record id
            $result = wl_core_get_relation_instances_for( $post_id );
            $this->assertCount( 1, $result );

            $result = wl_core_delete_relation_instance( $post_id, WL_WHAT_RELATION, $entity_id );
            $this->assertTrue( $result );
            $result = wl_core_get_relation_instances_for( $post_id );
            $this->assertCount( 0, $result );

        }

        function testWlCoreDeleteRelationInstances(){

            // Create a post and an entity
            $post_id = wl_create_post( '', 'post1', 'A post');
            $entity_id = wl_create_post( '', 'entity1', 'An Entity', 'draft', 'entity' );
            
            // No relations at this point
            $result = wl_core_get_relation_instances_for( $post_id );
            $this->assertCount( 0, $result );
            // Insert relation and verify it
            $result = wl_core_add_relation_instance( $post_id, WL_WHAT_RELATION, $entity_id );
            $this->assertTrue( is_numeric( $result ) ); // The methods return a record id
            $result = wl_core_add_relation_instance( $post_id, WL_WHO_RELATION, $entity_id );
            $this->assertTrue( is_numeric( $result ) ); // The methods return a record id

            $result = wl_core_get_relation_instances_for( $post_id );
            $this->assertCount( 2, $result );

            $result = wl_core_delete_relation_instances( $post_id );
            $this->assertTrue( $result );
            $result = wl_core_get_relation_instances_for( $post_id );
            $this->assertCount( 0, $result );

        }

        function testWlCoreGetRelationInstancesFor() {
            // Create a post and 2 entities
            $post_1_id = wl_create_post( '', 'post1', 'A post');
            $entity_1_id = wl_create_post( '', 'entity1', 'An Entity', 'draft', 'entity' );
            $entity_2_id = wl_create_post( '', 'entity2', 'An Entity', 'draft', 'entity' );

            // Insert relations
            wl_core_add_relation_instances( $post_1_id, WL_WHAT_RELATION, array( $entity_1_id, $entity_2_id ) );
            wl_core_add_relation_instance( $post_1_id, WL_WHERE_RELATION, $entity_1_id );
            wl_core_add_relation_instance( $post_1_id, WL_WHO_RELATION, $entity_2_id );
            
            // Check relation are retrieved as expected
            $result = wl_core_get_relation_instances_for( $post_1_id );
            $this->assertCount( 4, $result );

            $result = wl_core_get_relation_instances_for( $post_1_id, WL_WHAT_RELATION );
            $this->assertCount( 2, $result );
            $result = wl_core_get_relation_instances_for( $post_1_id, WL_WHERE_RELATION );
            $this->assertCount( 1, $result );
            $result = wl_core_get_relation_instances_for( $post_1_id, WL_WHO_RELATION );
            $this->assertCount( 1, $result );
            $result = wl_core_get_relation_instances_for( $post_1_id, WL_WHEN_RELATION );
            $this->assertCount( 0, $result );
        }

        function testWlCoreGetRelatedPostIds() {
            
            // Create 2 posts and 1 entities
            $post_1_id = wl_create_post( '', 'post1', 'A post');
            $post_2_id = wl_create_post( '', 'post2', 'A post');            
            $entity_1_id = wl_create_post( '', 'entity1', 'An Entity', 'draft', 'entity' );
            
            // Insert relations
            wl_core_add_relation_instance( $post_1_id, WL_WHERE_RELATION, $entity_1_id );
            wl_core_add_relation_instance( $post_2_id, WL_WHO_RELATION, $entity_1_id );
            
            // Check relation are retrieved as expected
            $result = wl_core_get_related_post_ids( $entity_1_id );
            $this->assertCount( 2, $result );
            $this->assertTrue( in_array( $post_1_id, $result ) );
            $this->assertTrue( in_array( $post_2_id, $result ) );
            
            $result = wl_core_get_related_post_ids( $entity_1_id, WL_WHERE_RELATION );
            $this->assertCount( 1, $result );
            $this->assertTrue( in_array( $post_1_id, $result ) );

            $result = wl_core_get_related_post_ids( $entity_1_id, WL_WHO_RELATION );
            $this->assertCount( 1, $result );
            $this->assertTrue( in_array( $post_2_id, $result ) );

            $result = wl_core_get_related_post_ids( $entity_1_id, WL_WHAT_RELATION );
            $this->assertCount( 0, $result );

        }

}