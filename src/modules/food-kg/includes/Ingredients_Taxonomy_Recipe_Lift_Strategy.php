<?php

namespace Wordlift\Modules\Food_Kg;

use Wordlift\Content\Content_Service;
use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use WP_Term;

class Ingredients_Taxonomy_Recipe_Lift_Strategy implements Recipe_Lift_Strategy {

	/**
	 * @var Ingredients_Client
	 */
	private $ingredients_client;

	/**
	 * @var Notices
	 */
	private $notices;

	/**
	 * @var Content_Service
	 */
	private $content_service;

	public function __construct( Ingredients_Client $ingredients_client, Notices $notices, Content_Service $content_service ) {
		$this->ingredients_client = $ingredients_client;
		$this->notices            = $notices;
		$this->content_service    = $content_service;
	}

	public function run() {
		$this->notices->queue( 'info', __( 'WordLift detected WP Recipe Maker and, it is lifting the ingredients...', 'wordlift' ) );

		/**
		 * @var WP_Term[] $terms
		 */
		$terms                = get_terms(
			array(
				'taxonomy'   => 'wprm_ingredient',
				'fields'     => 'all',
				'hide_empty' => false,
			)
		);
		$filtered_terms       = $this->terms_without_about_jsonld( $terms );
		$filtered_terms_names = array_map( array( $this, 'term_name' ), $filtered_terms );
		$ingredients          = $this->ingredients_client->ingredients( $filtered_terms_names );

		foreach ( $ingredients as $key => $value ) {
			$term = get_term_by( 'name', $key, 'wprm_ingredient' );
			if ( ! isset( $term ) ) {
				continue;
			}

			$this->content_service->set_about_jsonld(
				Wordpress_Content_Id::create_term( $term->term_id ),
				$value
			);

			/**
			 * @@todo update notification with progress
			 */
		}

		// Clean up caches.
		do_action( 'wl_ttl_cache_cleaner__flush' );

		/**
		 * @@todo add notification that procedure is complete, with information about the number of processed items vs
		 *   total items
		 */
		$count_terms        = count( $filtered_terms );
		$count_lifted_terms = count( $ingredients );
		/* translators: 1: The number of lifted ingredients, 2: The total number of ingredients. */
		$this->notices->queue( 'info', sprintf( __( 'WordLift detected WP Recipe Maker and, it lifted %1$d of %2$d ingredient(s).', 'wordlift' ), $count_lifted_terms, $count_terms ) );
	}

	/**
	 * @param $terms WP_Term[] An array of terms.
	 *
	 * @return WP_Term[]
	 */
	private function terms_without_about_jsonld( $terms ) {
		return array_filter( $terms, array( $this, 'term_has_not_about_jsonld' ) );
	}

	/**
	 * @param $term WP_Term
	 *
	 * @return bool true if the term has an about_jsonld otherwise false.
	 */
	private function term_has_not_about_jsonld( $term ) {
		$about_jsonld = $this->content_service->get_about_jsonld(
			Wordpress_Content_Id::create_term( $term->term_id )
		);

		return $about_jsonld === null;
	}

	/**
	 * @param $term WP_Term
	 *
	 * @return string
	 */
	public function term_name( $term ) {
		return $term->name;
	}
}
