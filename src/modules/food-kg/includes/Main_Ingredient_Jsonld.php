<?php

namespace Wordlift\Modules\Food_Kg;

use Wordlift\Content\Content_Service;
use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use WPRM_Recipe;

class Main_Ingredient_Jsonld {

	/**
	 * @var Content_Service $content_service
	 */
	private $content_service;

	public function __construct( Content_Service $content_service ) {
		$this->content_service = $content_service;
	}

	public function register_hooks() {
		add_filter( 'wprm_recipe_metadata', array( $this, '__recipe_metadata' ), 10, 2 );
	}

	/**
	 * @param array       $metadata
	 * @param WPRM_Recipe $recipe
	 *
	 * @return array
	 */
	public function __recipe_metadata( $metadata, $recipe ) {
		$jsonld = $this->content_service->get_about_jsonld(
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
