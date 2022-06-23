<?php

namespace Wordlift\Modules\Food_Kg;

class Main_Ingredient_Recipe_Lift_Strategy implements Recipe_Lift_Strategy {

	/**
	 * @var Ingredients_Client
	 */
	private $ingredients_client;

	/**
	 * @var Notices
	 */
	private $notices;

	function __construct( Ingredients_Client $ingredients_client, Notices $notices ) {
		$this->ingredients_client = $ingredients_client;
		$this->notices            = $notices;
	}

	function run() {
		$this->notices->queue( 'info', __( 'WordLift detected WP Recipe Maker and, it is lifting the ingredients...', 'wordlift' ) );

		$recipes      = get_posts( [ 'post_type' => 'wprm_recipe', 'numberposts' => - 1 ] );
		$count_lifted = 0;
		foreach ( $recipes as $recipe ) {
			// Skip posts with existing data.
			$existing = get_post_meta( $recipe->ID, '_wl_main_ingredient_jsonld', true );
			if ( ! empty( $existing ) ) {
				$count_lifted ++;
				continue;
			}

			$jsonld = $this->ingredients_client->main_ingredient( $recipe->post_title );
			if ( ! empty( $jsonld ) ) {
				add_post_meta( $recipe->ID, '_wl_main_ingredient_jsonld', $jsonld );
				$count_lifted ++;
			} else {
				delete_post_meta( $recipe->ID, '_wl_main_ingredient_jsonld' );
			}
		}

		/**
		 * @@todo add notification that procedure is complete, with information about the number of processed items vs
		 *   total items
		 */
		$count = count( $recipes );
		$this->notices->queue( 'info', sprintf( __( 'WordLift detected WP Recipe Maker and, it lifted %d of %d recipe(s).', 'wordlift' ), $count_lifted, $count ) );
	}
}