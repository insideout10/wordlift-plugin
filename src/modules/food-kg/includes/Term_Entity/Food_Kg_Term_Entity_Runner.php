<?php

namespace Wordlift\Modules\Food_Kg\Term_Entity;

use Wordlift\Content\Content_Service;
use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Modules\Common\Synchronization\Runner;
use Wordlift\Modules\Food_Kg\Ingredients_Client;
use WP_Term;

class Food_Kg_Term_Entity_Runner implements Runner {

	/**
	 * @var Food_Kg_Ingredients_Term_Store
	 */
	private $store;

	/**
	 * @var Content_Service
	 */
	private $content_service;

	/**
	 * @var Ingredients_Client
	 */
	private $ingredients_client;

	public function __construct( Food_Kg_Ingredients_Term_Store $store, Content_Service $content_service, Ingredients_Client $ingredients_client ) {
		$this->store              = $store;
		$this->content_service    = $content_service;
		$this->ingredients_client = $ingredients_client;
	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function run( $last_id ) {

		$items = $this->store->list_items( $last_id, 100 );
		$names = array_column(
			array_map(
				function ( $item ) {
					return (array) $item;
				},
				$items
			),
			'name'
		);

		$ingredients = $this->ingredients_client->ingredients( $names );

		foreach ( $ingredients as $key => $value ) {
			$term = get_term_by( 'name', $key, 'wprm_ingredient' );
			if ( ! isset( $term ) || $this->term_has_about_jsonld( $term ) ) {
				continue;
			}

			$content_id = Wordpress_Content_Id::create_term( $term->term_id );
			$this->content_service->set_about_jsonld( $content_id, $value );
		}

		$count        = count( $items );
		$last_item    = end( $items );
		$last_item_id = ( isset( $last_item->term_id ) ? $last_item->term_id : null );

		return array( $count, $last_item_id );
	}

	/**
	 * Get the total number of posts to process.
	 *
	 * We only count published posts.
	 *
	 * @return int
	 */
	public function get_total() {
		return $this->store->get_total();
	}

	/**
	 * @param $term WP_Term
	 *
	 * @return bool true if the term has an about_jsonld otherwise false.
	 */
	private function term_has_about_jsonld( $term ) {
		$about_jsonld = $this->content_service->get_about_jsonld(
			Wordpress_Content_Id::create_term( $term->term_id )
		);

		return $about_jsonld !== null;
	}

}
