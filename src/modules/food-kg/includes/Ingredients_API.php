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

		$sql =
			"SELECT p1.ID AS recipe_ID,
					p1.post_title AS recipe_name,
					p2.ID AS post_ID,
					p2.post_title,
					p2.post_status
				FROM {$wpdb->posts} p1
					INNER JOIN {$wpdb->postmeta} pm1 ON pm1.post_ID = p1.ID
						AND pm1.meta_key = '_wl_main_ingredient_jsonld'
					INNER JOIN {$wpdb->posts} p2"
			// The following ignore rule is used against the `LIKE CONCAT`. We only have const values.
			// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.LikeWildcardsInQuery
			. " ON p2.post_content LIKE CONCAT( '%<!--WPRM Recipe ', p1.ID,'-->%' )
				AND p2.post_status = 'publish'
				WHERE p1.post_type = 'wprm_recipe'" . $this->limit( $per_page ) . $this->offset( $offset );

		$ingredients = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		if ( empty( $ingredients ) ) {
			return new \WP_Error( 'no_ingredients', __( 'No ingredients found.', 'wordlift' ), array( 'status' => 404 ) );
		}

		$data = array();
		foreach ( $ingredients as $ingredient ) {
			$recipe_json_ld = get_post_meta( $ingredient->recipe_ID, '_wl_main_ingredient_jsonld', true ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$recipe         = json_decode( $recipe_json_ld, true );

			// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$data[] = array(
				'ingredient_id'   => '', // TODO: get the ingredient id.
				'ingredient_name' => $recipe ? $recipe['name'] : 'null',
				'recipe_id'       => $ingredient->recipe_ID,
				'recipe_name'     => $ingredient->recipe_name,
				'post_id'         => $ingredient->post_ID,
				'post_name'       => $ingredient->post_title,
				'post_url'        => esc_url( get_the_permalink( $ingredient->post_ID ) ),
			);
			// phpcs:enable
		}

		return rest_ensure_response( $data );

	}

	/**
	 * Add the limit clause if specified.
	 *
	 * @param null|int $limit The maximum number of results.
	 *
	 * @return string The limit clause (empty if no limit has been specified).
	 */
	private function limit( $limit = null ) {

		if ( null === $limit || ! is_numeric( $limit ) ) {
			return '';
		}

		return " LIMIT $limit";
	}

	/**
	 * Add the OFFSET clause if specified.
	 *
	 * @param null|int $offset The number of results to skip.
	 *
	 * @return string The offset clause (empty if no offset has been specified).
	 */
	private function offset( $offset = null ) {

		if ( null === $offset || ! is_numeric( $offset ) ) {
			return '';
		}

		return " OFFSET $offset";
	}
}
