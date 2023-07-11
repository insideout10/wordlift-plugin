<?php

namespace Wordlift\Modules\Dashboard\Post_Entity_Match;

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;
use Wordlift\Entity\Entity_Uri_Generator;
use Wordlift\Modules\Common\Api\Cursor;
use Wordlift\Modules\Common\Api\Cursor_Page;
use Wordlift\Modules\Dashboard\Match\Match_Entry;
use Wordlift\Object_Type_Enum;

/**
 * Class Post_Entity_Match_Rest_Controller
 *
 * @package Wordlift\Modules\Dashboard\Post_Entity_Match
 */
class Post_Entity_Match_Rest_Controller extends \WP_REST_Controller {

	/**
	 * @var Post_Entity_Match_Service
	 */
	private $match_service;

	/**
	 * Construct
	 *
	 * @param Post_Entity_Match_Service $match_service
	 */
	public function __construct( $match_service ) {
		$this->match_service = $match_service;
	}

	/**
	 * Register hooks.
	 */
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
					'cursor'      => array(
						'type'              => 'string',
						'default'           => Cursor::EMPTY_CURSOR_AS_BASE64_STRING,
						'validate_callback' => 'rest_validate_request_arg',
						'sanitize_callback' => array( Cursor::class, 'rest_sanitize_request_arg' ),
					),
					'limit'       => array(
						'type'              => 'integer',
						'validate_callback' => 'rest_validate_request_arg',
						'default'           => 10,
						'minimum'           => 1,
						'maximum'           => 100,
						'sanitize_callback' => 'absint',
					),
					'post_types'  => array(
						'type'              => 'array',
						'validate_callback' => 'rest_validate_request_arg',
					),
					'has_match'   => array(
						'type'              => 'boolean',
						'required'          => false,
						'validate_callback' => 'rest_validate_request_arg',
					),
					'post_status' => array(
						'type'              => 'string',
						'required'          => false,
						'enum'              => array( 'publish', 'draft' ),
						'validate_callback' => 'rest_validate_request_arg',
					),
					'sort'        => array(
						'type'              => 'string',
						'required'          => false,
						'enum'              => array( '+date_modified_gmt', '-date_modified_gmt' ),
						'validate_callback' => 'rest_validate_request_arg',
						'default'           => '+date_modified_gmt',
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
		if ( $request->has_param( 'post_types' ) ) {
			$cursor['query']['post_types'] = $request->get_param( 'post_types' );
		}
		if ( $request->has_param( 'has_match' ) ) {
			$cursor['query']['has_match'] = $request->get_param( 'has_match' );
		}
		if ( $request->has_param( 'post_status' ) ) {
			$cursor['query']['post_status'] = $request->get_param( 'post_status' );
		}

		// Query.
		$post_types = isset( $cursor['query']['post_types'] ) ? $cursor['query']['post_types'] : apply_filters(
			'wl_dashboard__post_entity_match__post_types',
			array(
				'post',
				'page',
			)
		);
		$has_match  = isset( $cursor['query']['has_match'] ) ? $cursor['query']['has_match'] : null;

		$post_status = isset( $cursor['query']['post_status'] ) ? $cursor['query']['post_status'] : null;

		$items = $this->match_service->list_items(
			array(
				// Query
				'post_types'  => $post_types,
				'has_match'   => $has_match,
				'post_status' => $post_status,
				// Cursor-Pagination
				'position'    => $cursor['position'],
				'element'     => $cursor['element'],
				'direction'   => $cursor['direction'],
				// `+1` to check if we have other results.
				'limit'       => $cursor['limit'] + 1,
				'sort'        => $cursor['sort'],
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

	/**
	 * Create a new match for a post.
	 *
	 * @param  $request \WP_REST_Request
	 *
	 * @throws \Exception
	 */
	public function create_post_match( $request ) {
		$post_id = $request->get_param( 'post_id' );

		// If we dont have a entry on the match table, then add one.
		$content_id = Wordpress_Content_Id::create_post( $post_id );
		if ( ! Wordpress_Content_Service::get_instance()->get_entity_id( $content_id ) ) {
			$uri = Entity_Uri_Generator::create_uri( $content_id->get_type(), $content_id->get_id() );
			Wordpress_Content_Service::get_instance()->set_entity_id( $content_id, $uri );
		}

		$match_id = $this->match_service->get_id(
			$post_id,
			Object_Type_Enum::POST
		);

		return $this->match_service->set_jsonld(
			$post_id,
			Object_Type_Enum::POST,
			$match_id,
			$request->get_json_params()
		);
	}

	/**
	 * Update post match.
	 *
	 * @param  $request \WP_REST_Request
	 *
	 * @return Match_Entry
	 *
	 * @throws \Exception
	 */
	public function update_post_match( $request ) {

		return $this->match_service->set_jsonld(
			$request->get_param( 'post_id' ),
			Object_Type_Enum::POST,
			$request->get_param( 'match_id' ),
			$request->get_json_params()
		);
	}
}
