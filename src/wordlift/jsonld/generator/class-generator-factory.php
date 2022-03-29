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
		if ( $recipe_maker_validation_service->can_integrate_with_yoast_jsonld( $post_id ) ) {
			return new Recipe_Maker_Yoast_Generator( $jsonld_service );
		}

		return new Default_Generator( $jsonld_service );
	}


}