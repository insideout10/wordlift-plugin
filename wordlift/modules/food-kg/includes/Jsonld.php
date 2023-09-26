<?php

namespace Wordlift\Modules\Food_Kg;

use Wordlift\Content\Content_Service;
use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use WP_Term;
use WPRM_Recipe;

class Jsonld {

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

		$ingredients = get_the_terms( $recipe->id(), 'wprm_ingredient' );
		if ( ! $ingredients ) {
			return $metadata;
		}
		$jsonlds = array_filter( array_map( array( $this, '__term_id_to_jsonld' ), $ingredients ) );

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
	 * @return array|null
	 */
	private function __term_id_to_jsonld( $ingredient ) {
		$content_id = Wordpress_Content_Id::create_term( $ingredient->term_id );
		$jsonld     = $this->content_service->get_about_jsonld( $content_id );

		if ( ! is_string( $jsonld ) ) {
			return null;
		}

		return json_decode( $jsonld, true );
	}

}
