<?php

namespace Wordlift\Modules\Dashboard\Synchronization;

use WP_REST_Request;

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

		register_rest_route(
			'wl-dashboard/v1',
			'/synchronizations',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'list_syncs' ),
				'args'     => array(
					'is_running' => array(
						'description' => esc_html__( 'Filter the is_running', 'wordlift' ),
						'type'        => 'boolean',
					),
				),
			)
		);
	}

	public function create_sync() {
		try {
			return rest_ensure_response( $this->synchronization_service->create() );
		} catch ( \Exception $e ) {
			return new \WP_Error( 'wl_error_synchronization_running', esc_html__( 'Another synchronization is already running.', 'wordlift' ), array( 'status' => 409 ) );
		}
	}

	public function list_syncs( WP_REST_Request $request ) {
		$last_synchronization = $this->synchronization_service->load();
		$is_running_all       = ! $request->has_param( 'is_running' );
		if ( is_a( $last_synchronization, 'Wordlift\Modules\Dashboard\Synchronization\Synchronization' )
			 && ( $is_running_all || $request->get_param( 'is_running' ) === $last_synchronization->is_running() ) ) {
			$data = array( $last_synchronization );
		} else {
			$data = array();
		}

		return rest_ensure_response( array( 'items' => $data ) );
	}

}
