<?php

namespace Wordlift\Modules\Dashboard\Api;

use Wordlift\Content\Wordpress\Wordpress_Content_Service;
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

		$query = "SELECT e.content_id as id, e.about_jsonld as match_jsonld,  t.name,  e.id AS match_id FROM {$wpdb->prefix}wl_entities e
                  LEFT JOIN {$wpdb->prefix}terms t ON e.content_id = t.term_id
                  INNER JOIN {$wpdb->prefix}term_taxonomy tt ON t.term_id = tt.term_id
                  WHERE e.content_type = %d AND tt.taxonomy = %s AND e.id {$operator} %d LIMIT %d";

		$position = $cursor_args['position'];
		$items    = $this->format(
			$wpdb->get_results(
				$wpdb->prepare(
					$query,
					Object_Type_Enum::TERM,
					$taxonomy,
					$position,
					$cursor_args['limit']
				),
				ARRAY_A
			)
		);

		return array(
			'first' => 0 === $position ? null : $this->cursor( $limit, 0, 'forwards' ),
			'last'  => PHP_INT_MAX === $position ? null : $this->cursor( $limit, PHP_INT_MAX, 'backwards' ),
			'next'  => $this->next( $items, $limit, $position ),
			'prev'  => $this->prev( $items, $limit, $position ),
			'items' => $items,
		);

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

	private function next( $items, $limit, $position ) {
		// Check if we have reached the end of the results
		if ( count( $items ) < $limit ) {
			return null;
		}

		// Get the position of the last item in the current result set
		$last_item_position = end( $items )['id'];

		// Generate the next cursor
		return $this->cursor( $limit, $last_item_position, 'forward' );
	}

	private function prev( $items, $limit, $position ) {
		/**
		 * If i want to go to previous page i would need to be sure that such page exists.
		 * I would just need to reverse the direction.
		 */
		if ( $position === 0 ) {
			return null;
		}

		if ( count( $items ) <= 0 ) {
			return $this->cursor( $limit, $position, 'backward' );
		}

		return $this->cursor( current( $items )['id'], $position, 'forward' );
	}



	 /**
	 * Create a new match for a term.
	  * @var $request \WP_REST_Request
	  */
	public function create_term_match( $request ) {
		global $wpdb;
		$body = $request->get_json_params();
		// since we dont have the match_id, we would need to get the match_id by querying the term_id
		$match_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM {$wpdb->prefix}_wl_entities WHERE content_id = %d",
				$request->get_param( 'term_id' )
			)
		);
		if ( ! $match_id ) {
			return new \WP_REST_Response(
				array(
					'code'    => 'error',
					'message' => __( 'The term_id is not valid.', 'wordlift' ),
				),
				400
			);
		}

		return $this->set_jsonld_from_match_id( $wpdb, $body, $match_id );

	}

	 /**
	  * @var $request \WP_REST_Request
	 */
	 public function update_term_match( $request ) {
		return $this->set_jsonld_from_match_id(
			$request->get_json_params(),
			$request->get_param('match_id')
		);
	 }

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
	private function format( $rows ) {
		return array_map(
			array( $this, 'set_name' ),
			$rows
		);
	}

	/**
	 * @param \wpdb $wpdb
	 * @param array $jsonld
	 * @param $match_id
	 *
	 * @return array|object|\stdClass|null
	 */
	public function set_jsonld_from_match_id(  $jsonld, $match_id ) {
		global $wpdb;
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->prefix}_wl_entities SET about_jsonld = %s WHERE id = %d",
				wp_json_encode( $jsonld ),
				$match_id
			)
		);

		$query = "SELECT e.content_id as match_id, e.about_jsonld as match_jsonld,  t.name,  e.id FROM {$wpdb->prefix}wl_entities e
                  LEFT JOIN {$wpdb->prefix}terms t ON e.content_id = t.term_id
                  WHERE  AND e.id = %d";

		return $wpdb->get_row( $wpdb->prepare( $query, $match_id ) );
	}

	private function set_name( $item ) {
		$jsonld             = json_decode( $item['match_jsonld'], true );
		$item['match_name'] = $jsonld != null ? $jsonld['name'] : null;
		return $item;
	}

}
