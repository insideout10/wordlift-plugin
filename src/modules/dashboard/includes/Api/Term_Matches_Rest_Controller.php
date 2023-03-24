<?php

namespace Wordlift\Modules\Dashboard\Api;

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;
use Wordlift\Entity\Entity_Uri_Generator;
use Wordlift\Object_Type_Enum;

class Term_Matches_Rest_Controller extends \WP_REST_Controller {
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

		// Get term matches by taxonomy name
		register_rest_route(
			'/wl-dashboard/v1/wordlift/v1',
			'/term-matches',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'get_term_matches' ),
				'args'     => array(
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
			//
			// 'permission_callback' => function () {
			// return current_user_can( 'manage_options' );
			// },
			)
		);

		// Create a new match for a term
		register_rest_route(
			'/wl-dashboard/v1/wordlift/v1',
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
			'/wl-dashboard/v1/wordlift/v1',
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
		$limit        = $query_params['limit'] ? $query_params['limit'] : 10;

		$cursor_args = array(
			'limit'     => $limit,
			'position'  => 0,
			'direction' => Page::FORWARD,
		);
		if ( isset( $query_params['cursor'] ) && is_string( $query_params['cursor'] ) ) {
			$cursor_args = wp_parse_args( json_decode( base64_decode( $query_params['cursor'] ), true ), $cursor_args );
		}
		$operator       = $cursor_args['direction'] === Page::FORWARD ? '>' : '<';
		$sort_direction = $cursor_args['direction'] === Page::FORWARD ? 'ASC' : 'DESC';
		$position       = $cursor_args['position'];

		$query = $wpdb->prepare(
			"SELECT t.term_id as id, e.about_jsonld as match_jsonld,  t.name,  e.id AS match_id FROM {$wpdb->prefix}terms t
     INNER JOIN {$wpdb->prefix}term_taxonomy tt ON t.term_id = tt.term_id
     LEFT JOIN {$wpdb->prefix}wl_entities e ON t.term_id = e.content_id
     WHERE e.content_type = %d AND tt.taxonomy = %s AND t.term_id {$operator} %d ORDER BY t.term_id {$sort_direction} LIMIT %d",
			Object_Type_Enum::TERM,
			$taxonomy,
			$position,
			$cursor_args['limit']
		);

		error_log( $query );

		$items = array_map(
			function ( $e ) {
				return Match_Entry::from( $e )->serialize();
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
	  * Create a new match for a term.
	  *
	  * @var $request \WP_REST_Request
	  */
	public function create_term_match( $request ) {

		$term_id = $request->get_param( 'term_id' );

		// If we dont have a entry on the match table, then add one.
		$content_id = Wordpress_Content_Id::create_term( $term_id );
		if ( ! Wordpress_Content_Service::get_instance()
										->get_entity_id( $content_id ) ) {
			$uri = Entity_Uri_Generator::create_uri( $content_id->get_type(), $content_id->get_id() );
			Wordpress_Content_Service::get_instance()->set_entity_id( $content_id, $uri );
		}

		$match_id = $this->match_service->get_id(
			$term_id,
			Object_Type_Enum::TERM
		);

		return $this->match_service->set_jsonld(
			$term_id,
			Object_Type_Enum::TERM,
			$match_id,
			$request->get_json_params()
		)->serialize();

	}

	 /**
	  * @var $request \WP_REST_Request
	  */
	public function update_term_match( $request ) {
		return $this->match_service->set_jsonld(
			$request->get_param( 'term_id' ),
			Object_Type_Enum::TERM,
			$request->get_param( 'match_id' ),
			$request->get_json_params()
		)->serialize();
	}

}
