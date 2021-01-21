<?php
/**
 * @since 3.28.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * Class Entity_Taxonomy_No_Index_Test
 * @group entity
 */
class Entity_Taxonomy_No_Index_Test extends Wordlift_Unit_Test_Case {

	public function setUp() {
		global $wp_filter;
		$wp_filter = array();
		run_wordlift();
		parent::setUp();

	}


	public function test_should_remove_entity_taxonomy_from_wp_sitemap() {
		$taxonomies = apply_filters( 'wp_sitemaps_taxonomies', array( 'wl_entity_type'=> array() ) );
		$this->assertCount(0, $taxonomies, 'wl_entity_type taxonomy should be removed');
	}


}
