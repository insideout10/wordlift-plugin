<?php

namespace Wordlift\Modules\Redeem_Code;

use Wordlift\Api\User_Agent;
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
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	public function rest_api_init() {
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/redeem-codes',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'create_sync' ),
				'args'                => array(
					'redeem_code'        => array(
						'required'          => true,
						'validate_callback' => 'rest_validate_request_arg',
					),
					'enable_diagnostics' => array(
						'required'          => true,
						'validate_callback' => 'rest_validate_request_arg',
					),
				),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
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
	}

	public function create_sync( $request ) {
		$redeem_code        = $request->get_param( 'redeem_code' );
		$enable_diagnostics = $request->get_param( 'enable_diagnostics' );

		$url = trailingslashit( WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE ) . 'redeem-codes';

		$body = wp_json_encode(
			array(
				'redeem_code' => $redeem_code,
			)
		);

		$response = wp_remote_post(
			$url,
			array(
				'timeout'    => 60,
				'user-agent' => User_Agent::get_user_agent(),
				'headers'    => array(
					'Content-Type' => 'application/json',
					'Expect'       => '',
				),
				'body'       => $body,
			)
		);

		$code    = wp_remote_retrieve_response_code( $response );
		$message = wp_remote_retrieve_response_message( $response );

		if ( empty( $code ) || ! is_numeric( $code ) ) {
			wp_send_json_error( $message, $code );
		}

		if ( 409 === $code ) {
			return new \WP_REST_Response(
				array(
					'title'  => 'Redeem Code already used',
					'status' => $code,
					'detail' => 'The redeem code has been used already, try with another redeem code.',
				),
				$code
			);
		}

		if ( 404 === $code ) {
			return new \WP_REST_Response(
				array(
					'title'  => 'Invalid Redeem Code',
					'status' => $code,
					'detail' => 'The redeem code is invalid, check for typos or try with another code.',
				),
				$code
			);
		}

		if ( 500 === $code ) {
			return new \WP_REST_Response( json_decode( $response['body'] ), $code );
		}

		$key_array = json_decode( $response['body'] );
		$key       = $key_array->key;

		$this->configuration_service->set_key( $key );
		$this->configuration_service->set_diagnostic_preferences( $enable_diagnostics );

		return new \WP_REST_Response(
			array(
				'key'      => $key,
				'status'   => $this->configuration_service->get_dataset_uri(),
				'language' => 'en',
			),
			$code
		);
	}

}
