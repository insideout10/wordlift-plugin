<?php

namespace Wordlift\Widgets\Faceted_Search;

use WP_REST_Server;

/**
 * @since ?.??.?
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Faceted_Search_Template_Endpoint {

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_template_route' ) );
	}

	public function register_template_route() {

		register_rest_route( WL_REST_ROUTE_DEFAULT_NAMESPACE, '/faceted-search/template/', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'get_template' ),
			/**
			 * We want this endpoint to be publicly accessible
			 */
			'permission_callback' => '__return_true',
			'args'                => array(
				'template_id' => array(
					'validate_callback' => function ( $param, $request, $key ) {
						return is_string( $param ) && $param;
					},
				),
			)
		) );
	}

	public function get_template( $request ) {
		return '';
	}
}