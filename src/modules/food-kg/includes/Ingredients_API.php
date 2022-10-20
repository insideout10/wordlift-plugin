<?php

namespace Wordlift\Modules\Food_Kg;

class Ingredients_API {

	/**
	 * Ingredients Service.
	 *
	 * @var \Wordlift\Modules\Food_Kg\Services\Ingredients $ingredients_service The Ingredients Service.
	 */
	public $ingredients_service;

	public function __construct( $ingredients_service ) {
		$this->ingredients_service = $ingredients_service;
	}

	/**
	 * Register Hooks.
	 */
	public function register_hooks() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register Routes.
	 */
	public function register_routes() {
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/ingredients',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_ingredients' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
				'args'                => array(
					'per_page' => array(
						'type'              => 'integer',
						'validate_callback' => 'rest_validate_request_arg',
						'default'           => 20,
						'minimum'           => 1,
						'maximum'           => 100,
						'sanitize_callback' => 'absint',
					),
					'page'     => array(
						'type'              => 'integer',
						'validate_callback' => 'rest_validate_request_arg',
						'default'           => 1,
						'sanitize_callback' => 'absint',
					),
					'offset'   => array(
						'type'              => 'integer',
						'validate_callback' => 'rest_validate_request_arg',
						'sanitize_callback' => 'absint',
					),
				),
			)
		);
	}

	/**
	 * Get Ingredients Data.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_ingredients( \WP_REST_Request $request ) {
		$per_page = $request['per_page'];
		$page     = $request['page'];
		$offset   = $request['offset'];

		if ( ! isset( $offset ) ) {
			$offset = ( $page - 1 ) * $per_page;
		}

		$data = $this->ingredients_service->get_data( $per_page, $offset );

		return rest_ensure_response( $data );
	}
}
