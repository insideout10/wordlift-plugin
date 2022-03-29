<?php

namespace Wordlift\Recipe_Maker;

class Recipe_Maker {

	public function register_hooks() {

		add_filter( 'wprm_recipe_metadata', array( $this, 'recipe_metadata' ), 10, 2 );

	}

	public function recipe_metadata( $metadata, $recipe ) {
		add_filter( 'wl_jsonld_enabled', '__return_false' );

		$this->add_mentions( $metadata );

		return $metadata;
	}

	private function add_mentions( &$metadata ) {
		$metadata['Hello'] = 'World!';
	}

}
