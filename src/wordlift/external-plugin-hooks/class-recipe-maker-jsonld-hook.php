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
		add_filter( 'wprm_recipe_metadata', array( $this, 'swap_jsonld' ), 10, 2 );

		add_filter( 'wl_entity_jsonld_array', array( $this, 'wl_entity_jsonld_array' ), 10, 2 );

		add_filter( 'wl_post_jsonld', array( $this, 'wl_post_jsonld' ), 10, 3 );

	}

	public function swap_jsonld( $metadata, $recipe ) {
		// Return empty jsonld array.
		return array();
	}

	public function wl_entity_jsonld_array( $arr, $post_id ) {

		$jsonld     = $arr['jsonld'];
		$references = $arr['references'];

		/**
		 * Dont alter the jsonld if the classes are not present.
		 */
		if ( ! class_exists( '\WPRM_Recipe_Manager' ) || ! class_exists( 'WPRM_Metadata' ) ) {
			return $arr;
		}
		if ( ! method_exists( '\WPRM_Recipe_Manager', 'get_recipe_ids_from_post' ) ||
		     ! method_exists( '\WPRM_Recipe_Manager', 'get_recipe' ) ||
		     ! method_exists( '\WPRM_Metadata', 'get_metadata_details' )
		) {
			return $arr;
		}

		// 1. Get the jsonld from recipe maker for the post id.
		$recipe_ids = \WPRM_Recipe_Manager::get_recipe_ids_from_post( $post_id );

		// If there are no associated recipes for a post id then return early
		if ( ! $recipe_ids ) {
			return $jsonld;
		}

		return array(
			'jsonld'     => $jsonld,
			'references' => array_merge( $recipe_ids, $references )
		);

	}

	public function wl_post_jsonld( $jsonld, $post_id, $references ) {
		$recipe_data = $this->process_single_recipe_item( $post_id );
		if ( ! $recipe_data ) {
			return $jsonld;
		}

		return $recipe_data + $jsonld;
	}

	/**
	 * @param $linked_recipe_id
	 *
	 * @return array
	 */
	private function process_single_recipe_item( $linked_recipe_id ) {
		$linked_recipe = \WPRM_Recipe_Manager::get_recipe( $linked_recipe_id );
		if ( $linked_recipe ) {
			return \WPRM_Metadata::get_metadata_details( $linked_recipe ) ?: array();
		}

		return array();
	}
}
