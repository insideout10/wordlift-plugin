<?php

namespace Wordlift\Modules\Food_Kg;

class Ingredients_API {

	public function register_hooks() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function register_routes() {
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/ingredients',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_ingredients' ),
				// 'permission_callback' => function () {
				// 	return current_user_can( 'manage_options' );
				// },
				'permission_callback' => '__return_true',
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

	public function get_ingredients( \WP_REST_Request $request ) {
		$per_page = $request['per_page'];
		$page     = $request['page'];
		$offset   = $request['offset'];

		global $wpdb;

		if ( isset( $offset ) ) {
			$offset = (int) $offset;
		} else {
			$offset = ( $page - 1 ) * $per_page;
		}

		$ingredients = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT p1.ID AS recipe_ID,
						p1.post_title AS recipe_name,
						p2.ID AS post_ID,
						p2.post_title
						FROM $wpdb->postmeta pm1
							INNER JOIN $wpdb->posts p1
								ON p1.ID = pm1.post_ID AND p1.post_type = 'wprm_recipe'
							INNER JOIN $wpdb->postmeta pm2
								ON pm2.post_ID = pm1.post_ID AND pm2.meta_key = 'wprm_parent_post_id'
							INNER JOIN $wpdb->posts p2"
				// The following ignore rule is used against the `LIKE CONCAT`. We only have const values.
				// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.LikeWildcardsInQuery
				. " ON p2.post_status = 'publish' AND p2.ID = pm2.meta_value
							WHERE pm1.meta_key = '_wl_main_ingredient_jsonld'
					LIMIT %d
					OFFSET %d",
				$per_page,
				$offset
			)
		);

		if ( empty( $ingredients ) ) {
			return new \WP_Error( 'no_ingredients', __( 'No ingredients found.', 'wordlift' ), array( 'status' => 404 ) );
		}

		$data = array();
		foreach ( $ingredients as $ingredient ) {
			$recipe_json_ld = get_post_meta( $ingredient->recipe_ID, '_wl_main_ingredient_jsonld', true ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$recipe         = json_decode( $recipe_json_ld, true );

			// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$data[] = array(
				'main_ingredient_item_id' => $recipe ? $recipe['@id'] : null,
				'main_ingredient_name'    => $recipe ? $recipe['name'] : null,
				'recipe_id'               => $ingredient->recipe_ID,
				'recipe_name'             => $ingredient->recipe_name,
				'post_id'                 => $ingredient->post_ID,
				'post_name'               => $ingredient->post_title,
				'post_url'                => get_the_permalink( $ingredient->post_ID ),
			);
			// phpcs:enable
		}

		return rest_ensure_response( $data );
	}
}
