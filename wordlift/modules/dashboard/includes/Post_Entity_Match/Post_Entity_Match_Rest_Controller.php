<?php

namespace Wordlift\Modules\Dashboard\Post_Entity_Match;

use Exception;
use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;
use Wordlift\Content\Wordpress\Wordpress_Dataset_Content_Service_Hooks;
use Wordlift\Entity\Entity_Uri_Generator;
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
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/post-matches',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_post_matches' ),
				'args'                => array(
					'cursor'      => array(
						'type'              => 'string',
						// 'default'           => Cursor::EMPTY_CURSOR_AS_BASE64_STRING,
						'validate_callback' => 'rest_validate_request_arg',
						// 'sanitize_callback' => array( Cursor::class, 'rest_sanitize_request_arg' ),
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
						'enum'              => array( 'publish', 'draft', 'all' ),
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
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/post-matches/(?P<post_id>\d+)/matches',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'create_post_match' ),
				'args'                => array(
					'post_id' => array(
						'required'          => true,
						'validate_callback' => 'rest_validate_request_arg',
						'type'              => 'integer',
					),
				),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);

		// Update an existing post match
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/post-matches/(?P<post_id>\d+)/matches/(?P<match_id>\d+)',
			array(
				'methods'             => 'PUT',
				'callback'            => array( $this, 'update_post_match' ),
				'args'                => array(
					'post_id'  => array(
						'required'          => true,
						'validate_callback' => 'rest_validate_request_arg',
						'type'              => 'integer',
					),
					'match_id' => array(
						'required'          => true,
						'validate_callback' => 'rest_validate_request_arg',
						'type'              => 'integer',
					),
				),
				'permission_callback' => function () {

					return current_user_can( 'manage_options' );
				},
			)
		);
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return array|object|\stdClass[]|null
	 * @throws Exception when some of the parameters are not in the accepted format.
	 */
	public function get_post_matches( $request ) {
		$limit       = $request->has_param( 'limit' ) ? $request->get_param( 'limit' ) : 10;
		$cursor      = $request->has_param( 'cursor' )
			? Cursor::from_base64_string( $request->get_param( 'cursor' ) )
			: Cursor::empty_cursor();
		$cursor_sort = new Cursor_Sort(
			$request->has_param( 'sort' ) ? $request->get_param( 'sort' ) : '-date_modified_gmt'
		);

		// If we're looking for recipes, we need the Recipe_Query.
		if ( in_array( 'wprm_recipe', (array) $request->get_param( 'post_types' ), true ) ) {
			$query = new Recipe_Query( $request, $cursor, $cursor_sort, $limit + 1 );
		} else {
			$query = new Post_Query( $request, $cursor, $cursor_sort, $limit + 1 );
		}

		$results       = $query->get_results();
		$items         = $cursor->get_direction() === 'ASCENDING'
			? array_slice( $results, 0, min( count( $results ), $limit ) )
			: array_slice( $results, count( $results ) > $limit ? 1 : 0 );
		$position      = current( $items )->{$cursor_sort->get_sort_property()};
		$next_position = end( $items )->{$cursor_sort->get_sort_property()};

		$self  = $cursor;
		$first = new Cursor( null, 'INCLUDED', 'ASCENDING' );
		$last  = new Cursor( null, 'INCLUDED', 'DESCENDING' );

		return new Page(
			$items,
			$self->to_base64_string(),
			$first->to_base64_string(),
			$this->get_prev_cursor_as_base64_string( $cursor, $results, $limit, $position ),
			$this->get_next_cursor_as_base64_string( $cursor, $results, $limit, $next_position ),
			$last->to_base64_string()
		);
	}

	/**
	 * @param Cursor $cursor
	 *
	 * @return string|null
	 * @throws Exception when one of the called functions throws an exception.
	 */
	private function get_prev_cursor_as_base64_string( $cursor, $results, $limit, $position ) {
		if ( ( $cursor->get_direction() === 'ASCENDING' && $cursor->get_position() !== null ) || ( $cursor->get_direction() === 'DESCENDING' && count( $results ) > $limit ) ) {
			$cursor = new Cursor( $position, 'EXCLUDED', 'DESCENDING' );

			return $cursor->to_base64_string();
		}

		return null;
	}

	/**
	 * @param Cursor $cursor
	 *
	 * @return string|null
	 * @throws Exception when one of the called functions throws an exception.
	 */
	private function get_next_cursor_as_base64_string( $cursor, $results, $limit, $position ) {
		if ( ( $cursor->get_direction() === 'DESCENDING' && $cursor->get_position() !== null ) || ( $cursor->get_direction() === 'ASCENDING' && count( $results ) > $limit ) ) {
			$cursor = new Cursor( $position, 'EXCLUDED', 'ASCENDING' );

			return $cursor->to_base64_string();
		}

		return null;
	}

	/**
	 * Create a new match for a post.
	 *
	 * @param  $request \WP_REST_Request
	 *
	 * @throws Exception If there was a problem creating the match.
	 */
	public function create_post_match( $request ) {
		$post_id = $request->get_param( 'post_id' );

		// If we dont have a entry on the match table, then add one.
		$content_id = Wordpress_Content_Id::create_post( $post_id );
		if ( ! Wordpress_Content_Service::get_instance()->get_entity_id( $content_id ) ) {
			$uri = Entity_Uri_Generator::create_uri( $content_id->get_type(), $content_id->get_id() );
			Wordpress_Content_Service::get_instance()->set_entity_id( $content_id, $uri );
		}

		$match_id = Wordpress_Dataset_Content_Service_Hooks::get_id_or_create(
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
	 * @throws Exception If there was a problem updating the match.
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
