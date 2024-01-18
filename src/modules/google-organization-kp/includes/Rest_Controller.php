<?php

/**
 * Module: 	Google Organization Knowledge Panel
 * Class: 	Rest_Controller
 *
 * A controller class that registers the endpoints for the API and handles sending requests and responses.
 *
 * @since
 * 
 * @package Wordlift/modules/google-organization-kp
 */

namespace Wordlift\Modules\Google_Organization_Kp;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class Rest_Controller {

	/**
	 * @var Organization_Knowledge_Panel_Service
	 * 
	 * @since
	 */
	private $organization_kp_service;

	/**
	 * Save a reference to the Organization Knowledge Panel Service class.
	 * 
	 * @since 3.53.0
	 * 
	 * @param Organization_Knowledge_Panel_Service $organization_kp_service
	 */
	public function __construct( Organization_Knowledge_Panel_Service $organization_kp_service ) {
		$this->organization_kp_service = $organization_kp_service;
	}

	/**
	 * Register the rest routes on the WP rest_api_init hook.
	 * 
	 * @since 3.53.0
	 */
    public function init() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Define the rest routes and the permission, sanitization, and final callbacks.
	 * 
	 * @since 3.53.0
	 */
	public function register_routes() {
        register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/wl-google-organization-kp/pages',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'pages_get_callback' ),
				'args'                => array(
					'pagination' => array(
						'default' 			=> 0,
						'required'          => true,
						'validate_callback' => function( $param ) {
							return is_numeric( $param );
						}
					),
					'title_starts_with' => array(
						'required'          => false,
						'validate_callback' => function( $param ) {
							return is_string( $param );
						}
					),
				),
				'permission_callback' => '__return_true' // Testing
				// 'permission_callback' => function () {
				// 	return current_user_can( 'manage_options' );
				// },
			)
		);

		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/wl-google-organization-kp/countries',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'countries_get_callback' ),
				'permission_callback' => '__return_true' // Testing
				// 'permission_callback' => function () {
				// 	return current_user_can( 'manage_options' );
				// },
			)
		);

		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/wl-google-organization-kp/data',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'form_data_get_callback' ),
				'permission_callback' => '__return_true' // Testing
				// 'permission_callback' => function () {
				// 	return current_user_can( 'manage_options' );
				// },
			)
		);

		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'wl-google-organization-kp/data',
			array(
				'methods'			  => 'POST',
				'callback'			  => array( $this, 'form_data_post_callback' ),
				'permission_callback' => '__return_true' // Testing
				// 'permission_callback' => function () {
				// 	return current_user_can( 'manage_options' );
				// },
			)
		);
    }

	/**
	 * Handle a request to the pages GET endpoint.
	 * 
	 * Gets a list of pages from the service and returns.
	 * 
	 * The expected request parameters are:
	 * - <int> pagination 			: The pagination step
	 * - <string> title_starts_with : A filter to narrow down pages by the starting characters of the title.
	 * 
	 * @param 	WP_REST_Request $request The Wordpress request object.
	 *
	 * @return 	WP_REST_Response|WP_Error Returns a Wordpress response object, or a Wordpress error object if something went wrong.
	 *
	 * @since 3.53.0
	 *
	 */
    public function pages_get_callback( WP_REST_Request $request ) {
		// Get the pages data from the service and return.
		$params = $request->get_params();
		$data = $this->organization_kp_service->get_pages( $params['pagination'], $params['title_starts_with'] );

        return rest_ensure_response( $data );
    }

	/**
	 * Handle a request to the countries GET endpoint.
	 *
	 * Gets an array of countries from the service and return.
	 *
	 * @return 	WP_REST_Response|WP_Error Returns a Wordpress response object, or a Wordpress error object if something went wrong.
	 *
	 * @since 3.53.0
	 */
	public function form_data_get_callback() {
		// Get the publisher data from the service and return.
		$data = $this->organization_kp_service->get_form_data();

		// @todo: Should we include this or simply return a blank array?
		if ( empty( $data ) ) {
			return new WP_Error( '404', 'No existing form data.', array( 'status' => 404 ) );
		}

		return rest_ensure_response( $data );
	}

	/**
	 * Handle a request to the countries GET endpoint.
	 *
	 * Gets an array of countries from the service and return.
	 *
	 * @return 	WP_REST_Response|WP_Error Returns a Wordpress response object, or a Wordpress error object if something went wrong.
	 *
	 * @since 3.53.0
	 */
	public function countries_get_callback() {
		// Get the countries data from the service and return
		$data = $this->organization_kp_service->get_countries();

		return rest_ensure_response( $data );
	}

	/**
	 * Handle a request to the data POST endpoint.
	 *
	 * Passes the parameters to the service to save the publisher data
	 *
	 * @param 	WP_REST_Request $request The Wordpress request object.
	 *
	 * @return 	WP_REST_Response|WP_Error Returns a Wordpress response object, or a Wordpress error object if something went wrong.
	 *
	 * @since 3.53.0
	 */
	public function form_data_post_callback( WP_REST_Request $request ) {
		/**
		 * Required params:
		 * - id
		 * - name
		 * - type
		 * - logo / image
		 */

		// Retrieve the relevant form data from the request and send it to the service.

		$params = $request->get_params();
		$files  = $request->get_file_params();

		if ( ! empty( $files['image'] ) ) {
			$params['image'] = $files['image'];
		}

		// Return error if MIME type not set.
//		if ( ! isset( $request_file['type'] ) ) {
//			return new WP_Error( '400', 'File mime type is not supported', array( 'status' => 400 ) );
//		}

		// Return error if file type is not image
//		if ( strpos( $request_file['type'], 'image' ) === false ) {
//			return new WP_Error( '400', 'Only image files are supported', array( 'status' => 400 ) );
//		}

		return rest_ensure_response( $this->organization_kp_service->set_form_data( $params ) );
	}
}