<?php

namespace Wordlift\Modules\Food_Kg\Term_Entity;

use Wordlift\Modules\Common\Synchronization\Runner;
use Wordlift\Modules\Food_Kg\Ingredients_Client;

class Food_Kg_Term_Entity_Runner implements Runner {

	/**
	 * @var Ingredients_Client
	 */
	private $ingredients_client;

	public function __construct( Ingredients_Client $ingredients_client ) {
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
			update_term_meta( $term->term_id, '_wl_jsonld', wp_slash( $value ) );

			/**
			 * @@todo update notification with progress
			 */
		}

		return count( $terms );
	}

	/**
	 * Get the total number of posts to process.
	 *
	 * We only count published posts.
	 *
	 * @return int
	 */
	public function get_total() {
		$terms = get_terms(
			array(
				'taxonomy'   => 'wprm_ingredient',
				'fields'     => 'names',
				'hide_empty' => false,
			)
		);

		return count( $terms );
	}

}
