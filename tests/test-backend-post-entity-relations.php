<?php

/**
 * Class PostEntityRelationsTest
 * @group backend
 */
class PostEntityRelationsTest extends Wordlift_Unit_Test_Case {

	function testFindByURI() {

		$entity_post_id = wl_create_post( '', 'test_entity', 'Test Entity', 'draft', 'entity' );
		$entity_uri     = wl_get_entity_uri( $entity_post_id );
		wl_schema_set_value( $entity_post_id, 'sameAs', 'http://example.org/entity/test_entity' );

		$same_as_array = wl_schema_get_value( $entity_post_id, 'sameAs' );
		$this->assertTrue( is_array( $same_as_array ) );
		$this->assertEquals( 'http://example.org/entity/test_entity', $same_as_array[0] );

		wl_schema_set_value( $entity_post_id, 'sameAs', array(
			'http://example.org/entity/test_entity',
			'http://data.example.org/entity/test_entity',
		) );

		$same_as_array = wl_schema_get_value( $entity_post_id, 'sameAs' );
		$this->assertTrue( is_array( $same_as_array ) );
		$this->assertEquals( 'http://example.org/entity/test_entity', $same_as_array[0] );
		$this->assertEquals( 'http://data.example.org/entity/test_entity', $same_as_array[1] );

		$post = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( 'http://example.org/entity/test_entity' );
		$this->assertNotNull( $post );

		$post = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( 'http://data.example.org/entity/test_entity' );
		$this->assertNotNull( $post );

		$same_as_uri = 'http://example.org/entity/test_entity2';

		$entity_post_id = wl_create_post( '', 'test_entity_2', 'Test Entity 2', 'draft', 'entity' );
		$entity_uri     = wl_get_entity_uri( $entity_post_id );
		wl_schema_set_value( $entity_post_id, 'sameAs', $same_as_uri );

		$same_as_array = wl_schema_get_value( $entity_post_id, 'sameAs' );
		$this->assertTrue( is_array( $same_as_array ) );
		$this->assertEquals( $same_as_uri, $same_as_array[0] );

		$post = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( 'http://example.org/entity/test_entity' );
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
		$args   = array();
		$result = wl_core_get_posts( $args );
		$this->assertFalse( $result );

		// Case 2a - :related_to not numeric
		$args   = array(
			'get'        => 'posts',
			'related_to' => 'not-a-numeric-value',
			'as'         => 'subject',
			'post_type'  => 'post',
		);
		$result = wl_core_get_posts( $args );
		$this->assertFalse( $result );

		// Case 2b - :related_to string representing a number
		$args   = array(
			'get'        => 'post_ids',
			'related_to' => '23',
			'as'         => 'subject',
			'post_type'  => 'post',
		);
		$result = wl_core_get_posts( $args );
		$this->assertInternalType( 'array', $result );

		// Case 3 - invalid :get
		$args   = array(
			'get'        => 'pippo',
			'related_to' => 3,
			'as'         => 'subject',
			'post_type'  => 'post',
		);
		$result = wl_core_get_posts( $args );
		$this->assertFalse( $result );

		// Case 4 - invalid :as
		$args   = array(
			'get'        => 'posts',
			'related_to' => 3,
			'as'         => 'pippo',
			'post_type'  => 'post',
		);
		$result = wl_core_get_posts( $args );
		$this->assertFalse( $result );

		// Case 5 - invalid :post_type
		$args   = array(
			'get'        => 'posts',
			'related_to' => 3,
			'as'         => 'subject',
			'post_type'  => 'pippo',
		);
		$result = wl_core_get_posts( $args );
		$this->assertFalse( $result );

		// Case 6 - invalid :with_predicate
		$args = array(
			'get'            => 'posts',
			'related_to'     => 3,
			'as'             => 'subject',
			'post_type'      => 'post',
			'with_predicate' => 'pippo',
		);

		$result = wl_core_get_posts( $args );
		$this->assertFalse( $result );

		// Case 7 - invalid :related_to__in -> empty array
		$args = array(
			'get'            => 'posts',
			'related_to'     => 6,
			'related_to__in' => array(),
			'as'             => 'subject',
			'post_type'      => 'post',
			'with_predicate' => 'what',
		);

		$result = wl_core_get_posts( $args );
		$this->assertFalse( $result );

		// Case 8 - invalid :related_to__in
		$args = array(
			'get'            => 'posts',
			'related_to'     => 5,
			'related_to__in' => array( 'not-numeric-value' ),
			'as'             => 'subject',
			'post_type'      => 'post',
			'with_predicate' => 'what',
		);

		$result = wl_core_get_posts( $args );
		$this->assertFalse( $result );

		// Case 9 - invalid :related_to__in
		$args = array(
			'get'            => 'posts',
			'related_to'     => 4,
			'related_to__in' => array( 'not-numeric-value', '13' ),
			'as'             => 'subject',
			'post_type'      => 'post',
			'with_predicate' => 'what',
		);

		$result = wl_core_get_posts( $args );
		$this->assertInternalType( "array", $result );

		// Case 10 - missing both :related_to and :related_to__in
		$args = array(
			'get'            => 'posts',
			'as'             => 'subject',
			'post_type'      => 'post',
			'with_predicate' => 'what',
		);

		$result = wl_core_get_posts( $args );
		$this->assertFalse( $result );

		// Case 11 - just :related_to is set: it should be valid
		$args = array(
			'get'            => 'posts',
			'as'             => 'subject',
			'post_type'      => 'post',
			'with_predicate' => 'what',
			'related_to'     => 4,
		);

		$result = wl_core_get_posts( $args );
		$this->assertInternalType( "array", $result );

		// Case 12 - just :related_to__in is set: it should be valid
		$args = array(
			'get'            => 'posts',
			'as'             => 'subject',
			'post_type'      => 'post',
			'with_predicate' => 'what',
			'related_to__in' => array( 1, 2 ),
		);

		$result = wl_core_get_posts( $args );
		$this->assertInternalType( "array", $result );

		// Case 13 - Ask a valid post status
		$args = array(
			'get'         => 'posts',
			'as'          => 'subject',
			'post_type'   => 'post',
			'post_status' => 'draft',
			'related_to'  => 4,
		);

		$result = wl_core_get_posts( $args );
		$this->assertInternalType( "array", $result );

		// Case 14 - Ask an invalid post status
		$args = array(
			'get'         => 'posts',
			'as'          => 'subject',
			'post_type'   => 'post',
			'post_status' => 'pippo',
			'related_to'  => 4,
		);

		$result = wl_core_get_posts( $args );
		$this->assertFalse( $result );
	}

