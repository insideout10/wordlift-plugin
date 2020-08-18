<?php

use Wordlift\External_Plugin_Hooks\Recipe_Maker_Post_Type_Hook;

/**
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.27.1
 */

class Install_3_27_1_Test extends Wordlift_Unit_Test_Case {

	private $instance;

	public function setUp() {
		parent::setUp();
		$this->instance = new Wordlift_Install_3_27_1();
	}


	public function test_recipe_maker_cpt_should_be_assigned_recipe() {
		$post_1 = $this->factory()->post->create( array( 'post_type' => Recipe_Maker_Post_Type_Hook::RECIPE_MAKER_POST_TYPE ) );
		$post_2 = $this->factory()->post->create( array( 'post_type' => Recipe_Maker_Post_Type_Hook::RECIPE_MAKER_POST_TYPE ) );
		$this->instance->install();
		$selected_entities = wp_get_object_terms( $post_1, \Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
		$this->assertCount( 1, $selected_entities );
		$selected_entities = wp_get_object_terms( $post_2, \Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
		$this->assertCount( 1, $selected_entities );
		$term = current( $selected_entities );
		$this->assertEquals( 'Recipe', $term->name );
	}


}
