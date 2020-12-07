<?php

namespace Wordlift\Dataset;

use WP_REST_Server;

class Sync_Wpjson_Endpoint {

	/**
	 * @var Sync_Service
	 */
	private $sync_service;

	function __construct( $sync_service ) {

		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
		$this->sync_service = $sync_service;
	}

	function rest_api_init() {

		// register_rest_route() handles more arguments but we are going to stick to the basics for now.
		register_rest_route( 'wordlift/v1', '/dataset/count', array(
			// By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
			'methods'  => WP_REST_Server::READABLE,
			// Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
			'callback' => array( $this, 'count' ),
		) );

		register_rest_route( 'wordlift/v1', '/dataset/next', array(
			// By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
			'methods'  => WP_REST_Server::READABLE,
			// Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
			'callback' => array( $this, 'next' ),
		) );

		register_rest_route( 'wordlift/v1', '/dataset/info', array(
			// By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
			'methods'  => WP_REST_Server::READABLE,
			// Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
			'callback' => array( $this, 'info' ),
		) );

		// register_rest_route() handles more arguments but we are going to stick to the basics for now.
		register_rest_route( 'wordlift/v1', '/dataset/sync', array(
			// By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
			'methods'  => WP_REST_Server::CREATABLE,
			// Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
			'callback' => array( $this, 'post__dataset__sync' ),
		) );

		// register_rest_route() handles more arguments but we are going to stick to the basics for now.
		register_rest_route( 'wordlift/v1', '/dataset/sync', array(
			// By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
			'methods'  => WP_REST_Server::DELETABLE,
			// Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
			'callback' => array( $this, 'delete__dataset__sync' ),
		) );


	}

	function count() {
		return rest_ensure_response( $this->sync_service->count() );
	}

	function next() {
		return rest_ensure_response( $this->sync_service->next() );
	}

	function post__dataset__sync() {

		$this->sync_service->start();

	}

	function delete__dataset__sync() {

		$this->sync_service->request_cancel();

	}

	function info() {
		return rest_ensure_response( $this->sync_service->info() );
	}

}
