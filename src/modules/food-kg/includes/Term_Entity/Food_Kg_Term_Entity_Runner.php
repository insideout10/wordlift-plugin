<?php

namespace Wordlift\Modules\Food_Kg\Term_Entity;

use Wordlift\Content\Content_Service;
use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Modules\Common\Synchronization\Runner;
use Wordlift\Modules\Food_Kg\Ingredients_Client;

class Food_Kg_Term_Entity_Runner implements Runner {

	/**
	 * @var Content_Service
	 */
	private $content_service;

	/**
	 * @var Ingredients_Client
	 */
	private $ingredients_client;

	public function __construct( Content_Service $content_service, Ingredients_Client $ingredients_client ) {
		$this->content_service    = $content_service;
		$this->ingredients_client = $ingredients_client;
	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function run( $last_id ) {
		/**
		 * @var string[] $terms
		 */
		$terms       = get_terms(
			array(
				'taxonomy'   => 'wprm_ingredient',
				'fields'     => 'names',
				'hide_empty' => false,
			)
		);
		$ingredients = $this->ingredients_client->ingredients( $terms );

		foreach ( $ingredients as $key => $value ) {
			$term = get_term_by( 'name', $key, 'wprm_ingredient' );
			if ( ! isset( $term ) ) {
				continue;
			}

			$content_id = Wordpress_Content_Id::create_term( $term->term_id );
			$this->content_service->set_about_jsonld( $content_id, $value );
		}

		$count = count( $terms );

		return array( $count, null );
	}

	/**
	 * Get the total number of posts to process.
	 *
	 * We only count published posts.
	 *
	 * @return int
	 */
	public function get_total() {
		global $wpdb;

		return $wpdb->get_var(
			"
			SELECT COUNT(1) FROM $wpdb->term_taxonomy where taxonomy = 'wprm_ingredient'
		"
		);
	}

}
