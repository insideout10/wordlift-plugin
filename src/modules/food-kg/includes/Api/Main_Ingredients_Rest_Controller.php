<?php
namespace Wordlift\Modules\Food_Kg\Api;

use Wordlift\Modules\Food_Kg\Recipe_Lift_Strategy;

class Main_Ingredients_Rest_Controller {

	/**
	 * @var Recipe_Lift_Strategy
	 */
	private $recipe_lift_strategy;

	public function __construct( Recipe_Lift_Strategy $recipe_lift_strategy ) {
		$this->recipe_lift_strategy = $recipe_lift_strategy;
	}

	public function register() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		register_rest_route(
			'wordlift/v1',
			'/main-ingredients',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_main_ingredients' ),
				'args'                => array(
					'query' => array(
						'type'              => 'string',
						'required'          => true,
						'validate_callback' => 'rest_validate_request_arg',
					),
				),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
	}

	/**
	 * @param $request \WP_REST_Request
	 *
	 * @return array
	 */
	public function get_main_ingredients( $request ) {

		$query_params    = $request->get_query_params();
		$ingredient_name = $query_params['query'];
		$jsonld          = $this->recipe_lift_strategy->get_json_ld_data( $ingredient_name );
		$data            = json_decode( $jsonld, true );

		if ( ! $data ) {
			return new \WP_REST_Response( null, 400 );
		}

		return $data;
	}
}
