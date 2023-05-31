<?php

namespace Wordlift\Modules\Redeem_Code;

class Rest_Controller {

	/**
	 * @var \Wordlift_Configuration_Service
	 */
	private $configuration_service;

	public function __construct() {
		$this->configuration_service = \Wordlift_Configuration_Service::get_instance();
	}

	public function register_hooks() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	public function rest_api_init() {
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

}
