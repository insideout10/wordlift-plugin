<?php

namespace Wordlift\Modules\Dashboard\Api;

use Wordlift\Object_Type_Enum;

class Term_Matches_Rest_Controller extends \WP_REST_Controller {

	public function register() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {

		// Get term matches by taxonomy name
		register_rest_route(
			'wordlift/v1',
			'/term-matches',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_term_matches' ),
				'args'                => array(
					'taxonomy' => array(
						'required'          => true,
						'validate_callback' => 'rest_validate_request_arg',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'cursor'   => array(
						'type'              => 'string',
						'validate_callback' => 'rest_validate_request_arg',
					),
					'limit'    => array(
						'type'              => 'integer',
						'validate_callback' => 'rest_validate_request_arg',
						'default'           => 20,
						'minimum'           => 1,
						'maximum'           => 100,
						'sanitize_callback' => 'absint',
					),
				),

				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);

		// Create a new match for a term
		register_rest_route(
			'wordlift/v1',
			'/term-matches/(?P<term_id>\d+)/matches',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'create_term_match' ),
				'args'                => array(
					'term_id' => array(
						'required'          => true,
						'validate_callback' => 'rest_validate_request_arg',
					),
				),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);

		// Update an existing term match
		register_rest_route(
			'wordlift/v1',
			'/term-matches/(?P<term_id>\d+)/matches/(?P<match_id>\d+)',
			array(
				'methods'             => 'PUT',
				'callback'            => array( $this, 'update_term_match' ),
				'args'                => array(
					'term_id'  => array(
						'required'          => true,
						'validate_callback' => 'rest_validate_request_arg',
					),
					'match_id' => array(
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
	 * Get the term matches by taxonomy name.
	 *
	 * @var $request \WP_REST_Request
	 */
	public function get_term_matches( $request ) {
		global $wpdb;
		$query_params = $request->get_query_params();
		$taxonomy     = $query_params['taxonomy'];
		$limit        = $query_params['limit'] ?: 10;

		$cursor_args = array(
			'limit'     => $limit,
			'position'  => 0,
			'direction' => 'forward',
		);
		if ( isset( $query_params['cursor'] ) && is_string( $query_params['cursor'] ) ) {
			$cursor_args = wp_parse_args( json_decode( base64_decode( $query_params['cursor'] ), true ), $cursor_args );
		}
		$operator = $cursor_args['direction'] === 'forward' ? '>' : '<';

		$query = "SELECT e.content_id as match_id, t.name,  e.id FROM {$wpdb->prefix}wl_entities e
                  LEFT JOIN {$wpdb->prefix}terms t ON e.content_id = t.term_id
                  INNER JOIN {$wpdb->prefix}term_taxonomy tt ON t.term_id = tt.term_id
                  WHERE e.content_type = %d AND tt.taxonomy = %s AND e.id {$operator} %d LIMIT %d";

		$items = $wpdb->get_results(
			$wpdb->prepare(
				$query,
				Object_Type_Enum::TERM,
				$taxonomy,
				$cursor_args['position'],
				$cursor_args['limit']
			)
		);

		return array(
			'first'     => $this->cursor( $limit, 0, 'forwards' ),
			'last'      => $this->cursor( $limit, PHP_INT_MAX, 'backwards' ),

			// 'next'  => $this->next($items, $limit, $cursor_args['position']),
			// 'prev'  => $this->prev($items, $limit, $cursor_args['position']),

				'items' => $items,
		);

	}

	private function next( $items, $limit ) {

	}

	private function prev( $items, $limit, $position ) {
		/**
		 * If i want to go to previous page i would need to be sure that such page exists.
		 * I would just need to reverse the direction.
		 */
		// return $this->cursor( $limit,)
	}

	private function cursor( $limit, $position, $direction ) {
		return base64_encode(
			json_encode(
				array(
					'limit'     => $limit,
					'position'  => $position,
					'direction' => $direction,
				)
			)
		);
	}
	//
	// **
	// * Create a new match for a term.
	// */
	// public function create_term_match( $request ) {
	// Implement the function here
	// }
	//
	// **
	// * Update an existing term match.
	// */
	// public function update_term_match( $request ) {
	// Implement the function here
	// }

	// **
	// * Retrieves the term match schema, conforming to JSON Schema.
	// *
	// * @return array
	// */
	// public function get_item_schema() {
	// return array(
	// '$schema'    => 'http://json-schema.org/draft-04/schema#',
	// 'title'      => 'term-match',
	// 'type'       => 'object',
	// 'properties' => array(
	// 'id'           => array(
	// 'description' => __( 'Unique identifier for the term match.' ),
	// 'type'        => 'integer',
	// 'readonly'    => true,
	// ),
	// 'name'         => array(
	// 'description' => __( 'The term name.' ),
	// 'type'        => 'string',
	// 'required'    => true,
	// ),
	// 'match_id'     => array(
	// 'description' => __( 'Unique identifier for the matched term.' ),
	// 'type'        => 'integer',
	// 'required'    => true,
	// ),
	// 'match_name'   => array(
	// 'description' => __( 'The name of the matched term.' ),
	// 'type'        => 'string',
	// 'required'    => true,
	// ),
	// 'match_jsonld' => array(
	// 'description' => __( 'The JSON-LD representation of the matched term.' ),
	// 'type'        => 'object',
	// 'properties'  => array(
	// '@context' => array(
	// 'description' => __( 'The context for the JSON-LD data.' ),
	// 'type'        => 'string',
	// 'required'    => true,
	// ),
	// '@type'    => array(
	// 'description' => __( 'The type of the JSON-LD data.' ),
	// 'type'        => 'string',
	// 'required'    => true,
	// ),
	// 'name'     => array(
	// 'description' => __( 'The name of the matched term.' ),
	// 'type'        => 'string',
	// 'required'    => true,
	// ),
	// ),
	// ),
	// ),
	// );
	// }

}
