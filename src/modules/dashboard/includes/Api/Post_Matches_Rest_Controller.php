<?php

namespace Wordlift\Modules\Dashboard\Api;

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;
use Wordlift\Entity\Entity_Uri_Generator;
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

		$query_params = $request->get_query_params();

		$cursor_args = array(
			'limit'     => $query_params['limit'] ? $query_params['limit'] : 10,
			'position'  => 0,
			'direction' => Page::FORWARD,
			'sort'      => Page::SORT_ASC,
		);
		if ( isset( $query_params['cursor'] ) && is_string( $query_params['cursor'] ) ) {
			$cursor_args = wp_parse_args( json_decode( base64_decode( $query_params['cursor'] ), true ), $cursor_args );
		}

		$items = $this->match_service->get_post_matches(
			$query_params['post_type'],
			$cursor_args['position'],
			$cursor_args['limit'],
			$cursor_args['direction'],
			$cursor_args['sort']
		);

		$page = new Page( $items, $cursor_args['limit'], $cursor_args['position'] );
		return $page->serialize();
	}

	 /**
	  * Create a new match for a post.
	  *
	  * @var $request \WP_REST_Request
	  */
	public function create_post_match( $request ) {
		$post_id = $request->get_param( 'post_id' );

		// If we dont have a entry on the match table, then add one.
		$content_id = Wordpress_Content_Id::create_post( $post_id );
		if ( ! Wordpress_Content_Service::get_instance()
										->get_entity_id( $content_id ) ) {
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
