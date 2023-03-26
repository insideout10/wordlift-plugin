<?php

namespace Wordlift\Modules\Food_Kg\Api;

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
			'wordlift/v1',
			'/term-matches',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_term_matches' ),
				'args'                => array(
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
					'filter'   => array(
						'required'          => false,
						'type'              => 'string',
						'enum'              => array( 'ALL', 'MATCHED', 'UNMATCHED' ),
						'sanitize_callback' => 'sanitize_text_field',
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

		$items = $this->match_service->get_term_matches(
			$query_params['taxonomy'],
			$cursor_args['position'],
			$cursor_args['limit'],
			$cursor_args['direction'],
			$cursor_args['sort'],
			$query_params['filter']
		);

		$page = new Page( $query_params['cursor'], $items, $cursor_args['limit'], $cursor_args['position'] );
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
