<?php
/**
 * @since 3.27.2
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\External_Plugin_Hooks\Recipe_Maker;

/**
 * This class helps to remove the jsonld of wp recipe maker for the post and add the jsonld to
 * wordlift jsonld.
 * Class Recipe_Maker_Jsonld_Hook
 *
 * @package Wordlift\External_Plugin_Hooks
 */
class Recipe_Maker_Jsonld_Hook {

	/**
	 * @var \Wordlift_Attachment_Service
	 */
	private $attachment_service;
	/**
	 * @var Recipe_Maker_Validation_Service
	 */
	private $recipe_maker_validation_service;

	/**
	 * Recipe_Maker_Jsonld_Hook constructor.
	 *
	 * @param $attachment_service \Wordlift_Attachment_Service
	 * @param $recipe_maker_validation_service Recipe_Maker_Validation_Service
	 */
	public function __construct( $attachment_service, $recipe_maker_validation_service ) {

		$this->attachment_service              = $attachment_service;
		$this->recipe_maker_validation_service = $recipe_maker_validation_service;
		$this->merge_recipe_jsonld();
	}

	private function merge_recipe_jsonld() {
		// First we push all the linked recipes to references.
		add_filter( 'wl_entity_jsonld_array', array( $this, 'wl_entity_jsonld_array' ), 10, 2 );
		add_filter( 'wl_post_jsonld_array', array( $this, 'wl_entity_jsonld_array' ), 10, 2 );

		// Then we merge the jsonld for every recipe.
		add_filter( 'wl_entity_jsonld', array( $this, 'wl_entity_jsonld' ), 10, 2 );
	}

	public function wl_entity_jsonld_array( $arr, $post_id ) {

		$jsonld     = $arr['jsonld'];
		$references = $arr['references'];

		// check if wp recipe maker installed, if not return early.
		if ( ! $this->recipe_maker_validation_service->is_wp_recipe_maker_available() ) {
			return $arr;
		}

		// 1. Get the jsonld from recipe maker for the post id.
		$recipe_ids = \WPRM_Recipe_Manager::get_recipe_ids_from_post( $post_id );

		// If there are no associated recipes for a post id then return early
		if ( ! $recipe_ids ) {
			return $arr;
		}

		return array(
			'jsonld'     => $jsonld,
			'references' => array_merge( $recipe_ids, $references ),
		);

	}

	public function wl_entity_jsonld( $jsonld, $post_id ) {
		$recipe_data = $this->process_single_recipe_item( $post_id );
		if ( ! $recipe_data ) {
			return $jsonld;
		}

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
		if ( ! $this->recipe_maker_validation_service->is_wp_recipe_maker_available() ) {
			return array();
		}
		$linked_recipe = \WPRM_Recipe_Manager::get_recipe( $linked_recipe_id );
		if ( $linked_recipe ) {
			$metadata = \WPRM_Metadata::get_metadata_details( $linked_recipe );
			return $metadata ? $metadata : array();
		}

		return array();
	}

}
