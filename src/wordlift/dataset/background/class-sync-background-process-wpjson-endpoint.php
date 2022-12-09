<?php

namespace Wordlift\Dataset\Background;

use WP_REST_Server;

class Sync_Background_Process_Wpjson_Endpoint {

	/**
	 * @var Sync_Background_Process
	 */
	private $sync_background_process;

	/**
	 * Sync_Background_Process_Wpjson_Endpoint constructor.
	 *
	 * @param Sync_Background_Process $sync_background_process
	 */
	public function __construct( $sync_background_process ) {

		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );

		$this->sync_background_process = $sync_background_process;

	}

	public function rest_api_init() {

		register_rest_route(
			'wordlift/v1',
			'/dataset/background/sync',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this->sync_background_process, 'start' ),
				'permission_callback' => function () {
					$user = wp_get_current_user();

					return is_super_admin( $user->ID ) || in_array( 'administrator', (array) $user->roles, true );
				},
			)
		);

		register_rest_route(
			'wordlift/v1',
			'/dataset/background/sync',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this->sync_background_process, 'get_info' ),
				'permission_callback' => function () {
					$user = wp_get_current_user();

					return is_super_admin( $user->ID ) || in_array( 'administrator', (array) $user->roles, true );
				},
			)
		);

		register_rest_route(
			'wordlift/v1',
			'/dataset/background/sync',
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this->sync_background_process, 'stop' ),
				'permission_callback' => function () {
					$user = wp_get_current_user();

					return is_super_admin( $user->ID ) || in_array( 'administrator', (array) $user->roles, true );
				},
			)
		);

		register_rest_route(
			'wordlift/v1',
			'/dataset/background/sync',
			array(
				'methods'             => 'PUT',
				'callback'            => array( $this->sync_background_process, 'resume' ),
				'permission_callback' => function () {
					$user = wp_get_current_user();

					return is_super_admin( $user->ID ) || in_array( 'administrator', (array) $user->roles, true );
				},
			)
		);

	}

}
