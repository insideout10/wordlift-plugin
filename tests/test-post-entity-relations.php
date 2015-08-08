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

            // Case 7 - invalid :related_to__in -> empty array
            $args = array(
                'get' => 'posts',  
                'related_to' => 6,
                'related_to__in' => array(),
                'as' => 'subject',
                'post_type' => 'post',
                'with_predicate' => 'what' 
                );

            $result = wl_core_get_posts( $args );
            $this->assertFalse( $result );

            // Case 8 - invalid :related_to__in 
            $args = array(
                'get' => 'posts',  
                'related_to' => 5,
                'related_to__in' => array('not-numeric-value'),
                'as' => 'subject',
                'post_type' => 'post',
                'with_predicate' => 'what' 
                );

            $result = wl_core_get_posts( $args );
            $this->assertFalse( $result );   

            // Case 9 - invalid :related_to__in 
            $args = array(
                'get' => 'posts',  
                'related_to' => 4,
                'related_to__in' => array('not-numeric-value','13'),
                'as' => 'subject',
                'post_type' => 'post',
                'with_predicate' => 'what' 
                );

            $result = wl_core_get_posts( $args );
            $this->assertInternalType( "array", $result ); 

            // Case 10 - missing both :related_to and :related_to__in 
            $args = array(
                'get' => 'posts',  
                'as' => 'subject',
                'post_type' => 'post',
                'with_predicate' => 'what' 
                );

            $result = wl_core_get_posts( $args );
            $this->assertFalse( $result );               

            // Case 11 - just :related_to is set: it should be valid 
            $args = array(
                'get' => 'posts',  
                'as' => 'subject',
                'post_type' => 'post',
                'with_predicate' => 'what', 
                'related_to' => 4,
                );

            $result = wl_core_get_posts( $args );
            $this->assertInternalType( "array", $result ); 

            // Case 12 - just :related_to__in is set: it should be valid 
            $args = array(
                'get' => 'posts',  
                'as' => 'subject',
                'post_type' => 'post',
                'with_predicate' => 'what', 
                'related_to__in' => array(1,2),
                );

            $result = wl_core_get_posts( $args );
            $this->assertInternalType( "array", $result ); 
            
            // Case 13 - Ask a valid post status 
            $args = array(
                'get' => 'posts',  
                'as' => 'subject',
                'post_type' => 'post',
                'post_status' => 'draft',
                'related_to' => 4,
                );
            
            $result = wl_core_get_posts( $args );
            
            $this->assertInternalType( "array", $result ); 
            
            // Case 14 - Ask an invalid post status 
            $args = array(
                'get' => 'posts',  
                'as' => 'subject',
                'post_type' => 'post',
                'post_status' => 'pippo',
                'related_to' => 4,
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

            // Case 8 - Find first ten post ids of type 'post' related to post / entity with ID 3 as subject
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

            // Case 9 - Find first ten post ids of type 'post' related to post / entity with ID 3 as object
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

            // Case 10 - Find first ten post ids of type 'post' related to post / entity with ID 3 as object with predicate what
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

            // Case 11 - Find first ten post ids of type 'post' related to post / entity with ID 3 and IN (4,5) as object with predicate what
            $args = array(
                'first' => 10,
                'get' => 'posts',  
                'related_to' => 3,
                'related_to__in' => array('4','5'),
                'post_type' => 'post', 
                'as' => 'object',
                );
            $expected_sql = <<<EOF
SELECT p.* FROM $wpdb->posts as p JOIN $wl_table_name as r ON p.id = r.object_id AND p.post_type = 'post' AND r.subject_id = 3 AND r.subject_id IN (4,5) GROUP BY p.id LIMIT 10;
EOF;
            $actual_sql = wl_core_sql_query_builder( $args );
            $this->assertEquals( $expected_sql, $actual_sql );
            // Try to perform query in order to see if there are errors on db side
            $wpdb->get_results( $actual_sql );
            $this->assertEmpty( $wpdb->last_error );

            // Case 12 - Find first ten post ids of type 'post' related to post / entity id IN (3, 4) as object with predicate what
            $args = array(
                'first' => 10,
                'get' => 'posts',  
                'related_to__in' => array('4','5'),
                'post_type' => 'post', 
                'as' => 'object',
                );
            $expected_sql = <<<EOF
SELECT p.* FROM $wpdb->posts as p JOIN $wl_table_name as r ON p.id = r.object_id AND p.post_type = 'post' AND r.subject_id IN (4,5) GROUP BY p.id LIMIT 10;
EOF;
            $actual_sql = wl_core_sql_query_builder( $args );
            $this->assertEquals( $expected_sql, $actual_sql );
            // Try to perform query in order to see if there are errors on db side
            $wpdb->get_results( $actual_sql );
            $this->assertEmpty( $wpdb->last_error );

            // Case 13 - Find post ids of type 'post' not included IN (6) related to post / entity id IN (3, 4) as object 
            $args = array(
                'get' => 'posts',  
                'related_to__in' => array('4','5'),
                'post__not_in' => array('6'),
                'post_type' => 'post', 
                'as' => 'object',
                );
            $expected_sql = <<<EOF
SELECT p.* FROM $wpdb->posts as p JOIN $wl_table_name as r ON p.id = r.object_id AND p.post_type = 'post' AND r.subject_id IN (4,5) AND r.object_id NOT IN (6) GROUP BY p.id;
EOF;
            $actual_sql = wl_core_sql_query_builder( $args );
            $this->assertEquals( $expected_sql, $actual_sql );
            // Try to perform query in order to see if there are errors on db side
            $wpdb->get_results( $actual_sql );
            $this->assertEmpty( $wpdb->last_error );
            
            // Case 14 - Require a specific post status
            $args = array(
                'get' => 'posts',  
                'related_to' => 4,
                'post_type' => 'post', 
                'post_status' => 'draft',
                'as' => 'object',
                );
            $expected_sql = <<<EOF
SELECT p.* FROM $wpdb->posts as p JOIN $wl_table_name as r ON p.id = r.object_id AND p.post_type = 'post' AND p.post_status = 'draft' AND r.subject_id = 4 GROUP BY p.id;
EOF;
            $actual_sql = wl_core_sql_query_builder( $args );
            $this->assertEquals( $expected_sql, $actual_sql );
            // Try to perform query in order to see if there are errors on db side
            $wpdb->get_results( $actual_sql );
            $this->assertEmpty( $wpdb->last_error );
            
            // Case 15 - Do not require an post status
            $args = array(
                'get' => 'posts',  
                'related_to' => 4,
                'post_type' => 'post', 
                'post_status' => null,
                'as' => 'object',
                );
            $expected_sql = <<<EOF
SELECT p.* FROM $wpdb->posts as p JOIN $wl_table_name as r ON p.id = r.object_id AND p.post_type = 'post' AND r.subject_id = 4 GROUP BY p.id;
EOF;
            $actual_sql = wl_core_sql_query_builder( $args );
            $this->assertEquals( $expected_sql, $actual_sql );
            // Try to perform query in order to see if there are errors on db side
            $wpdb->get_results( $actual_sql );
            $this->assertEmpty( $wpdb->last_error );

            // Case 16 - Find post ids of type 'post' only if included IN (6) and related to post / entity id IN (3, 4) as object 
            $args = array(
                'get' => 'posts',  
                'related_to__in' => array('4','5'),
                'post__in' => array('6'),
                'post_type' => 'post', 
                'as' => 'object',
                );
            $expected_sql = <<<EOF
SELECT p.* FROM $wpdb->posts as p JOIN $wl_table_name as r ON p.id = r.object_id AND p.post_type = 'post' AND r.subject_id IN (4,5) AND r.object_id IN (6) GROUP BY p.id;
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
            $result = $this->wl_core_get_relation_instances_for( $post_id );
            $this->assertCount( 0, $result );
            // Insert relation and verify it
            $result = wl_core_add_relation_instance( $post_id, WL_WHAT_RELATION, $entity_id );
            $this->assertTrue( is_numeric( $result ) ); // The methods return a record id
            $result = $this->wl_core_get_relation_instances_for( $post_id );
            $this->assertCount( 1, $result );

            $result = wl_core_delete_relation_instance( $post_id, WL_WHAT_RELATION, $entity_id );
            $this->assertTrue( $result );
            $result = $this->wl_core_get_relation_instances_for( $post_id );
            $this->assertCount( 0, $result );

        }

        function testWlCoreDeleteRelationInstances(){

            // Create a post and an entity
            $post_id = wl_create_post( '', 'post1', 'A post');
            $entity_id = wl_create_post( '', 'entity1', 'An Entity', 'draft', 'entity' );
            
            // No relations at this point
            $result = $this->wl_core_get_relation_instances_for( $post_id );
            $this->assertCount( 0, $result );
            // Insert relation and verify it
            $result = wl_core_add_relation_instance( $post_id, WL_WHAT_RELATION, $entity_id );
            $this->assertTrue( is_numeric( $result ) ); // The methods return a record id
            $result = wl_core_add_relation_instance( $post_id, WL_WHO_RELATION, $entity_id );
            $this->assertTrue( is_numeric( $result ) ); // The methods return a record id

            $result = $this->wl_core_get_relation_instances_for( $post_id );
            $this->assertCount( 2, $result );

            $result = wl_core_delete_relation_instances( $post_id );
            $this->assertTrue( $result );
            $result = $this->wl_core_get_relation_instances_for( $post_id );
            $this->assertCount( 0, $result );

        }

        function testWlCoreGetRelatedPostIdsForAnEntity() {
            
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
            
            $result = wl_core_get_related_post_ids( $entity_1_id, array(
                'predicate' => WL_WHERE_RELATION
            ) );
            $this->assertCount( 1, $result );
            $this->assertTrue( in_array( $post_1_id, $result ) );

            $result = wl_core_get_related_post_ids( $entity_1_id, array(
                'predicate' => WL_WHO_RELATION
            ) );
            $this->assertCount( 1, $result );
            $this->assertTrue( in_array( $post_2_id, $result ) );

            $result = wl_core_get_related_post_ids( $entity_1_id, array(
                'predicate' => WL_WHAT_RELATION
            ) );
            $this->assertCount( 0, $result );

        }

        function testWlCoreGetRelatedPostIdsForAPost() {
            
            // Create 2 posts and 1 entities
            $post_1_id = wl_create_post( '', 'post1', 'A post');
            $post_2_id = wl_create_post( '', 'post2', 'A post');            
            $entity_1_id = wl_create_post( '', 'entity1', 'An Entity', 'draft', 'entity' );
            
            // Insert relations
            wl_core_add_relation_instance( $post_1_id, WL_WHERE_RELATION, $entity_1_id );
            wl_core_add_relation_instance( $post_2_id, WL_WHO_RELATION, $entity_1_id );
            
            // Check relation are retrieved as expected
            $result = wl_core_get_related_post_ids( $post_1_id );
            $this->assertCount( 1, $result );
            $this->assertTrue( in_array( $post_2_id, $result ) );
            
            $result = wl_core_get_related_post_ids( $post_1_id, array(
                'predicate' => WL_WHERE_RELATION
            ) );
            $this->assertCount( 0, $result );
            
            $result = wl_core_get_related_post_ids( $post_1_id, array(
                'predicate' => WL_WHO_RELATION
            ) );
            $this->assertCount( 1, $result );
            $this->assertTrue( in_array( $post_2_id, $result ) );

        }

        function testWlCoreGetRelatedEntityIdsForAPost() {
            
            // Create 2 posts and 1 entities
            $post_1_id = wl_create_post( '', 'post1', 'A post');
            $entity_1_id = wl_create_post( '', 'entity1', 'An Entity', 'draft', 'entity' );
            $entity_2_id = wl_create_post( '', 'entity2', 'An Entity', 'draft', 'entity' );
            
            // Insert relations
            wl_core_add_relation_instance( $post_1_id, WL_WHERE_RELATION, $entity_1_id );
            wl_core_add_relation_instance( $post_1_id, WL_WHO_RELATION, $entity_2_id );
            
            // Check relation are retrieved as expected
            $result = wl_core_get_related_entity_ids( $post_1_id );
            $this->assertCount( 2, $result );
            $this->assertTrue( in_array( $entity_1_id, $result ) );
            $this->assertTrue( in_array( $entity_2_id, $result ) );
            
            $result = wl_core_get_related_entity_ids( $post_1_id, array(
                'predicate' => WL_WHERE_RELATION
            ) );
            $this->assertCount( 1, $result );
            $this->assertTrue( in_array( $entity_1_id, $result ) );
            
            $result = wl_core_get_related_entity_ids( $post_1_id, array(
                'predicate' => WL_WHO_RELATION
            ) );
            $this->assertCount( 1, $result );
            $this->assertTrue( in_array( $entity_2_id, $result ) );

        }

        function testWlCoreGetRelatedEntityIdsForAnEntity() {
            
            // Create 2 posts and 1 entities
            $entity_0_id = wl_create_post( '', 'entity0', 'An Entity', 'draft', 'entity' );
            $entity_1_id = wl_create_post( '', 'entity1', 'An Entity', 'draft', 'entity' );
            $entity_2_id = wl_create_post( '', 'entity2', 'An Entity', 'draft', 'entity' );
            
            // Insert relations
            wl_core_add_relation_instance( $entity_0_id, WL_WHERE_RELATION, $entity_1_id );
            wl_core_add_relation_instance( $entity_0_id, WL_WHO_RELATION, $entity_2_id );
            
            // Check relation are retrieved as expected
            $result = wl_core_get_related_entity_ids( $entity_0_id );
            $this->assertCount( 2, $result );
            $this->assertTrue( in_array( $entity_1_id, $result ) );
            $this->assertTrue( in_array( $entity_2_id, $result ) );
            
            $result = wl_core_get_related_entity_ids( $entity_0_id, array(
                'predicate' => WL_WHERE_RELATION
            ) );
            $this->assertCount( 1, $result );
            $this->assertTrue( in_array( $entity_1_id, $result ) );
            
            $result = wl_core_get_related_entity_ids( $entity_0_id, array(
                'predicate' => WL_WHO_RELATION
            ) );
            $this->assertCount( 1, $result );
            $this->assertTrue( in_array( $entity_2_id, $result ) );

        }
        
        /**
         * Get relations for a given $subject_id as an associative array.
         * 
         * @global type $wpdb
         * @param type $post_id
         * @param type $predicate
         * @return array in the following format:
         *              Array (
         *                  [0] => stdClass Object ( [id] => 140 [subject_id] => 17 [predicate] => what [object_id] => 47 ),
         *                  [1] => stdClass Object ( [id] => 141 [subject_id] => 17 [predicate] => what [object_id] => 14 ),
         *                  [2] => stdClass Object ( [id] => 142 [subject_id] => 17 [predicate] => where [object_id] => 16 ),
         *                  ...
         */
        function wl_core_get_relation_instances_for( $post_id, $predicate = null ) {

           // Prepare interaction with db
           global $wpdb;
           // Retrieve Wordlift relation instances table name
           $table_name = wl_core_get_relation_instances_table_name();
           // Sql Action
           $sql_statement = $wpdb->prepare( "SELECT * FROM $table_name WHERE subject_id = %d", $post_id );
           if ( null != $predicate ) {
               $sql_statement .= $wpdb->prepare( " AND predicate = %s", $predicate );     
           }
           $results = $wpdb->get_results( $sql_statement );
           return $results;

        }
}