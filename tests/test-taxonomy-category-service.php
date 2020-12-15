<?php
/**
 * Tests: Category Taxonomy Service.
 *
 * @since   3.11.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Category_Taxonomy_Service_Test} class.
 *
 * @since   3.11.0
 * @package Wordlift
 * @group taxonomy
 */
class Wordlift_Category_Taxonomy_Service_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test non main query is ignored.
	 *
	 * @since 3.11
	 */
	public function test_non_main_query_ignored() {

		$post_id = $this->factory->post->create( array(
			'post_type'    => 'post',
			'post_content' => '',
			'post_title'   => 'test post',
			'post_status'  => 'publish',
		) );

		$entity_id   = $this->factory->post->create( array(
			'post_type'    => 'entity',
			'post_content' => '',
			'post_title'   => 'test post',
			'post_status'  => 'publish',
		) );
		$category_id = $this->factory->category->create( array( 'name' => 'testcat' ) );

		// associate both post and entity with the same category.
		wp_set_post_terms( $post_id, array( $category_id ), 'category' );
		wp_set_post_terms( $entity_id, array( $category_id ), 'category' );

		$query = new WP_Query( array(
			'cat' => $category_id,
		) );
		$posts = $query->get_posts();

		// test that only one post is returned.
		$this->assertEquals( 1, count( $posts ) );

		// ... and that it is the actual post.
		$this->assertEquals( $post_id, $posts[0]->ID );
	}

	/**
	 * Test category archive page shows both posts and entities.
	 *
	 * Test done by simulating a main query.
	 *
	 * @since 3.11
	 */
	public function test_category_archive_page() {

		$post_id = $this->factory->post->create( array(
			'post_type'    => 'post',
			'post_content' => '',
			'post_title'   => 'test post',
			'post_status'  => 'publish',
		) );

		$entity_id   = $this->factory->post->create( array(
			'post_type'    => 'entity',
			'post_content' => '',
			'post_title'   => 'test post',
			'post_status'  => 'publish',
		) );
		$category_id = $this->factory->category->create( array( 'name' => 'testcat' ) );

		// associate both post and entity with the same category.
		wp_set_post_terms( $post_id, array( $category_id ), 'category' );
		wp_set_post_terms( $entity_id, array( $category_id ), 'category' );

		$this->go_to( '?cat=' . $category_id );

		global $wp_the_query;

		$posts = $wp_the_query->get_posts();

		// test that two posts are returned.
		$this->assertEquals( 2, count( $posts ) );

		// ... and that it is the actual posts.
		// since creation is about the same time we can't count on consistant
		// result order.
		$posts_ids = array( $posts[1]->ID, $posts[0]->ID );
		$this->assertTrue( in_array( $entity_id, $posts_ids ) );
		$this->assertTrue( in_array( $post_id, $posts_ids ) );
	}

	/**
	 * Test queries with explicit post type specification
	 *
	 * @since 3.11
	 */
	public function test_explicit_post_type() {

		$post_id = $this->factory->post->create( array(
			'post_type'    => 'post',
			'post_content' => '',
			'post_title'   => 'test post',
			'post_status'  => 'publish',
		) );

		$entity_id   = $this->factory->post->create( array(
			'post_type'    => 'entity',
			'post_content' => '',
			'post_title'   => 'test post',
			'post_status'  => 'publish',
		) );
		$category_id = $this->factory->category->create( array( 'name' => 'testcat' ) );

		// associate both post and entity with the same category.
		wp_set_post_terms( $post_id, array( $category_id ), 'category' );
		wp_set_post_terms( $entity_id, array( $category_id ), 'category' );

		// reset global query variables to virgin state to make is_main_query true.

		global $wp_the_query, $wp_query;
		$wp_the_query = new WP_QUery();
		$wp_query     = $wp_the_query;

		// test explicit post post type as a string.

		$posts = $wp_the_query->query( array(
			'cat'       => $category_id,
			'post_type' => 'post',
		) );

		// test that two posts are returned.
		$this->assertEquals( 2, count( $posts ) );

		// ... and that it is the actual posts.
		// since creation is about the same time we can't count on consistant
		// result order.
		$posts_ids = array( $posts[1]->ID, $posts[0]->ID );
		$this->assertTrue( in_array( $entity_id, $posts_ids ) );
		$this->assertTrue( in_array( $post_id, $posts_ids ) );

		// test explicit post post type as an array.

		$posts = $wp_the_query->query( array(
			'cat'       => $category_id,
			'post_type' => array( 'post' ),
		) );

		// test that two posts are returned.
		$this->assertEquals( 2, count( $posts ) );

		// ... and that it is the actual posts.
		// since creation is about the same time we can't count on consistant
		// result order.
		$posts_ids = array( $posts[1]->ID, $posts[0]->ID );
		$this->assertTrue( in_array( $entity_id, $posts_ids ) );
		$this->assertTrue( in_array( $post_id, $posts_ids ) );

		// test explicit post post type with combination with other post type.

		$posts = $wp_the_query->query( array(
			'cat'       => $category_id,
			'post_type' => array( 'post', 'product' ),
		) );

		// test that two posts are returned.

		$this->assertEquals( 2, count( $posts ) );

		// ... and that it is the actual posts.
		// since creation is about the same time we can't count on consistant
		// result order.
		$posts_ids = array( $posts[1]->ID, $posts[0]->ID );
		$this->assertTrue( in_array( $entity_id, $posts_ids ) );
		$this->assertTrue( in_array( $post_id, $posts_ids ) );

		// test unrelated post type.

		$posts = $wp_the_query->query( array(
			'cat'       => $category_id,
			'post_type' => array( 'product' ),
		) );

		// no posts should be returned
		$this->assertEquals( 0, count( $posts ) );
	}

	/**
	 * Test queries in admin context are ignored.
	 *
	 * @since 3.11
	 */
	public function test_admin_context() {

		$post_id = $this->factory->post->create( array(
			'post_type'    => 'post',
			'post_content' => '',
			'post_title'   => 'test post',
			'post_status'  => 'publish',
		) );

		$entity_id   = $this->factory->post->create( array(
			'post_type'    => 'entity',
			'post_content' => '',
			'post_title'   => 'test post',
			'post_status'  => 'publish',
		) );
		$category_id = $this->factory->category->create( array( 'name' => 'testcat' ) );

		// associate both post and entity with the same category.
		wp_set_post_terms( $post_id, array( $category_id ), 'category' );
		wp_set_post_terms( $entity_id, array( $category_id ), 'category' );

		global $wp_the_query; // the main query object.

		$posts = $wp_the_query->query( array(
			'cat' => $category_id,
		) );

		// test that only one post is returned.
		$this->assertEquals( 1, count( $posts ) );

		// ... and that it is the actual post.
		$this->assertEquals( $post_id, $posts[0]->ID );
	}

}
