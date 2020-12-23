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

	/**
	 * Faceted search widget makes call to this endpoint to get the template.
	 * Takes the request, checks if template id is registered via filter,
	 * if not it returns empty.
	 *
	 * @param $request \WP_REST_Request
	 *
	 * @return string Returns the template string.
	 */
	public function get_template( $request ) {
		$data        = $request->get_params();
		$template_id = (string) $data['template_id'];
		$templates   = apply_filters( 'wordlift_faceted_search_templates', array() );
		$template =  array_key_exists( $template_id, $templates ) ? $templates[$template_id] : '';
		return array( 'template' => $template );
	}
}