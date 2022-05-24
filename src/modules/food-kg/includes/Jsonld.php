<?php

namespace Wordlift\Modules\Food_Kg;

use WPRM_Recipe;

class Jsonld {

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

		$jsonlds = array_filter( array_map( [ $this, '__term_id_to_jsonld' ], $recipe->ingredients_flat() ) );

		if ( empty( $jsonlds ) ) {
			return $metadata;
		}

		$metadata['mentions'] = $metadata['mentions'] ?: [];
		$metadata['mentions'] = array_merge( $metadata['mentions'], array_map( [ $this, '__make_id' ], $jsonlds ) );

		return $metadata;
	}

	/**
	 * @param array{'id': int} $ingredient
	 *
	 * @return string void
	 */
	private function __term_id_to_jsonld( $ingredient ) {
		return json_decode( get_term_meta( $ingredient['id'], '_wl_jsonld', true ) );
	}

	/**
	 * @param object{'@id': string} $jsonld
	 *
	 * @return array{'@id': string}
	 */
	private function __make_id( $jsonld ) {
		return [ '@id' => $jsonld->{'@id'} ];
	}

}