	/** Enumerations for `create_posts`. */
	const POST_AS_ARTICLE_1 = 0;
	const POST_AS_ARTICLE_2 = 1;
	const POST_AS_EVENT = 2;
	const PAGE_AS_ARTICLE = 3;
	const PAGE_AS_RECIPE = 4;
	const ENTITY_AS_EVENT = 5;

	/**
	 * Create posts for tests.
	 *
	 * @since 3.15.0
	 *
	 * @return array An array of test {@link WP_Post}s' ids.
	 */
	private function create_posts( $times = 1 ) {
		// Create some tests cases:
		//  - post as non-Article entity,
		//  - post as Article entity,
		//  - page as non-Article entity,
		//  - page as Article entity,
		//  - entity.

		$posts = array();

		for ( $i = 0; $i < $times; $i ++ ) {
			$posts[] = $this->factory->post->create( array(
				'post_type'   => 'post',
				'post_status' => 'publish',
			) );
			$term_id = $this->get_term_id_by_slug( 'article' );
			wp_set_post_terms( $posts[ count( $posts ) - 1 ], $term_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

			$posts[] = $this->factory->post->create( array(
				'post_type'   => 'post',
				'post_status' => 'publish',
			) );
			$term_id = $this->get_term_id_by_slug( 'article' );
			wp_set_post_terms( $posts[ count( $posts ) - 1 ], $term_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

			$posts[] = $this->factory->post->create( array(
				'post_type'   => 'post',
				'post_status' => 'publish',
			) );
			$term_id = $this->get_term_id_by_slug( 'event' );
			$result  = wp_set_post_terms( $posts[ count( $posts ) - 1 ], $term_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
			$this->assertFalse( is_wp_error( $result ) );
			$this->assertNotFalse( $result );

			$posts[] = $this->factory->post->create( array(
				'post_type'   => 'page',
				'post_status' => 'publish',
			) );
			$term_id = $this->get_term_id_by_slug( 'article' );
			wp_set_post_terms( $posts[ count( $posts ) - 1 ], $term_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

			$posts[] = $this->factory->post->create( array(
				'post_type'   => 'page',
				'post_status' => 'publish',
			) );
			$term_id = $this->get_term_id_by_slug( 'recipe' );
			wp_set_post_terms( $posts[ count( $posts ) - 1 ], $term_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

			$posts[] = $this->factory->post->create( array(
				'post_type'   => 'entity',
				'post_status' => 'publish',
			) );
			$term_id = $this->get_term_id_by_slug( 'event' );
			wp_set_post_terms( $posts[ count( $posts ) - 1 ], $term_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
		}

		for ( $i = 0; $i < count( $posts ); $i ++ ) {
			for ( $j = 0; $j < count( $posts ); $j ++ ) {
				if ( $i === $j ) {
					continue;
				}
				$result = $this->add_relation( $posts[ $i ], $posts[ $j ] );
				$this->assertGreaterThan( 0, $result );
			}
		}

		return $posts;
	}

	function test_core_sql_query_builder_01() {
		global $wpdb;

		$posts = $this->create_posts();

		$sql = wl_core_sql_query_builder( array(
			'get'        => 'posts',
			'related_to' => $posts[ self::POST_AS_ARTICLE_2 ],
			'as'         => 'subject',
			'post_type'  => 'post',
		) );

		// Try to perform query in order to see if there are errors on db side
		$results = $wpdb->get_results( $sql );
		$this->assertEmpty( $wpdb->last_error );
		$this->assertCount( 2, $results, 'Expect 2 articles.' );

		$filtered = array_filter( $results, function ( $item ) {
			$terms = wp_get_post_terms( $item->ID, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

			return in_array( $item->post_type, Wordlift_Entity_Service::valid_entity_post_types() ) && ! empty( $terms ) && 'article' === $terms[0]->slug;
		} );

		$this->assertCount( 2, $filtered, 'Expect 2 articles.' );

	}

	function test_core_sql_query_builder_2() {
		global $wpdb;

		$posts = $this->create_posts( 6 );

		$sql = wl_core_sql_query_builder( array(
			'first'      => 10,
			'get'        => 'posts',
			'related_to' => $posts[1],
			'as'         => 'subject',
			'post_type'  => 'post',
		) );

		// Try to perform query in order to see if there are errors on db side
		$results = $wpdb->get_results( $sql );
		$this->assertEmpty( $wpdb->last_error );

		$this->assertCount( 10, $results, 'Expect 10 articles.' );

		$filtered = array_filter( $results, function ( $item ) {
			$terms = wp_get_post_terms( $item->ID, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

			return in_array( $item->post_type, Wordlift_Entity_Service::valid_entity_post_types() ) && ! empty( $terms ) && 'article' === $terms[0]->slug;
		} );

		$this->assertCount( 10, $filtered, 'Expect 10 articles.' );

	}

	function test_core_sql_query_builder_3() {
		global $wpdb;

		$posts = $this->create_posts( 1 );

		$sql = wl_core_sql_query_builder( array(
			'first'      => 10,
			'get'        => 'posts',
			'related_to' => $posts[2],
			'as'         => 'object',
			'post_type'  => 'post',
		) );

		// Try to perform query in order to see if there are errors on db side
		$results = $wpdb->get_results( $sql );
		$this->assertEmpty( $wpdb->last_error );

		$this->assertCount( 3, $results, 'Expect 3 articles.' );

		$filtered = array_filter( $results, function ( $item ) {
			$terms = wp_get_post_terms( $item->ID, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

			return in_array( $item->post_type, Wordlift_Entity_Service::valid_entity_post_types() ) && ! empty( $terms ) && 'article' === $terms[0]->slug;
		} );

		$this->assertCount( 3, $filtered, 'Expect 3 articles.' );

	}

	function test_core_sql_query_builder_4() {
		global $wpdb;

		$posts = $this->create_posts( 6 );

		$sql = wl_core_sql_query_builder( array(
			'first'      => 10,
			'get'        => 'posts',
			'related_to' => $posts[2],
			'as'         => 'object',
			'post_type'  => 'post',
		) );

		// Try to perform query in order to see if there are errors on db side
		$results = $wpdb->get_results( $sql );
		$this->assertEmpty( $wpdb->last_error );

		$this->assertCount( 10, $results, 'Expect 10 articles.' );

		$filtered = array_filter( $results, function ( $item ) {
			$terms = wp_get_post_terms( $item->ID, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

			return in_array( $item->post_type, Wordlift_Entity_Service::valid_entity_post_types() ) && ! empty( $terms ) && 'article' === $terms[0]->slug;
		} );

		$this->assertCount( 10, $filtered, 'Expect 10 articles.' );

	}

	function test_core_sql_query_builder_5() {
		global $wpdb;

		$posts = $this->create_posts( 1 );

		$sql = wl_core_sql_query_builder( array(
			'first'          => 10,
			'get'            => 'posts',
			'related_to'     => $posts[1],
			'as'             => 'object',
			'post_type'      => 'post',
			'with_predicate' => 'what',
		) );

		// Try to perform query in order to see if there are errors on db side
		$results = $wpdb->get_results( $sql );
		$this->assertEmpty( $wpdb->last_error );

		$this->assertCount( 2, $results, 'Expect 2 articles.' );

		$filtered = array_filter( $results, function ( $item ) {
			$terms = wp_get_post_terms( $item->ID, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

			return in_array( $item->post_type, Wordlift_Entity_Service::valid_entity_post_types() ) && ! empty( $terms ) && 'article' === $terms[0]->slug;
		} );

		$this->assertCount( 2, $filtered, 'Expect 2 articles.' );

	}

	function test_core_sql_query_builder_6() {
		global $wpdb;

		$posts = $this->create_posts( 1 );

		$sql = wl_core_sql_query_builder( array(
			'first'          => 10,
			'get'            => 'posts',
			'related_to'     => $posts[2],
			'related_to__in' => array( $posts[3], $posts[4] ),
			'post_type'      => 'post',
			'as'             => 'object',
		) );

		// Try to perform query in order to see if there are errors on db side
		$results = $wpdb->get_results( $sql );
		$this->assertEmpty( $wpdb->last_error );

		$this->assertCount( 0, $results, 'Expect 0 articles.' );

	}

	function test_core_sql_query_builder_7() {
		global $wpdb;

		$posts = $this->create_posts( 1 );

		$sql = wl_core_sql_query_builder( array(
			'first'          => 10,
			'get'            => 'posts',
			'related_to'     => $posts[1],
			'as'             => 'object',
			'post_type'      => 'post',
			'with_predicate' => 'what',
		) );

		// Try to perform query in order to see if there are errors on db side
		$results = $wpdb->get_results( $sql );
		$this->assertEmpty( $wpdb->last_error );

		$this->assertCount( 2, $results, 'Expect 2 articles.' );

		$filtered = array_filter( $results, function ( $item ) {
			$terms = wp_get_post_terms( $item->ID, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

			return in_array( $item->post_type, Wordlift_Entity_Service::valid_entity_post_types() ) && ! empty( $terms ) && 'article' === $terms[0]->slug;
		} );

		$this->assertCount( 2, $filtered, 'Expect 2 articles.' );

	}

	function test_core_sql_query_builder_8() {
		global $wpdb;

		$posts = $this->create_posts();

		$sql = wl_core_sql_query_builder( array(
			'first'          => 10,
			'get'            => 'posts',
			'related_to'     => $posts[1],
			'related_to__in' => array( $posts[2], $posts[3] ),
			'post_type'      => 'post',
			'as'             => 'object',
		) );

		// Try to perform query in order to see if there are errors on db side
		$results = $wpdb->get_results( $sql );
		$this->assertEmpty( $wpdb->last_error );

		$this->assertCount( 0, $results, 'Expect 2 articles.' );

	}

	function test_core_sql_query_builder_9() {
		global $wpdb;

		$posts = $this->create_posts();

		$sql = wl_core_sql_query_builder( array(
			'first'          => 10,
			'get'            => 'posts',
			'related_to__in' => array(
				$posts[ self::POST_AS_ARTICLE_1 ],
				$posts[ self::POST_AS_ARTICLE_2 ],
			),
			'post_type'      => 'post',
			'as'             => 'object',
		) );

		// Try to perform query in order to see if there are errors on db side
		$results = $wpdb->get_results( $sql );
		$this->assertEmpty( $wpdb->last_error );

		$this->assertCount( 1, $results, 'Expect 1 article.' );

		$filtered = array_filter( $results, function ( $item ) {
			$terms = wp_get_post_terms( $item->ID, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

			return in_array( $item->post_type, Wordlift_Entity_Service::valid_entity_post_types() ) && ! empty( $terms ) && 'article' === $terms[0]->slug;
		} );

		$this->assertCount( 1, $filtered, 'Expect 1 articles.' );

	}

	function test_core_sql_query_builder_10() {
		global $wpdb;

		$posts = $this->create_posts();

		$sql = wl_core_sql_query_builder( array(
			'get'            => 'posts',
			'related_to__in' => array(
				$posts[ self::POST_AS_ARTICLE_1 ],
				$posts[ self::POST_AS_ARTICLE_2 ],
			),
			'post__not_in'   => array( $posts[ self::PAGE_AS_ARTICLE ], ),
			'post_type'      => 'post',
			'as'             => 'object',
		) );

		// Try to perform query in order to see if there are errors on db side
		$results = $wpdb->get_results( $sql );
		$this->assertEmpty( $wpdb->last_error );

		$this->assertCount( 0, $results, 'Expect 1 article.' );

	}

	function test_core_sql_query_builder_11() {
		global $wpdb;

		$posts = $this->create_posts();

		$sql = wl_core_sql_query_builder( array(
			'get'         => 'posts',
			'related_to'  => $posts[ self::POST_AS_ARTICLE_1 ],
			'post_type'   => 'post',
			'post_status' => 'draft',
			'as'          => 'object',
		) );

		// Try to perform query in order to see if there are errors on db side
		$results = $wpdb->get_results( $sql );
		$this->assertEmpty( $wpdb->last_error );

		$this->assertCount( 0, $results, 'Expect 1 article.' );

	}

	function test_core_sql_query_builder_12() {
		global $wpdb;

		$posts = $this->create_posts();

		$sql = wl_core_sql_query_builder( array(
			'get'         => 'posts',
			'related_to'  => $posts[ self::POST_AS_ARTICLE_1 ],
			'post_type'   => 'post',
			'post_status' => null,
			'as'          => 'object',
		) );

		// Try to perform query in order to see if there are errors on db side
		$results = $wpdb->get_results( $sql );
		$this->assertEmpty( $wpdb->last_error );

		$this->assertCount( 2, $results, 'Expect 2 articles.' );

		$filtered = array_filter( $results, function ( $item ) {
			$terms = wp_get_post_terms( $item->ID, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

			return in_array( $item->post_type, Wordlift_Entity_Service::valid_entity_post_types() ) && ! empty( $terms ) && 'article' === $terms[0]->slug;
		} );

		$this->assertCount( 2, $filtered, 'Expect 2 articles.' );

	}

	function test_core_sql_query_builder_13() {
		global $wpdb;

		$posts = $this->create_posts();

		$sql = wl_core_sql_query_builder( array(
			'get'            => 'posts',
			'related_to__in' => array(
				$posts[ self::POST_AS_ARTICLE_1 ],
				$posts[ self::POST_AS_ARTICLE_2 ],
			),
			'post__in'       => array( $posts[ self::PAGE_AS_ARTICLE ], ),
			'post_type'      => 'post',
			'as'             => 'object',
		) );

		// Try to perform query in order to see if there are errors on db side
		$results = $wpdb->get_results( $sql );
		$this->assertEmpty( $wpdb->last_error );

		$this->assertCount( 1, $results, 'Expect 1 article.' );

		$filtered = array_filter( $results, function ( $item ) {
			$terms = wp_get_post_terms( $item->ID, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

			return in_array( $item->post_type, Wordlift_Entity_Service::valid_entity_post_types() ) && ! empty( $terms ) && 'article' === $terms[0]->slug;
		} );

		$this->assertCount( 1, $filtered, 'Expect 1 article.' );

	}

	/**
	 * @param $slug
	 *
	 * @return int
	 */
	private function get_term_id_by_slug( $slug ) {

		$term_id = get_term_by( 'slug', $slug, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME )->term_id;

		$this->assertGreaterThan( 0, $term_id, "Term $slug must exist." );

		return $term_id;
	}

	private function add_relation( $source, $destination ) {

		return wl_core_add_relation_instance(
			$source,
			Wordlift_Entity_Service::get_instance()
			                       ->get_classification_scope_for( $destination ),
			$destination
		);
	}

	function testWlCoreAddRelationInstance() {

		// Create a post and an entity
		$post_id   = wl_create_post( '', 'post1', 'A post' );
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
		$post_1_id   = wl_create_post( '', 'post1', 'A post' );
		$entity_1_id = wl_create_post( '', 'entity1', 'An Entity', 'draft', 'entity' );
		$entity_2_id = wl_create_post( '', 'entity2', 'An Entity', 'draft', 'entity' );

		// Stress method with strange parmeters
		$result = wl_core_add_relation_instances( '', WL_WHAT_RELATION, array(
			$entity_1_id,
			$entity_2_id,
		) );
		$this->assertFalse( $result );
		$result = wl_core_add_relation_instances( $post_1_id, WL_WHAT_RELATION, null );
		$this->assertFalse( $result );
		$result = wl_core_add_relation_instances( $post_1_id, WL_WHAT_RELATION, array() );
		$this->assertFalse( $result );
		$result = wl_core_add_relation_instances( $post_1_id, 'ulabadula', array(
			$entity_1_id,
			$entity_2_id,
		) );
		$this->assertFalse( $result );
		$result = wl_core_add_relation_instances( $post_1_id, 'ulabadula', array() );
		$this->assertFalse( $result );

		// Nothing has been inserted as relation so far.
		$result = wl_core_get_related_entity_ids( $post_1_id );
		$this->assertTrue( is_array( $result ) );
		$this->assertEmpty( $result );

		// Insert relation and verify it
		$result = wl_core_add_relation_instances( $post_1_id, WL_WHAT_RELATION, array(
			$entity_1_id,
			$entity_2_id,
		) );
		$this->assertTrue( is_numeric( $result[0] ) ); // The methods return an array of record ids
		$this->assertTrue( is_numeric( $result[1] ) ); // The methods return an array of record ids
		$this->assertCount( 2, $result );
		$result = wl_core_get_related_entity_ids( $post_1_id );
		$expected = array( $entity_1_id, $entity_2_id );
		// comapare diff of expected and results to make order irrelevant.
		$this->assertSame( array_diff( $expected, $result ), array_diff( $result, $expected ) );
	}

	function testWlCoreDeleteRelationInstance() {

		// Create a post and an entity
		$post_id   = wl_create_post( '', 'post1', 'A post' );
		$entity_id = wl_create_post( '', 'entity1', 'An Entity', 'draft', 'entity' );

		// No relations at this point
		$result = wl_tests_get_relation_instances_for( $post_id );
		$this->assertCount( 0, $result );
		// Insert relation and verify it
		$result = wl_core_add_relation_instance( $post_id, WL_WHAT_RELATION, $entity_id );
		$this->assertTrue( is_numeric( $result ) ); // The methods return a record id
		$result = wl_tests_get_relation_instances_for( $post_id );
		$this->assertCount( 1, $result );

		$result = wl_core_delete_relation_instance( $post_id, WL_WHAT_RELATION, $entity_id );
		$this->assertTrue( $result );
		$result = wl_tests_get_relation_instances_for( $post_id );
		$this->assertCount( 0, $result );

	}

	function testWlCoreDeleteRelationInstances() {

		// Create a post and an entity
		$post_id   = wl_create_post( '', 'post1', 'A post' );
		$entity_id = wl_create_post( '', 'entity1', 'An Entity', 'draft', 'entity' );

		// No relations at this point
		$result = wl_tests_get_relation_instances_for( $post_id );
		$this->assertCount( 0, $result );
		// Insert relation and verify it
		$result = wl_core_add_relation_instance( $post_id, WL_WHAT_RELATION, $entity_id );
		$this->assertTrue( is_numeric( $result ) ); // The methods return a record id
		$result = wl_core_add_relation_instance( $post_id, WL_WHO_RELATION, $entity_id );
		$this->assertTrue( is_numeric( $result ) ); // The methods return a record id

		$result = wl_tests_get_relation_instances_for( $post_id );
		$this->assertCount( 2, $result );

		$result = wl_core_delete_relation_instances( $post_id );
		$this->assertTrue( $result );
		$result = wl_tests_get_relation_instances_for( $post_id );
		$this->assertCount( 0, $result );

	}

	function testWlCoreGetRelatedPostIdsForAnEntity() {

		// Create 2 posts and 1 entities
		$post_1_id   = wl_create_post( '', 'post1', 'A post' );
		$post_2_id   = wl_create_post( '', 'post2', 'A post' );
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
			'predicate' => WL_WHERE_RELATION,
		) );
		$this->assertCount( 1, $result );
		$this->assertTrue( in_array( $post_1_id, $result ) );

		$result = wl_core_get_related_post_ids( $entity_1_id, array(
			'predicate' => WL_WHO_RELATION,
		) );
		$this->assertCount( 1, $result );
		$this->assertTrue( in_array( $post_2_id, $result ) );

		$result = wl_core_get_related_post_ids( $entity_1_id, array(
			'predicate' => WL_WHAT_RELATION,
		) );
		$this->assertCount( 0, $result );

	}

	function testWlCoreGetRelatedPostIdsForAPost() {

		// Create 2 posts and 1 entities
		$post_1_id   = wl_create_post( '', 'post1', 'A post' );
		$post_2_id   = wl_create_post( '', 'post2', 'A post' );
		$entity_1_id = wl_create_post( '', 'entity1', 'An Entity', 'draft', 'entity' );

		// Insert relations
		wl_core_add_relation_instance( $post_1_id, WL_WHERE_RELATION, $entity_1_id );
		wl_core_add_relation_instance( $post_2_id, WL_WHO_RELATION, $entity_1_id );

		// Check relation are retrieved as expected
		$result = wl_core_get_related_post_ids( $post_1_id );
		$this->assertCount( 1, $result );
		$this->assertTrue( in_array( $post_2_id, $result ) );

		$result = wl_core_get_related_post_ids( $post_1_id, array(
			'predicate' => WL_WHERE_RELATION,
		) );
		$this->assertCount( 0, $result );

		$result = wl_core_get_related_post_ids( $post_1_id, array(
			'predicate' => WL_WHO_RELATION,
		) );
		$this->assertCount( 1, $result );
		$this->assertTrue( in_array( $post_2_id, $result ) );

	}

	function testWlCoreGetRelatedEntityIdsForAPost() {

		// Create 2 posts and 1 entities
		$post_1_id   = wl_create_post( '', 'post1', 'A post' );
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
			'predicate' => WL_WHERE_RELATION,
		) );
		$this->assertCount( 1, $result );
		$this->assertTrue( in_array( $entity_1_id, $result ) );

		$result = wl_core_get_related_entity_ids( $post_1_id, array(
			'predicate' => WL_WHO_RELATION,
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
			'predicate' => WL_WHERE_RELATION,
		) );
		$this->assertCount( 1, $result );
		$this->assertTrue( in_array( $entity_1_id, $result ) );

		$result = wl_core_get_related_entity_ids( $entity_0_id, array(
			'predicate' => WL_WHO_RELATION,
		) );
		$this->assertCount( 1, $result );
		$this->assertTrue( in_array( $entity_2_id, $result ) );

	}

}
