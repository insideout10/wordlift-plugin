<?php

namespace Wordlift\Vocabulary\Api;

use Wordlift\Api\Api_Service;

class Search_Entity_Rest_Endpoint {

	/**
	 * @var Api_Service
	 */
	private $api_service;

	public function __construct( $api_service ) {
		$this->api_service = $api_service;
		add_action( 'rest_api_init', array( $this, 'register_route_callback' ) );
	}

	public function register_route_callback() {
		register_rest_route(
			Api_Config::REST_NAMESPACE,
			'/search-entity/(?P<entity>[a-zA-Z0-9_-]+)',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'get_entities_from_api' ),
				'args'                => array(
					'entity' => array(
						'validate_callback' => function ( $param ) {
							return is_string( $param );
						}
					),
				),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				}
			)
		);
	}

	public function get_entities_from_api( $request ) {
		$data               = $request->get_params();
		$autocomplete_input = $data['entity'];

		$response = $this->api_service->request(
			'POST',
			"/analysis/single",
			array( 'Content-Type' => 'application/json' ),
			wp_json_encode( array(
				"content"         => $autocomplete_input,
				"contentType"     => "text/plain",
				"version"         => "1.0.0",
				"contentLanguage" => "en",
				"scope"           => "all",
			) )
		);

		if ( ! $response->is_success() ) {
			return false;
		}

		$response = json_decode( $response->get_body(), true );

		if ( ! array_key_exists( 'entities', $response ) ) {
			return false;
		}

		return $response;
	}
}
