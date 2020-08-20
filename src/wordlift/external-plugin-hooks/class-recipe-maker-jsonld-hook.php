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

	/**
	 * @var \Wordlift_Attachment_Service
	 */
	private $attachment_service;

	/**
	 * Recipe_Maker_Jsonld_Hook constructor.
	 *
	 * @param $attachment_service \Wordlift_Attachment_Service
	 */
	public function __construct( $attachment_service ) {

		$this->attachment_service = $attachment_service;
		// Configure jsonld using filters.
		$this->remove_recipe_maker_jsonld();
		$this->merge_recipe_jsonld();

		// Add the filter to alter final jsonld.
		add_filter( 'wl_page_jsonld', array( $this, 'wl_page_jsonld' ), 10, 2 );
	}

	private function remove_recipe_maker_jsonld() {
		/**
		 * see issue #1121: Integrate the jsonld of wp recipe maker in to
		 * wordlift jsonld.
		 */
		add_filter( 'wprm_recipe_metadata', array( $this, 'swap_jsonld' ), 10, 2 );
	}

	private function merge_recipe_jsonld() {
		// First we push all the linked recipes to references.
		add_filter( 'wl_entity_jsonld_array', array( $this, 'wl_entity_jsonld_array' ), 10, 2 );
		add_filter( 'wl_post_jsonld_array', array( $this, 'wl_entity_jsonld_array' ), 10, 2 );

		// Then we merge the jsonld for every recipe.
		add_filter( 'wl_entity_jsonld', array( $this, 'wl_entity_jsonld' ), 10, 3 );
	}


	/**
	 * Swap the valid jsonld with empty array so that recipe maker
	 * wont output the jsonld.
	 *
	 * @param $metadata
	 * @param $recipe
	 *
	 * @return array
	 */
	public function swap_jsonld( $metadata, $recipe ) {
		// Return empty jsonld array.
		return array();
	}

	public function wl_entity_jsonld_array( $arr, $post_id ) {

		$jsonld     = $arr['jsonld'];
		$references = $arr['references'];

		// check if wp recipe maker installed, if not return early.
		if ( ! $this->is_wp_recipe_maker_available() ) {
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

	private function is_wp_recipe_maker_available() {
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

	public function wl_entity_jsonld( $jsonld, $post_id, $references ) {
		$recipe_data = $this->process_single_recipe_item( $post_id );
		if ( ! $recipe_data ) {
			return $jsonld;
		}

		// Set image via wordlift.
		\Wordlift_Abstract_Post_To_Jsonld_Converter::set_images(
			$this->attachment_service,
			get_post( $post_id ),
			$recipe_data
		);

		if ( ! $jsonld ) {
			return $recipe_data;
		}

		return $recipe_data + $jsonld;
	}

	/**
	 * @param $linked_recipe_id int post id of that recipe.
	 *
	 * @return array Recipe valid jsonld data.
	 */
	private function process_single_recipe_item( $linked_recipe_id ) {
		// check if recipe maker present.
		if ( ! $this->is_wp_recipe_maker_available() ) {
			return array();
		}
		$linked_recipe = \WPRM_Recipe_Manager::get_recipe( $linked_recipe_id );
		if ( $linked_recipe ) {
			return \WPRM_Metadata::get_metadata_details( $linked_recipe ) ?: array();
		}

		return array();
	}

	/**
	 * Add isPartOf to all the recipes.
	 *
	 * @param $jsonld array
	 *
	 * @param $post_id int
	 *
	 * @return array The altered jsonld array.
	 */
	public function wl_page_jsonld( $jsonld, $post_id ) {
		if ( ! is_array( $jsonld ) || count( $jsonld ) === 0 ) {
			return $jsonld;
		}
		// If there are no recipes in the post then dont alter the jsonld.
		if ( ! $this->is_atleast_once_recipe_present_in_the_post( $post_id ) ) {
			return $jsonld;
		}

		$post_jsonld    = $jsonld[0];
		$post_jsonld_id = array_key_exists( '@id', $post_jsonld ) ? $post_jsonld['@id'] : false;

		if ( ! $post_jsonld_id ) {
			return $jsonld;
		}

		foreach ( $jsonld as $key => $value ) {
			if ( array_key_exists( '@type', $value ) && $value['@type'] === 'Recipe' ) {
				$value['isPartOf'] = array(
					'@id' => $post_jsonld_id
				);
				$jsonld[ $key ]    = $value;
			}
		}

		return $jsonld;
	}


	public function is_atleast_once_recipe_present_in_the_post( $post_id ) {

		if ( ! $this->is_wp_recipe_maker_available() ) {
			return false;
		}
		$recipe_ids = \WPRM_Recipe_Manager::get_recipe_ids_from_post( $post_id );

		return is_array( $recipe_ids ) ? count( $recipe_ids ) > 0 : false;
	}
}
