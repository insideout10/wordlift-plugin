<?php

namespace Wordlift\Modules\Redeem_Code\Includes;

// use WP_REST_Request;
// use WP_REST_Response;

var_dump('hello a13xs');
	die();

class Rest_Controller {

	/**
	 * @var \Wordlift_Configuration_Service
	 */

	// var_dump('hello a13xs');
	// die();
	private $configuration_service;

	public function __construct() {
		$this->configuration_service = Wordlift_Configuration_Service::get_instance();
	}

	public function register_hooks() {
		// var_dump('hello a13xs');
		// die();
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	public function rest_api_init() {
		register_rest_route(
			'wl-dashboard/v1',
			'/redeem-codes',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'test' )
			)
		);

        register_rest_route(
            'wl-dashboard/v1',
            '/redeem-codes',
            array(
                'methods'  => 'POST',
                'callback' => array( $this, 'create_sync' ),
				'args'                => array(
					'redeem_code'  => array(
						'required'          => true,
						'validate_callback' => 'rest_validate_request_arg',
					),
					'enable_diagnostics' => array(
						'required'          => true,
						'validate_callback' => 'rest_validate_request_arg',
					),
				),
            )
        );
		// register_rest_route(
		// 'wl-dashboard/v1',
		// '/synchronizations',
		// array(
		// 'methods'  => 'POST',
		// 'callback' => array( $this, 'create_sync' ),
		// )
		// );

		/**
		 * POST /redeem-codes
		 * Accept: application/json
		 * Content-Type: application/json
		 *
		 * { "redeem_code": "a_redeem_code" }
		 *
		 *
		 * Successful Response:
		 *
		 * Content-Type: application/json
		 *
		 * { "key": "a_key" }
		 *
		 *
		 * Unsuccessful Response:
		 *
		 * Content-Type: application/json
		 *
		 * {
		 * "title": "Invalid Redeem Code",
		 * "status": 404,
		 * "detail": "The redeem code is invalid, check for typos or try with another code."
		 * }
		 *
		 * or
		 *
		 * {
		 * "title": "Redeem Code already used",
		 * "status": 409,
		 * "detail": "The redeem code has been used already, try with another redeem code."
		 * }
		 */

		// wp_remote_post(WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE . '/remote-codes');

		// $this->configuration_service->set_key( ... );
		// $this->configuration_service->set_diagnostic_preferences( ... );
	}

	public function create_sync( $request ) {
		$redeem_code = $request->get_param( 'redeem_code' );
		$enable_diagnostics = $request->get_param( 'enable_diagnostics' );

		var_dump($redeem_code, $enable_diagnostics);
		die();

		$this->configuration_service->set_key($redeem_code);
		$this->configuration_service->set_diagnostic_preferences($enable_diagnostics);
        // try {
        //     return rest_ensure_response( $this->synchronization_service->create() );
        // } catch ( \Exception $e ) {
        //     return new \WP_Error( 'wl_error_synchronization_running', esc_html__( 'Another synchronization is already running.', 'wordlift' ), array( 'status' => 409 ) );
        // }
    }

	public function test() {
		// $this->synchronization_service->delete_syncs();

		var_dump('hello a13xs');
	die();

		// return new WP_REST_Response();
	}

}
