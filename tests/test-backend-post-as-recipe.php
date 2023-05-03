<?php

use Wordlift\Relation\Relations;

/**
 * @group backend
 */

class Wordlift_Post_As_Recipe_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Post_To_JsonLd_Converter} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Post_To_Jsonld_Converter $post_to_jsonld_converter The {@link Wordlift_Post_To_JsonLd_Converter} instance.
	 */
	private $post_to_jsonld_converter;

	/**
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();

		$this->post_to_jsonld_converter = Wordlift_Post_To_Jsonld_Converter::get_instance();

	}

	/**
	 * Check that a {@link WP_Post} is automatically assigned with the `Article`
	 * entity type.
	 *
	 * @since 3.15.0
	 */
	public function test_post_as_article() {

		// Create a post
		$post_id = $this->factory->post->create( array(
			'status' => 'publish',
		) );

		// Check that the post by default is marked as `http://schema.org/Article`.
		$type = Wordlift_Entity_Type_Service::get_instance()->get( $post_id );

		// Assertions.
		$this->assertTrue( is_array( $type ) );
		$this->assertArrayHasKey( 'uri', $type, 'The type array must contain the schema.org URI.' );
		$this->assertEquals( 'http://schema.org/Article', $type['uri'], 'The schema.org URI must be http://schema.org/Article.' );

		// Try to find the post with an `article` taxonomy query.
		$posts = get_posts( array(
			'posts_per_page' => 1,
			'orderby'        => 'ID',
			'order'          => 'desc',
			'tax_query'      => array(
				array(
					'taxonomy' => Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
					'field'    => 'slug',
					'terms'    => 'article',
				),
			),
		) );

		// JSON-LD.
		$empty = array();
		$jsonld    = Wordlift_Post_To_Jsonld_Converter::get_instance()->convert( $post_id, $empty, $empty, new Relations() );
		$permalink = get_permalink( $post_id );

		// Assertions.
		$this->assertCount( 1, $posts, 'There must be one post found.' );
		$this->assertEquals( $post_id, $posts[0]->ID, 'The found post ID must match the ID of the test post.' );
		$this->assertArraySubset( array(
			'@type'            => 'Article',
			'mainEntityOfPage' => $permalink,
		), $jsonld, 'Expect the JSON-LD to use `Article`.' );

	}

	/**
	 * Check that a {@link WP_Post} can be assigned the `Recipe` entity type.
	 *
	 * @since 3.15.0
	 */
	public function test_post_as_recipe() {

		// Create a post
		$post_id = $this->factory->post->create();

		// Assign the `Recipe` class.
		Wordlift_Entity_Type_Service::get_instance()->set( $post_id, 'http://schema.org/Recipe' );

		// Check that the post is now configured as `http://schema.org/Recipe`.
		$type = Wordlift_Entity_Type_Service::get_instance()->get( $post_id );

		// Assertions.
		$this->assertTrue( is_array( $type ) );
		$this->assertArrayHasKey( 'uri', $type, 'The type array must contain the schema.org URI.' );
		$this->assertEquals( 'http://schema.org/Recipe', $type['uri'], 'The schema.org URI must be http://schema.org/Recipe.' );

		// Try to find the post with an `article` taxonomy query.
		$posts = get_posts( array(
			'posts_per_page' => 1,
			'orderby'        => 'ID',
			'order'          => 'desc',
			'tax_query'      => array(
				array(
					'taxonomy' => Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
					'field'    => 'slug',
					'terms'    => 'recipe',
				),
			),
		) );

		// Assertions.
		$this->assertCount( 1, $posts, 'There must be one post found.' );
		$this->assertEquals( $post_id, $posts[0]->ID, 'The found post ID must match the ID of the test post.' );

		// JSON-LD.
		$empty = array();
		$jsonld    = $this->post_to_jsonld_converter->convert( $post_id, $empty, $empty, new Relations());
		$permalink = get_permalink( $post_id );

		// Assertions.
		$this->assertCount( 1, $posts, 'There must be one post found.' );
		$this->assertEquals( $post_id, $posts[0]->ID, 'The found post ID must match the ID of the test post.' );
		$this->assertArraySubset( array(
			'@type'            => 'Recipe',
			'mainEntityOfPage' => $permalink,
		), $jsonld, 'Expect the JSON-LD to use `Recipe`.' );

	}

}
