<?php

namespace Wordlift\External_Plugin_Hooks\Recipe_Maker;

/**
 * @since 3.27.2
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Recipe_Maker_Validation_Service {

	public function is_atleast_once_recipe_present_in_the_post( $post_id ) {

		if ( ! $this->is_wp_recipe_maker_available() ) {
			return false;
		}
		$recipe_ids = \WPRM_Recipe_Manager::get_recipe_ids_from_post( $post_id );

		return is_array( $recipe_ids ) ? count( $recipe_ids ) > 0 : false;
	}

	public function is_wp_recipe_maker_available() {
		/**
		 * Dont alter the jsonld if the classes are not present.
		 */
		if ( ! class_exists( '\WPRM_Recipe_Manager' ) || ! class_exists( 'WPRM_Metadata' ) ) {
			return false;
		}
		if ( ! method_exists( '\WPRM_Recipe_Manager', 'get_recipe_ids_from_post' ) ||
			 ! method_exists( '\WPRM_Recipe_Manager', 'get_recipe' ) ||
			 ! method_exists( '\WPRM_Metadata', 'get_metadata_details' )
		) {
			return false;
		}

		return true;
	}

}
