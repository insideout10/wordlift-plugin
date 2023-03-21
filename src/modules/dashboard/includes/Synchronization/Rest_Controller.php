<?php

namespace Wordlift\Modules\Dashboard\Synchronization;

class Rest_Controller {

	/**
	 * @var Synchronization_Service $synchronization_service
	 */
	private $synchronization_service;

	/**
	 * @param Synchronization_Service $synchronization_service
	 */
	public function __construct( $synchronization_service ) {
		$this->synchronization_service = $synchronization_service;
	}

	public function register_hooks() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	public function rest_api_init() {
		register_rest_route(
			'wl-dashboard/v1',
			'/synchronizations',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'create_sync' ),
			)
		);
	}

	public function create_sync() {
		try {
			return rest_ensure_response( $this->synchronization_service->create() );
		} catch ( \Exception $e ) {
			return new \WP_Error( 'wl_error_synchronization_running', esc_html__( 'Another synchronization is already running.', 'wordlift' ), array( 'status' => 409 ) );
		}

		// return as_enqueue_async_action( Scheduler::HOOK, array(), Scheduler::GROUP );
	}

}
