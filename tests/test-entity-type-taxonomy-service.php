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

}
