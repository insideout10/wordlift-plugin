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
		global $wpdb;
		$query_params = $request->get_query_params();
		$post_type    = $query_params['post_type'];
		$limit        = $query_params['limit'] ? $query_params['limit'] : 10;

		$cursor_args = array(
			'limit'     => $limit,
			'position'  => 0,
			'direction' => Page::FORWARD,
			'sort'      => Page::SORT_ASC,
		);
		if ( isset( $query_params['cursor'] ) && is_string( $query_params['cursor'] ) ) {
			$cursor_args = wp_parse_args( json_decode( base64_decode( $query_params['cursor'] ), true ), $cursor_args );
		}
		$operator       = $cursor_args['direction'] === Page::FORWARD ? '>=' : '<=';
		$sort_direction = $cursor_args['sort'] === Page::SORT_ASC ? 'ASC' : 'DESC';

		$position = $cursor_args['position'];

		// I would need to select all the

		$query = $wpdb->prepare(
			"SELECT e.content_id as id, e.about_jsonld as match_jsonld, parent.post_title as name, p.post_title as recipe_name, e.id AS match_id 
FROM {$wpdb->prefix}posts p 
INNER JOIN {$wpdb->prefix}postmeta pm ON p.ID = pm.post_id AND pm.meta_key = 'wprm_parent_post_id' 
INNER JOIN {$wpdb->prefix}posts parent ON pm.meta_value = parent.ID 
LEFT JOIN {$wpdb->prefix}wl_entities e ON p.ID = e.content_id 
WHERE e.content_type = %d AND p.post_type = %s AND p.ID {$operator} %d AND pm.meta_value IS NOT NULL 
ORDER BY p.ID {$sort_direction} LIMIT %d;",
			Object_Type_Enum::POST,
			$post_type,
			$position,
			$cursor_args['limit']
		);
		error_log( $query );
		$items = array_map(
			function ( $e ) {
				return array_merge(
					Match_Entry::from( $e )->serialize(),
					array(
						'recipe_name'    => $e['recipe_name'],
						'post_permalink' => get_permalink( $e['id'] ),
					)
				);
			},
			$wpdb->get_results(
				$query,
				ARRAY_A
			)
		);

		$page = new Page( $items, $limit, $position );
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
