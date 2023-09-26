<?php

namespace Wordlift\Modules\Food_Kg;

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;

class Main_Ingredient_Recipe_Lift_Strategy implements Recipe_Lift_Strategy {

	/**
	 * @var Ingredients_Client
	 */
	private $ingredients_client;

	/**
	 * @var Notices
	 */
	private $notices;

	public function __construct( Ingredients_Client $ingredients_client, Notices $notices ) {
		$this->ingredients_client = $ingredients_client;
		$this->notices            = $notices;
	}

	public function get_json_ld_data( $ingredient ) {
		// Get JSON LD Data.
		return $this->ingredients_client->main_ingredient( $ingredient );
	}

	public function run() {
		$this->notices->queue( 'info', __( 'WordLift detected WP Recipe Maker and, it is lifting the ingredients...', 'wordlift' ) );

		$recipes = get_posts(
			array(
				'post_type'   => 'wprm_recipe',
				'numberposts' => - 1,
			)
		);
		$count   = count( $recipes );

		$count_lifted = 0;
		foreach ( $recipes as $recipe ) {
			/* translators: 1: The number of lifted recipes, 2: The total number of recipes. */
			$this->notices->queue( 'info', sprintf( __( 'WordLift is adding the main ingredient to recipes. So far it lifted %1$d of %2$d recipe(s).', 'wordlift' ), $count_lifted, $count ) );

			// Emit something to keep the connection alive.
			echo esc_html( "$count_lifted\n" );

			if ( $this->process( $recipe->ID ) ) {
				$count_lifted ++;
			}
		}

		/**
		 * @@todo add notification that procedure is complete, with information about the number of processed items vs
		 *   total items
		 */
		/* translators: 1: The number of lifted recipes, 2: The total number of recipes. */
		$this->notices->queue( 'info', sprintf( __( 'WordLift lifted %1$d of %2$d recipe(s).', 'wordlift' ), $count_lifted, $count ) );
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
}

