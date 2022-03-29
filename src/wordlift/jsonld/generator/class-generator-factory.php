<?php

namespace Wordlift\Jsonld\Generator;

use Wordlift\External_Plugin_Hooks\Recipe_Maker\Recipe_Maker_Validation_Service;

class Generator_Factory {


	/**
	 * @param  $jsonld_service \Wordlift_Jsonld_Service
	 *
	 * @return Generator
	 */
	public static function get_instance( $jsonld_service, $post_id ) {
		$recipe_maker_validation_service = Recipe_Maker_Validation_Service::get_instance();
		if ( is_singular()
		     // No Mentions on home page.
		     && ! is_home()
		     && $recipe_maker_validation_service->is_yoast_active()
		     && $recipe_maker_validation_service->is_wp_recipe_maker_available()
		     && $recipe_maker_validation_service->is_recipe_maker_yoast_integration_on()
		     && $recipe_maker_validation_service->is_atleast_once_recipe_present_in_the_post( $post_id )
		) {
			return new Recipe_Maker_Yoast_Generator( $jsonld_service );
		}

		return new Default_Generator( $jsonld_service );
	}



}