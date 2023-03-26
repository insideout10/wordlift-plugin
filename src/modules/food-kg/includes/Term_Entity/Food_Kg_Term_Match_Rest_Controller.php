<?php

namespace Wordlift\Modules\Food_Kg\Term_Entity;

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;
use Wordlift\Entity\Entity_Uri_Generator;
use Wordlift\Modules\Food_Kg\Api\Cursor;
use Wordlift\Modules\Food_Kg\Api\Cursor_Page;
use Wordlift\Object_Type_Enum;

class Food_Kg_Term_Match_Rest_Controller extends \WP_REST_Controller {

	/**
	 * @var Food_Kg_Term_Entity_Match_Service
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
			'wordlift/v1',
			'/term-matches',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_term_matches' ),
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
					'taxonomy'  => array(
						'required'          => true,
						'validate_callback' => 'rest_validate_request_arg',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'has_match' => array(
						'required'          => false,
						'type'              => 'boolean',
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
			'/wordlift/v1',
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
			'/wordlift/v1',
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

		$cursor = $request->get_param( 'cursor' );
		if ( $request->has_param( 'limit' ) ) {
			$cursor['limit'] = $request->get_param( 'limit' );
		}
		if ( $request->has_param( 'sort' ) ) {
			$cursor['sort'] = $request->get_param( 'sort' );
		}
		if ( $request->has_param( 'taxonomy' ) ) {
			$cursor['query']['taxonomy'] = $request->get_param( 'taxonomy' );
		}
		if ( $request->has_param( 'has_match' ) ) {
			$cursor['query']['has_match'] = $request->get_param( 'has_match' );
		}

		// $position  = isset( $cursor['position'] ) ? $cursor['position'] : null;
		// $element   = isset( $cursor['element'] ) ? $cursor['element'] : 'INCLUDED';
		// $direction = isset( $cursor['direction'] ) ? $cursor['direction'] : 'ASCENDING';
		// $limit     = isset( $cursor['limit'] ) ? $cursor['limit'] : 20;
		// $sort      = isset( $cursor['sort'] ) ? $cursor['sort'] : '+id';
		// Query.
		$taxonomy  = isset( $cursor['query']['taxonomy'] ) ? $cursor['query']['taxonomy'] : 'wprm_ingredients';
		$has_match = isset( $cursor['query']['has_match'] ) ? $cursor['query']['has_match'] : null;

		$items = $this->match_service->list_items(
			array(
				// Query
				'taxonomy'  => $taxonomy,
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
