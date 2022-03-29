<?php

namespace Wordlift\Jsonld\Generator;

class Generator_Factory {


	/**
	 * @param  $jsonld_service \Wordlift_Jsonld_Service
	 *
	 * @return Generator
	 */
	public static function get_instance( $jsonld_service, $post_id ) {

		if ( is_singular()
		     // No Mentions on home page.
		     && ! is_home()
		     && self::is_yoast_active()
		     && self::is_recipe_maker_active()
		     && self::is_recipe_maker_yoast_integration_on()
		     && self::is_atleast_one_recipe_embedded_in_post( $post_id )
		) {
			return new Recipe_Maker_Yoast_Generator( $jsonld_service );
		}

		return new Default_Generator( $jsonld_service );
	}

	private static function is_yoast_active() {
		return defined( 'WPSEO_VERSION' );
	}

	private static function is_recipe_maker_active() {
		return class_exists( '\WPRM_Recipe_Manager' );
	}

	private static function is_recipe_maker_yoast_integration_on() {

		if ( ! class_exists( '\WPRM_Settings' ) ) {
			return false;
		}

		return \WPRM_Settings::get( 'yoast_seo_integration' ) && interface_exists( 'WPSEO_Graph_Piece' );
	}

	private static function is_atleast_one_recipe_embedded_in_post( $post_id ) {

		if ( ! class_exists( '\WPRM_Recipe_Manager' ) ) {
			return false;
		}

		$recipe_ids = \WPRM_Recipe_Manager::get_recipe_ids_from_post( $post_id );

		return is_array( $recipe_ids ) && count( $recipe_ids ) > 0;
	}


}