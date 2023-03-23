<?php

namespace Wordlift\Modules\Food_Kg\Main_Entity;

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;
use Wordlift\Modules\Common\Synchronization\Runner;
use Wordlift\Modules\Food_Kg\Ingredients_Client;

class Food_Kg_Main_Entity_Runner implements Runner {

	/**
	 * @var Ingredients_Client
	 */
	private $ingredients_client;

	public function __construct( Ingredients_Client $ingredients_client ) {
		$this->ingredients_client = $ingredients_client;
	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function run( $last_id ) {
		$recipes = get_posts(
			array(
				'post_type'   => 'wprm_recipe',
				'numberposts' => - 1,
			)
		);
		$count   = count( $recipes );

		foreach ( $recipes as $recipe ) {
			$this->process( $recipe->ID );
		}

		return $count;
	}

	public function process( $post_id ) {

		$content_service = Wordpress_Content_Service::get_instance();
		$content_id      = Wordpress_Content_Id::create_post( $post_id );

		// Skip posts with existing data.
		$existing = $content_service->get_about_jsonld( $content_id );
		if ( ! empty( $existing ) ) {
			return true;
		}

		$post = get_post( $post_id );

		$jsonld          = $this->ingredients_client->main_ingredient( $post->post_title );
		$content_service = Wordpress_Content_Service::get_instance();
		$content_id      = Wordpress_Content_Id::create_post( $post_id );

		if ( $this->validate( $jsonld ) ) {
			$content_service->set_about_jsonld( $content_id, $jsonld );

			return true;
		} else {
			// No ingredient found.
			$content_service->set_about_jsonld( $content_id, null );

			return false;
		}

	}

	private function validate( $jsonld_string ) {

		try {
			$json = json_decode( $jsonld_string );
			if ( ! isset( $json->{'@type'} ) || ! isset( $json->name ) ) {
				return false;
			}
		} catch ( \Exception $e ) {
			return false;
		}

		return true;
	}

	public function get_total() {
		$recipes = get_posts(
			array(
				'post_type'   => 'wprm_recipe',
				'numberposts' => - 1,
			)
		);

		return count( $recipes );
	}

}
