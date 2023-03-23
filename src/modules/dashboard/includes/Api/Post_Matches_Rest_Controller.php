<?php

namespace Wordlift\Modules\Dashboard\Api;

use Wordlift\Object_Type_Enum;

class Post_Matches_Rest_Controller extends \WP_REST_Controller {

	public function register() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {

		// Get post matches by taxonomy name
		register_rest_route(
			'wordlift/v1',
			'/post-matches',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_post_matches' ),
				'args'                => array(
					'post_type' => array(
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

		// Create a new match for a post
		register_rest_route(
			'wordlift/v1',
			'/post-matches/(?P<post_id>\d+)/matches',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'create_post_match' ),
				'args'                => array(
					'post_id' => array(
						'required'          => true,
						'validate_callback' => 'rest_validate_request_arg',
					),
				),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);

		// Update an existing post match
		register_rest_route(
			'wordlift/v1',
			'/post-matches/(?P<post_id>\d+)/matches/(?P<match_id>\d+)',
			array(
				'methods'             => 'PUT',
				'callback'            => array( $this, 'update_post_match' ),
				'args'                => array(
					'post_id'  => array(
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
	 * Get the post matches by taxonomy name.
	 *
	 * @var $request \WP_REST_Request
	 */
	public function get_post_matches( $request ) {
		global $wpdb;
		$query_params = $request->get_query_params();
		$post_type     = $query_params['post_type'];
		$limit        = $query_params['limit'] ? $query_params['limit'] : 10;

		$cursor_args = array(
			'limit'     => $limit,
			'position'  => 0,
			'direction' => 'forward',
		);
		if ( isset( $query_params['cursor'] ) && is_string( $query_params['cursor'] ) ) {
			$cursor_args = wp_parse_args( json_decode( base64_decode( $query_params['cursor'] ), true ), $cursor_args );
		}
		$operator = $cursor_args['direction'] === 'forward' ? '>' : '<';

		$position = $cursor_args['position'];
		$items    = $this->format(
			$wpdb->get_results(
				$wpdb->prepare(
					"SELECT e.content_id as id, e.about_jsonld as match_jsonld,  p.post_title,  e.id AS match_id FROM {$wpdb->prefix}wl_entities e
                  LEFT JOIN {$wpdb->prefix}posts p ON e.content_id = p.ID
                  WHERE e.content_type = %d AND p.post_type = %s AND e.id {$operator} %d LIMIT %d",
					Object_Type_Enum::POST,
					$post_type,
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
	  * Create a new match for a post.
	  *
	  * @var $request \WP_REST_Request
	  */
	public function create_post_match( $request ) {
		global $wpdb;

		// since we dont have the match_id, we would need to get the match_id by querying the post_id
		$match_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM {$wpdb->prefix}wl_entities WHERE content_id = %d AND content_type = %d",
				$request->get_param( 'post_id' ),
				Object_Type_Enum::POST
			)
		);



		if ( ! $match_id ) {
			return new \WP_REST_Response(
				array(
					'code'    => 'error',
					'message' => __( 'The post_id is not valid.', 'wordlift' ),
				),
				400
			);
		}
		return $this->set_jsonld_from_match_id( $request->get_json_params(), $match_id );

	}

	 /**
	  * @var $request \WP_REST_Request
	  */
	public function update_post_match( $request ) {
		return $this->set_jsonld_from_match_id(
			$request->get_json_params(),
			$request->get_param( 'match_id' )
		);
	}

	private function format( $rows ) {
		return array_map(
			array( $this, 'set_name' ),
			$rows
		);
	}

	/**
	 * @param \wpdb    $wpdb
	 * @param array    $jsonld
	 * @param $match_id
	 *
	 * @return array|object|\stdClass|null
	 */
	public function set_jsonld_from_match_id( $jsonld, $match_id ) {
		global $wpdb;
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->prefix}wl_entities SET about_jsonld = %s WHERE id = %d",
				wp_json_encode( $jsonld ),
				$match_id
			)
		);

		$query = "SELECT e.content_id as match_id, e.about_jsonld as match_jsonld,  p.name,  e.id FROM {$wpdb->prefix}wl_entities e
                  LEFT JOIN {$wpdb->prefix}posts p ON e.content_id = p.ID
                  WHERE  e.id = %d";

		return $wpdb->get_row( $wpdb->prepare( $query, $match_id ) );
	}

	private function set_name( $item ) {
		$jsonld             = json_decode( $item['match_jsonld'], true );
		$item['match_name'] = $jsonld != null ? $jsonld['name'] : null;
		return $item;
	}

}
