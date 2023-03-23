<?php

namespace Wordlift\Modules\Dashboard\Api;

use Wordlift\Object_Type_Enum;

class Post_Matches_Rest_Controller extends \WP_REST_Controller {
	/**
	 * @var Match_Service
	 */
	private $match_service;

	public function __construct( $match_service ) {
		$this->match_service = $match_service;
	}

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
					'cursor'    => array(
						'type'              => 'string',
						'validate_callback' => 'rest_validate_request_arg',
					),
					'limit'     => array(
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
		$post_type    = $query_params['post_type'];
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
		$items    = array_map(
			function ( $e ) {
				return Match_Entry::from( $e )->serialize();
			},
			$wpdb->get_results(
				$wpdb->prepare(
					"SELECT e.content_id as id, e.about_jsonld as match_jsonld,  p.post_title as name,  e.id AS match_id FROM {$wpdb->prefix}wl_entities e
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

		$match_id = $this->match_service->get_id(
			$request->get_param( 'post_id' ),
			Object_Type_Enum::POST
		);

		return $this->match_service->set_jsonld(
			$request->get_param( 'post_id' ),
			Object_Type_Enum::POST,
			$match_id,
			$request->get_json_params()
		)->serialize();

	}

	 /**
	  * @var $request \WP_REST_Request
	  */
	public function update_post_match( $request ) {

		return $this->match_service->set_jsonld(
			$request->get_param( 'post_id' ),
			Object_Type_Enum::POST,
			$request->get_param( 'match_id' ),
			$request->get_json_params()
		)->serialize();
	}



}
