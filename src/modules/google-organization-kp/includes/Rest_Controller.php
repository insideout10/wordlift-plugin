<?php

namespace Wordlift\Modules\Google_Organization_Kp;

use Wordlift\Modules\Google_Organization_Kp\Symfony\Component\Config\Definition\Exception\Exception;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class Rest_Controller {
	/**
	 * @var \Wordlift_Countries
	 */
	private $countries;

	/**
	 * @var Publisher_Service
	 *
	 * @since
	 */
	private $publisher_service;

	/**
	 * @var Page_Service
	 *
	 * @since
	 */
	private $page_service;

	/**
	 * Save a reference to the Organization Knowledge Panel Service class.
	 *
	 * @since 3.53.0
	 *
	 * @param \Wordlift_Countries $wl_countries
	 * @param Publisher_Service   $publisher_service
	 * @param Page_Service        $page_service
	 */
	public function __construct(
		$countries,
		$publisher_service,
		$page_service
	) {
		$this->countries         = $countries;
		$this->publisher_service = $publisher_service;
		$this->page_service      = $page_service;
	}

	/**
	 * Register the rest routes on the WP rest_api_init hook.
	 *
	 * @since 3.53.0
	 */
	public function register_hooks() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Define the rest routes and the permission, sanitization, and final callbacks.
	 *
	 * @since 3.53.0
	 */
	public function register_routes() {
		// @@TODO check that this works fine with the autocomplete component:
		// https://ng.ant.design/components/auto-complete/en
		// @@TODO did we really need this API or could we have used this?
		// https://developer.wordpress.org/rest-api/reference/pages/
		// The suggested WordPress endpoint should work fine. E.g.
		// https://wordlift.www.localhost/wp-json/wp/v2/pages?search=sam&search_columns=post_title
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/wl-google-organization-kp/pages',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'pages_get_callback' ),
				'args'                => array(
					'pagination'        => array(
						'default'           => 0,
						'required'          => true,
						'validate_callback' => function ( $param ) {
							return is_numeric( $param );
						},
					),
					'title_starts_with' => array(
						'required'          => false,
						'validate_callback' => function ( $param ) {
							return is_string( $param );
						},
					),
				),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);

		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/wl-google-organization-kp/countries',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'countries_get_callback' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);

		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/wl-google-organization-kp/data',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'form_data_get_callback' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);

		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'wl-google-organization-kp/data',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'form_data_post_callback' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
	}

	/**
	 * Handle a request to the pages GET endpoint.
	 *
	 * Gets a list of pages from the service and returns.
	 *
	 * The expected request parameters are:
	 * - <int> pagination           : The pagination step
	 * - <string> title_starts_with : A filter to narrow down pages by the starting characters of the title.
	 *
	 * @param   WP_REST_Request $request The WordPress request object.
	 *
	 * @return  WP_REST_Response|WP_Error Returns a WordPress response object, or a WordPress error object if something went wrong.
	 *
	 * @since 3.53.0
	 */
	public function pages_get_callback( WP_REST_Request $request ) {
		// Get the pages data from the service and return.
		$params = $request->get_params();
		$data   = $this->page_service->get( $params['pagination'], $params['title_starts_with'] );

		return rest_ensure_response( $data );
	}

	/**
	 * Handle a request to the countries GET endpoint.
	 *
	 * Gets an array of countries from the service and return.
	 *
	 * @return  WP_REST_Response|WP_Error Returns a WordPress response object, or a WordPress error object if something went wrong.
	 *
	 * @since 3.53.0
	 */
	public function countries_get_callback() {
		// Get the countries data from the service and return.
		$data = $this->countries->get_countries();

		return rest_ensure_response( $data );
	}

	/**
	 * Handle a request to the countries GET endpoint.
	 *
	 * Gets an array of countries from the service and return.
	 *
	 * @return  WP_REST_Response|WP_Error Returns a WordPress response object, or a WordPress error object if something went wrong.
	 *
	 * @since 3.53.0
	 */
	public function form_data_get_callback() {
		// Get the publisher data from the service and return.
		$data = $this->publisher_service->get();

		if ( empty( $data ) ) {
			return new WP_Error( '404', 'No existing form data.', array( 'status' => 404 ) );
		}

		return rest_ensure_response( $data );
	}

	/**
	 * Handle a request to the data POST endpoint.
	 *
	 * Passes the parameters to the service to save the publisher data
	 *
	 * @param   WP_REST_Request $request The WordPress request object.
	 *
	 * @return  WP_REST_Response|WP_Error Returns a WordPress response object, or a WordPress error object if something went wrong.
	 *
	 * @since 3.53.0
	 */
	public function form_data_post_callback( WP_REST_Request $request ) {
		// Retrieve the relevant form data from the request and send it to the service.
		$params = $request->get_params();
		$files  = $request->get_file_params();

		// Check image.
		if ( ! empty( $files['image'] ) ) {
			// Return error if MIME type not set.
			if ( ! isset( $files['image']['type'] ) ) {
				return new WP_Error(
					'400',
					'File mime type is not supported',
					array( 'status' => 400 )
				);
			}

			// Return error if file type is not image
			if ( strpos( $files['image']['type'], 'image' ) === false ) {
				return new WP_Error(
					'400',
					'Only image files are supported',
					array( 'status' => 400 )
				);
			}

			// Add the image the the $params array.
			$params['image'] = $files['image'];
		}

		try {
			// Successfully return the form data.
			$data = $this->publisher_service->save( $params );
			rest_ensure_response( $data );
		} catch ( Exception $e ) {
			// Send back error.
			return new WP_Error(
				'400',
				$e->getMessage(),
				array( 'status' => 400 )
			);
		}
	}
}
