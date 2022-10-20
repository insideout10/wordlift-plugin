<?php

namespace Wordlift\Modules\Food_Kg;

use WP_Term;
use WPRM_Recipe;

class Jsonld {

	public function register_hooks() {
		add_action( 'wprm_recipe_metadata', array( $this, '__recipe_metadata' ), 10, 2 );
	}

	/**
	 * @param array $metadata
	 * @param WPRM_Recipe $recipe
	 *
	 * @return array
	 */
	public function __recipe_metadata( $metadata, $recipe ) {

		$ingredients = get_the_terms( $recipe->id(), 'wprm_ingredient' );
		$jsonlds     = array_filter( array_map( array( $this, '__term_id_to_jsonld' ), $ingredients ) );

		if ( empty( $jsonlds ) ) {
			return $metadata;
		}

		// We're embedding the full json-ld here because WL doesn't output its own markup, so it makes no sense
		// to hook to wl_after_json_ld.
		$metadata['mentions'] = isset( $metadata['mentions'] ) ? $metadata['mentions'] : array();
		$metadata['mentions'] = array_merge( $metadata['mentions'], $jsonlds );

		return $metadata;
	}

	/**
	 * @param WP_Term $ingredient
	 *
	 * @return array void
	 */
	private function __term_id_to_jsonld( $ingredient ) {
		return json_decode( get_term_meta( $ingredient->term_id, '_wl_jsonld', true ), true );
	}

}
