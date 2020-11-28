<?php
/**
 * Tests: Entity Type Taxonomy_Service Test.
 *
 * Test the {@link Wordlift_Entity_Type_Taxonomy_Service} class.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Wordlift_Entity_Type_Taxonomy_Service_Test class.
 *
 * @since 3.20.0
 * @group entity
 */
class Wordlift_Entity_Type_Taxonomy_Service_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test the `init` function.
	 *
	 * The `init` function should have registered our custom taxonomy.
	 *
	 * @since 3.20.0
	 */
	public function test_init() {

		global $wp_taxonomies;

		$this->assertTrue( is_array( $wp_taxonomies ), 'The `$wp_taxonomies` must be an array.' );
		$this->assertArrayHasKey( 'wl_entity_type', $wp_taxonomies, 'The `$wp_taxonomies` must contain `wl_entity_type`.' );
		$this->assertObjectHasAttribute( 'meta_box_cb', $wp_taxonomies['wl_entity_type'], 'The `wl_entity_type` taxonomy object must have `meta_box_cb`.' );
		$this->assertTrue( is_array( $wp_taxonomies['wl_entity_type']->meta_box_cb ), 'The `meta_box_cb` structure must be an array.' );
		$this->assertCount( 2, $wp_taxonomies['wl_entity_type']->meta_box_cb, 'The `meta_box_cb` array must contain 2 items.' );
		$this->assertEquals( 'Wordlift_Admin_Schemaorg_Taxonomy_Metabox', $wp_taxonomies['wl_entity_type']->meta_box_cb[0], 'The `meta_box_cb` array must contain `Wordlift_Admin_Schemaorg_Taxonomy_Metabox`.' );
		$this->assertEquals( 'render', $wp_taxonomies['wl_entity_type']->meta_box_cb[1], 'The `meta_box_cb` array must contain `render`.' );

	}

	public function test_article_by_default() {

		$post_id = $this->factory()->post->create();

		$this->assertNotWPError( $post_id );

		$terms_1 = wp_get_object_terms( $post_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		$this->assertTrue( is_array( $terms_1 ), 'Terms should be an array, but was: ' . var_export( $terms_1, true ) );
		$this->assertCount( 1, $terms_1, 'Terms should contain one term, but was: ' . var_export( $terms_1, true ) );
		$this->assertTrue( is_a( $terms_1[0], 'WP_Term' ), 'Term should be WP_Term, but was: ' . var_export( $terms_1[0], true ) );
		$this->assertEquals( 'article', $terms_1[0]->slug, 'Term should be article, but was ' . $terms_1[0]->slug );

		$result = wp_remove_object_terms( $post_id, 'article', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		$this->assertTrue( $result, 'Removing object terms should be successful.' );

		$terms_2 = wp_get_object_terms( $post_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		$this->assertTrue( is_array( $terms_2 ), 'Terms should be an array, but was: ' . var_export( $terms_2, true ) );
		$this->assertCount( 1, $terms_2, 'Terms should contain one term, but was: ' . var_export( $terms_2, true ) );
		$this->assertTrue( is_a( $terms_2[0], 'WP_Term' ), 'Term should be WP_Term, but was: ' . var_export( $terms_2[0], true ) );
		$this->assertEquals( 'article', $terms_2[0]->slug, 'Term should be article, but was ' . $terms_2[0]->slug );

	}

	public function test_thing_by_default() {

		$post_id = $this->factory()->post->create( array(
			'post_type' => 'entity'
		) );

		$this->assertNotWPError( $post_id );

		$terms_1 = wp_get_object_terms( $post_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		$this->assertTrue( is_array( $terms_1 ), 'Terms should be an array, but was: ' . var_export( $terms_1, true ) );
		$this->assertCount( 1, $terms_1, 'Terms should contain one term, but was: ' . var_export( $terms_1, true ) );
		$this->assertTrue( is_a( $terms_1[0], 'WP_Term' ), 'Term should be WP_Term, but was: ' . var_export( $terms_1[0], true ) );
		$this->assertEquals( 'thing', $terms_1[0]->slug, 'Term should be thing, but was ' . $terms_1[0]->slug );

		$result = wp_remove_object_terms( $post_id, 'thing', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		$this->assertTrue( $result, 'Removing object terms should be successful.' );

		$terms_2 = wp_get_object_terms( $post_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		$this->assertTrue( is_array( $terms_2 ), 'Terms should be an array, but was: ' . var_export( $terms_2, true ) );
		$this->assertCount( 1, $terms_2, 'Terms should contain one term, but was: ' . var_export( $terms_2, true ) );
		$this->assertTrue( is_a( $terms_2[0], 'WP_Term' ), 'Term should be WP_Term, but was: ' . var_export( $terms_2[0], true ) );
		$this->assertEquals( 'thing', $terms_2[0]->slug, 'Term should be thing, but was ' . $terms_2[0]->slug );

	}

}
