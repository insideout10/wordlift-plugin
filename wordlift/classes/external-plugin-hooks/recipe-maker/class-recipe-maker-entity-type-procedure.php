<?php

namespace Wordlift\External_Plugin_Hooks\Recipe_Maker;

/**
 * This procedure set all wp recipe maker posts to Recipe entity type
 * This is run on wordlift activation hook.
 * Class Recipe_Maker_Entity_Type_Procedure
 *
 * @package Wordlift\External_Plugin_Hooks
 */
class Recipe_Maker_Entity_Type_Procedure {

	/**
	 * Run the procedure and set all wprecipe post type
	 * to 'Recipe' in entity type.
	 */
	public function run_procedure() {
		$posts = $this->get_all_published_recipe_maker_posts();
		foreach ( $posts as $post_id ) {
			// set entity type to Product.
			\Wordlift_Entity_Type_Service::get_instance()
										->set(
											$post_id,
											'http://schema.org/Recipe',
											true
										);
		}
	}

	/**
	 * @return int[]|\WP_Post[]
	 */
	private function get_all_published_recipe_maker_posts() {
		return get_posts(
			array(
				'post_type'      => Recipe_Maker_Post_Type_Hook::RECIPE_MAKER_POST_TYPE,
				'posts_per_page' => - 1,
				'post_status'    => 'publish',
				'fields'         => 'ids',
			)
		);
	}

}
