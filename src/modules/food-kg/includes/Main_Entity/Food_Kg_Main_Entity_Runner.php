<?php

namespace Wordlift\Modules\Food_Kg\Main_Entity;

use Wordlift\Content\Content_Service;
use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Modules\Common\Synchronization\Runner;
use Wordlift\Modules\Common\Synchronization\Store;
use Wordlift\Modules\Food_Kg\Ingredients_Client;

class Food_Kg_Main_Entity_Runner implements Runner {

	/**
	 * @var Content_Service
	 */
	private $content_service;

	/**
	 * @var Store
	 */
	private $store;

	/**
	 * @var Ingredients_Client
	 */
	private $ingredients_client;

	public function __construct( Content_Service $content_service, Ingredients_Client $ingredients_client, Store $store ) {
		$this->store              = $store;
		$this->ingredients_client = $ingredients_client;
		$this->content_service    = $content_service;
	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function run( $last_id ) {
		$items = $this->store->list_items( $last_id, 100 );

		// Count the processed items.
		$count_items = count( $items );

		foreach ( $items as $recipe_id ) {
			$this->process( $recipe_id );
		}

		// Get the last ID.
		$last_id = end( $items );

		// Finally return the count.
		return array( $count_items, $last_id );
	}

	public function process( $post_id ) {

		$content_id = Wordpress_Content_Id::create_post( $post_id );

		// Skip posts with existing data.
		$existing = $this->content_service->get_about_jsonld( $content_id );
		if ( ! empty( $existing ) ) {
			return true;
		}

		$post = get_post( $post_id );

		$jsonld = $this->ingredients_client->main_ingredient( $post->post_title );

		if ( $this->validate( $jsonld ) ) {
			$this->content_service->set_about_jsonld( $content_id, $jsonld );

			return true;
		} else {
			// No ingredient found.
			$this->content_service->set_about_jsonld( $content_id, null );

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
		global $wpdb;

		return $wpdb->get_var(
			"
			SELECT COUNT(1) FROM $wpdb->posts WHERE post_type = 'wprm_recipe'
		"
		);
	}

}
