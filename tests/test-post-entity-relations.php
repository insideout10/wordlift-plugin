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

		$post_id        = $this->createPost();
		$entity_post_id = $this->createPost();

		$related_entities = wl_get_referenced_entity_ids( $post_id );
		$this->assertEquals( 0, count( $related_entities ) );

		$related_posts = wl_get_related_post_ids( $entity_post_id );
		$this->assertEquals( 0, count( $related_posts ) );

		wl_add_referenced_entities( $post_id, array( $entity_post_id ) );
		$this->assertEquals( 1, count( wl_get_referenced_entity_ids( $post_id ) ) );

		wl_add_related_posts( $entity_post_id, array( $post_id ) );
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
		wl_add_referenced_entities( $post_2, array( $entity_1 ) );

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
}