<?php
/**
 * @since 3.27.2
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\External_Plugin_Hooks;

/**
 * This class helps to remove the jsonld of wp recipe maker for the post and add the jsonld to
 * wordlift jsonld.
 * Class Recipe_Maker_Jsonld_Hook
 * @package Wordlift\External_Plugin_Hooks
 */
class Recipe_Maker_Jsonld_Hook {

	public function __construct() {
		/**
		 * see issue #1121: Integrate the jsonld of wp recipe maker in to
		 * wordlift jsonld.
		 */
		add_filter( 'wprm_recipe_metadata', function ( $metadata, $recipe ) {
			// The metadata contains the jsonld structure.
			return array();
		}, 10, 2 );

		add_filter( 'wl_post_jsonld', array( $this, 'add_recipe_jsonld' ), 10, 3 );

	}

	public function add_recipe_jsonld( $jsonld, $post_id, $references ) {
		if ( ! class_exists( 'WPRM_Recipe_Manager' ) ) {
			return $jsonld;
		}
		var_dump( \WPRM_Recipe_Manager::get_recipe( $post_id ) );
		wp_die();
	}
}
