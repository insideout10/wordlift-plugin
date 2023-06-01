<?php

namespace Wordlift\Modules\Redeem_Code\Includes;

use Wordlift_Configuration_Service;

class Rest_Controller {

	/**
	 * @var Wordlift_Configuration_Service
	 */

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
				'methods'  => 'POST',
				'callback' => array( $this, 'create_sync' ),
				'args'     => array(
					'redeem_code'        => array(
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

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function create_sync( $request ) {
		// $redeem_code        = $request->get_param( 'redeem_code' );
		// $enable_diagnostics = $request->get_param( 'enable_diagnostics' );

		// $response = wp_remote_post(
		// trailingslashit( WORDLIFT_API_URL ) . 'redeem-codes',
		// array(
		// 'timeout'    => 60,
		// 'user-agent' => User_Agent::get_user_agent(),
		// 'headers'    => array(
		// 'Content-Type' => 'application/json',
		// *
		// * This is required to avoid CURL receiving 502 Bad Gateway errors.
		// *
		// * @see https://stackoverflow.com/questions/30601075/curl-to-google-compute-load-balancer-gets-error-502
		// */
		// 'Expect'       => '',
		// ),
		// 'body'       => wp_json_encode(
		// array(
		// 'redeem_code' => $redeem_code,
		// )
		// ),
		// )
		// );

		// 200, `key` prop ---> $key

		// otherwise it's an error and you can send it back as it is
		// The response you get from the API is already a problem/json
		// return new \WP_REST_Response();

		// $this->configuration_service->set_key( $key );
		// $this->configuration_service->set_diagnostic_preferences( $enable_diagnostics );
		// try {
		// return rest_ensure_response( $this->synchronization_service->create() );
		// } catch ( \Exception $e ) {
		// return new \WP_Error( 'wl_error_synchronization_running', esc_html__( 'Another synchronization is already running.', 'wordlift' ), array( 'status' => 409 ) );
		// }
	}

}
