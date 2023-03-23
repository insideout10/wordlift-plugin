<?php

namespace Wordlift\Modules\Food_Kg;

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;
use WPRM_Recipe;

class Main_Ingredient_Jsonld {

	public function register_hooks() {
		add_action( 'wprm_recipe_metadata', array( $this, '__recipe_metadata' ), 10, 2 );
	}

	/**
	 * @param array       $metadata
	 * @param WPRM_Recipe $recipe
	 *
	 * @return array
	 */
	public function __recipe_metadata( $metadata, $recipe ) {
		$content_service = Wordpress_Content_Service::get_instance();

		$jsonld = $content_service->get_about_jsonld(
			Wordpress_Content_Id::create_post( $recipe->id() )
		);

		if ( empty( $jsonld ) ) {
			return $metadata;
		}

		// We're embedding the full json-ld here because WL doesn't output its own markup, so it makes no sense
		// to hook to wl_after_json_ld.
		$metadata['about'] = isset( $metadata['about'] ) ? $metadata['about'] : array();
		$metadata['about'] = array_merge( $metadata['about'], array( json_decode( $jsonld, true ) ) );

		return $metadata;
	}

}
