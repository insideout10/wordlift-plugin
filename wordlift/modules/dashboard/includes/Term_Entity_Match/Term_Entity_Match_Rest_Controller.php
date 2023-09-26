<?php

namespace Wordlift\Modules\Dashboard\Term_Entity_Match;

use Exception;
use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;
use Wordlift\Content\Wordpress\Wordpress_Dataset_Content_Service_Hooks;
use Wordlift\Entity\Entity_Uri_Generator;
use Wordlift\Modules\Dashboard\Common\Cursor;
use Wordlift\Modules\Dashboard\Common\Cursor_Sort;
use Wordlift\Modules\Dashboard\Common\Page;
use Wordlift\Modules\Dashboard\Match\Match_Entry;
use Wordlift\Modules\Dashboard\Post_Entity_Match\Post_Entity_Match_Service;
use Wordlift\Object_Type_Enum;

/**
 * Class Term_Entity_Match_Rest_Controller
 *
 * @package Wordlift\Modules\Dashboard\Term_Entity_Match
 */
class Term_Entity_Match_Rest_Controller extends \WP_REST_Controller {

	/**
	 * @var Post_Entity_Match_Service
	 */
	private $match_service;

	/**
	 * Construct
	 *
	 * @param $match_service
	 */
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
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/term-matches',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_term_matches' ),
				'args'                => array(
					'cursor'        => array(
						'type'              => 'string',
						// 'default'           => Cursor::EMPTY_CURSOR_AS_BASE64_STRING,
						'validate_callback' => 'rest_validate_request_arg',
						// 'sanitize_callback' => array( Cursor::class, 'rest_sanitize_request_arg' ),
					),
					'limit'         => array(
						'type'              => 'integer',
						'validate_callback' => 'rest_validate_request_arg',
						'default'           => 10,
						'minimum'           => 1,
						'maximum'           => 100,
						'sanitize_callback' => 'absint',
					),
					'taxonomies'    => array(
						'type'              => 'array',
						'validate_callback' => 'rest_validate_request_arg',
					),
					'has_match'     => array(
						'type'              => 'boolean',
						'required'          => false,
						'validate_callback' => 'rest_validate_request_arg',
					),
					'term_contains' => array(
						'type'              => 'string',
						'required'          => false,
						'validate_callback' => 'rest_validate_request_arg',
					),
					'sort'          => array(
						'type'              => 'string',
						'required'          => 'false',
						'enum'              => array(
							'+term_name',
							'-term_name',
							'+entity_name',
							'-entity_name',
							'+occurrences',
							'-occurrences',
						),
						'validate_callback' => 'rest_validate_request_arg',
					),
				),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);

		// Create a new match for a term
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
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
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
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

	public function get_term_matches( $request ) {
		$limit       = $request->has_param( 'limit' ) ? $request->get_param( 'limit' ) : 10;
		$cursor      = $request->has_param( 'cursor' )
			? Cursor::from_base64_string( $request->get_param( 'cursor' ) )
			: Cursor::empty_cursor();
		$cursor_sort = new Cursor_Sort(
			$request->has_param( 'sort' ) ? $request->get_param( 'sort' ) : '+term_name'
		);

		$query = new Term_Query( $request, $cursor, $cursor_sort, $limit + 1 );

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
	 * Get the term matches by taxonomy name.
	 *
	 * @param  $request \WP_REST_Request
	 *
	 * @throws \Exception If there was a problem getting the match.
	 */
	public function get_term_matches_legacy( $request ) {

		$cursor = $request->get_param( 'cursor' );
		if ( $request->has_param( 'limit' ) ) {
			$cursor['limit'] = $request->get_param( 'limit' );
		}
		if ( $request->has_param( 'sort' ) ) {
			$cursor['sort'] = $request->get_param( 'sort' );
		}
		if ( $request->has_param( 'taxonomies' ) ) {
			$cursor['query']['taxonomies'] = $request->get_param( 'taxonomies' );
		}
		if ( $request->has_param( 'has_match' ) ) {
			$cursor['query']['has_match'] = $request->get_param( 'has_match' );
		}
		if ( $request->has_param( 'term_contains' ) ) {
			$cursor['query']['term_contains'] = $request->get_param( 'term_contains' );
		}

		// Query.
		$taxonomies = isset( $cursor['query']['taxonomies'] ) ? $cursor['query']['taxonomies'] : apply_filters(
			'wl_dashboard__post_entity_match__taxonomies',
			array(
				'post_tag',
				'category',
			)
		);

		$has_match     = isset( $cursor['query']['has_match'] ) ? $cursor['query']['has_match'] : null;
		$term_contains = isset( $cursor['query']['term_contains'] ) ? $cursor['query']['term_contains'] : null;

		$items = $this->match_service->list_items(
			array(
				// Query
				'taxonomies'    => $taxonomies,
				'has_match'     => $has_match,
				'term_contains' => $term_contains,
				// Cursor-Pagination
				'position'      => $cursor['position'],
				'element'       => $cursor['element'],
				'direction'     => $cursor['direction'],
				// `+1` to check if we have other results.
				'limit'         => $cursor['limit'] + 1,
				'sort'          => $cursor['sort'],
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
	 * Create a new match for a term.
	 *
	 * @param  $request \WP_REST_Request
	 *
	 * @throws \Exception If there was a problem creating the match.
	 */
	public function create_term_match( $request ) {

		$term_id = $request->get_param( 'term_id' );

		// If we dont have a entry on the match table, then add one.
		$content_id = Wordpress_Content_Id::create_term( $term_id );
		if ( ! Wordpress_Content_Service::get_instance()->get_entity_id( $content_id ) ) {
			$uri = Entity_Uri_Generator::create_uri( $content_id->get_type(), $content_id->get_id() );
			Wordpress_Content_Service::get_instance()->set_entity_id( $content_id, $uri );
		}

		$match_id = Wordpress_Dataset_Content_Service_Hooks::get_id_or_create(
			$term_id,
			Object_Type_Enum::TERM
		);

		return $this->match_service->set_jsonld(
			$term_id,
			Object_Type_Enum::TERM,
			$match_id,
			$request->get_json_params()
		);
	}

	/**
	 * Update term match.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return Match_Entry
	 *
	 * @throws \Exception If there was a problem updating the match.
	 */
	public function update_term_match( $request ) {
		return $this->match_service->set_jsonld(
			$request->get_param( 'term_id' ),
			Object_Type_Enum::TERM,
			$request->get_param( 'match_id' ),
			$request->get_json_params()
		);
	}

}
