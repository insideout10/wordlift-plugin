<?php

namespace Wordlift\Modules\Food_Kg;

use WPRM_Recipe;

class Main_Ingredient_Jsonld {

	public function register_hooks() {
		add_action( 'wprm_recipe_metadata', [ $this, '__recipe_metadata' ], 10, 2 );
	}

	/**
	 * @param array $metadata
	 * @param WPRM_Recipe $recipe
	 *
	 * @return array
	 */
	public function __recipe_metadata( $metadata, $recipe ) {

		$jsonld = get_post_meta( $recipe->id(), '_wl_main_ingredient_jsonld', true );

		if ( empty( $jsonld ) ) {
			return $metadata;
		}

		// We're embedding the full json-ld here because WL doesn't output its own markup, so it makes no sense
		// to hook to wl_after_json_ld.
		$metadata['about'] = isset( $metadata['about'] ) ? $metadata['about'] : [];
		$metadata['about'] = array_merge( $metadata['about'], [ json_decode( $jsonld, true ) ] );

		return $metadata;
	}

}
