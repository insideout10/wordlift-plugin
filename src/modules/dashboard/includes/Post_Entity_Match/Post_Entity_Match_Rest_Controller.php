<?php

namespace Wordlift\Modules\Dashboard\Post_Entity_Match;

use Wordlift\Modules\Food_Kg\Api\Cursor;
use Wordlift\Modules\Food_Kg\Api\Cursor_Page;

class Post_Entity_Match_Rest_Controller extends \WP_REST_Controller {

	/**
	 * @var Post_Entity_Match_Service
	 */
	private $match_service;

	public function __construct( $match_service ) {
		$this->match_service = $match_service;
	}

	public function register_hooks() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {

		// Get term matches by taxonomy name
		register_rest_route(
			'wordlift/v1',
			'/post-matches',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_post_matches' ),
				'args'                => array(
					'cursor'    => array(
						'type'              => 'string',
						'default'           => Cursor::EMPTY_CURSOR_AS_BASE64_STRING,
						'validate_callback' => 'rest_validate_request_arg',
						'sanitize_callback' => array( Cursor::class, 'rest_sanitize_request_arg' ),
					),
					'limit'     => array(
						'type'              => 'integer',
						'validate_callback' => 'rest_validate_request_arg',
						'default'           => 20,
						'minimum'           => 1,
						'maximum'           => 100,
						'sanitize_callback' => 'absint',
					),
					'post_type' => array(
						'type'              => 'string',
						'required'          => true,
						'validate_callback' => 'rest_validate_request_arg',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'has_match' => array(
						'type'              => 'boolean',
						'required'          => false,
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
	public function get_post_matches( $request ) {

		$cursor = $request->get_param( 'cursor' );
		if ( $request->has_param( 'limit' ) ) {
			$cursor['limit'] = $request->get_param( 'limit' );
		}
		if ( $request->has_param( 'sort' ) ) {
			$cursor['sort'] = $request->get_param( 'sort' );
		}
		if ( $request->has_param( 'post_type' ) ) {
			$cursor['query']['taxonomy'] = $request->get_param( 'post_type' );
		}
		if ( $request->has_param( 'has_match' ) ) {
			$cursor['query']['has_match'] = $request->get_param( 'has_match' );
		}

		// Query.
		$post_type = isset( $cursor['query']['post_type'] ) ? $cursor['query']['post_type'] : null;
		$has_match = isset( $cursor['query']['has_match'] ) ? $cursor['query']['has_match'] : null;

		$items = $this->match_service->list_items(
			array(
				// Query
				'post_type' => $post_type,
				'has_match' => $has_match,
				// Cursor-Pagination
				'position'  => $cursor['position'],
				'element'   => $cursor['element'],
				'direction' => $cursor['direction'],
				// `+1` to check if we have other results.
				'limit'     => $cursor['limit'] + 1,
				'sort'      => $cursor['sort'],
			)
		);

		return new Cursor_Page(
			$items,
			$cursor['position'],
			$cursor['element'],
			$cursor['direction'],
			$cursor['sort'],
			$cursor['limit'],
			$cursor['query']
		);
	}

}
