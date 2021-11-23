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
			'/search-entity',
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
		$data   = $request->get_params();
		$search = $data['entity'];

		$response = $this->api_service->request(
			'POST',
			"/analysis/single",
			array( 'Content-Type' => 'application/json' ),
			wp_json_encode( array(
				"content"         => $search,
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

		return $this->convert_to_autocomplete_ui_response( $response['entities'] );
	}

	private function convert_to_autocomplete_ui_response( $entities ) {

		$autocomplete_entities = array();

		foreach ( $entities as $entity_id => $entity_data ) {

			$label                   = $entity_data['label'];
			$types                   = $entity_data['types'];
			$autocomplete_entities[] = array(
				"id"           => $entity_id,
				"labels"       => array( $label ),
				"descriptions" => array( $entity_data['description'] ),
				"types"        => $types,
				"urls"         => array( $entity_id ),
				"images"       => array(),
				"sameAss"      => $entity_data['sameAs'],
				"scope"        => "cloud",
				"description"  => $entity_data['description'],
				"mainType"     => $entity_data['mainType'],
				"label"        => $label,
				"displayTypes" => $types,
				"value"        => $entity_id
			);
		}

		return $autocomplete_entities;
	}
}
