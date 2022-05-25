<?php

namespace Wordlift\Modules\Food_Kg;

use Wordlift\Api\Api_Service_Ext;
use Wordlift\Modules\Food_Kg_Dependencies\Symfony\Component\Config\Definition\Exception\Exception;

class Module {

	const RUN_EVENT = 'wl_food_kg__run';

	/**
	 * @var Api_Service_Ext
	 */
	private $api_service;
	/**
	 * @var Ingredients_Client
	 */
	private $ingredients_client;

	/**
	 * @var Notices
	 */
	private $notices;

	/**
	 * @param Api_Service_Ext $api_service
	 * @param Ingredients_Client $ingredients_client
	 * @param Notices $notices
	 */
	public function __construct( Api_Service_Ext $api_service, Ingredients_Client $ingredients_client, Notices $notices ) {
		$this->api_service        = $api_service;
		$this->ingredients_client = $ingredients_client;
		$this->notices            = $notices;
	}

	public function register_hooks() {
		add_action( 'wl_key_updated', [ $this, '__key_updated' ] );
		add_action( self::RUN_EVENT, [ $this, '__run' ] );
		add_action( 'wp_ajax_wl_food_kg__run', [ $this, '__run' ] );
	}

	public function __key_updated() {
		try {
			$me_response    = $this->api_service->me();
			$has_food_kg    = isset( $me_response->networks )
			                  && array_reduce( $me_response->networks, [ $this, '__has_food_kg' ], false );
			$next_scheduled = wp_next_scheduled( self::RUN_EVENT );

			// We're connected to the Food KG, but not yet scheduled.
			if ( $has_food_kg && ! $next_scheduled ) {
				wp_schedule_event( time(), 'daily', self::RUN_EVENT );
			}

			// We're not connected to the Food KG, but we're scheduled.
			if ( ! $has_food_kg && $next_scheduled ) {
				wp_unschedule_event( $next_scheduled, self::RUN_EVENT );
			}

		} catch ( Exception $e ) {

		}
	}

	public function __run() {

		$this->notices->queue( 'info', __( 'WordLift detected WP Recipe Maker and, it is lifting the ingredients...', 'wordlift' ) );

		/**
		 * @var string[] $terms
		 */
		$terms       = get_terms( [ 'taxonomy' => 'wprm_ingredient', 'fields' => 'names', 'hide_empty' => false ] );
		$ingredients = $this->ingredients_client->ingredients( $terms );

		foreach ( $ingredients as $key => $value ) {
			$term = get_term_by( 'name', $key, 'wprm_ingredient' );
			if ( ! isset( $term ) ) {
				continue;
			}
			update_term_meta( $term->term_id, '_wl_jsonld', $value );

			/**
			 * @@todo update notification with progress
			 */
		}

		/**
		 * @@todo add notification that procedure is complete, with information about the number of processed items vs
		 *   total items
		 */
		$count_terms        = count( $terms );
		$count_lifted_terms = count( $ingredients );
		$this->notices->queue( 'info', sprintf( __( 'WordLift detected WP Recipe Maker and, it lifted %d of %d ingredient(s).', 'wordlift' ), $count_lifted_terms, $count_terms ) );

	}

	private function __has_food_kg( $carry, $item ) {
		return $carry || 'https://knowledge.cafemedia.com/food/' === $item->dataset_uri;
	}

}
