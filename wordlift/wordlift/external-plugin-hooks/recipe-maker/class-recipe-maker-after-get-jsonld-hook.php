<?php
/**
 * @since 2.48.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\External_Plugin_Hooks\Recipe_Maker;

/**
 * Hook in to full jsonld structure and alter it when
 * there are recipes referred in the post.
 *
 * Class Recipe_Maker_After_Get_Jsonld_Hook
 *
 * @package Wordlift\External_Plugin_Hooks
 */
class Recipe_Maker_After_Get_Jsonld_Hook {

	/**
	 * @var Recipe_Maker_Validation_Service
	 */
	private $recipe_maker_validation_service;

	public function __construct( $recipe_maker_validation_service ) {
		$this->recipe_maker_validation_service = $recipe_maker_validation_service;
		// Add the filter to alter final jsonld.
		add_filter( 'wl_after_get_jsonld', array( $this, 'wl_after_get_jsonld' ), 10, 2 );
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
	public function wl_after_get_jsonld( $jsonld, $post_id ) {
		if ( ! is_array( $jsonld ) || count( $jsonld ) === 0 ) {
			return $jsonld;
		}
		// If there are no recipes in the post then dont alter the jsonld.
		if ( ! $this->recipe_maker_validation_service->is_atleast_once_recipe_present_in_the_post( $post_id ) ) {
			return $jsonld;
		}

		// We remove the current post jsonld.
		$post_jsonld    = array_shift( $jsonld );
		$post_jsonld_id = array_key_exists( '@id', $post_jsonld ) ? $post_jsonld['@id'] : false;

		if ( ! $post_jsonld_id ) {
			return $jsonld;
		}

		foreach ( $jsonld as $key => $value ) {
			if ( ! array_key_exists( '@type', $value ) ) {
				continue;
			}
			$type = $value['@type'];
			if ( 'Recipe' === $type ) {
				$value['isPartOf'] = array(
					'@id' => $post_jsonld_id,
				);
				$jsonld[ $key ]    = $value;
			}
		}

		// Add back the post jsonld to first of array.
		array_unshift( $jsonld, $post_jsonld );

		return $jsonld;
	}

}
