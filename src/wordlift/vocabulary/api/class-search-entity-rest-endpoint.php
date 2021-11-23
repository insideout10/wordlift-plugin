<?php

namespace Wordlift\Vocabulary\Api;

use Wordlift\Vocabulary\Analysis_Service;

class Search_Entity_Rest_Endpoint {

	/**
	 * @var Analysis_Service
	 */
	private $analysis_service;

	public function __construct( $api_service ) {
		$this->analysis_service = $api_service;
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
		$data     = $request->get_params();
		$search   = $data['entity'];
		$entities = $this->analysis_service->get_entities_by_search_query( (string) $search );

		return $this->convert_to_autocomplete_ui_response( $entities );
	}

	private function convert_to_autocomplete_ui_response( $entities ) {

		$autocomplete_entities = array();

		foreach ( $entities as $entity_id => $entity_data ) {

			$label                   = $entity_data['label'];
			$types                   = $entity_data['types'];
			$autocomplete_entities[] = array(
				"id"           => $entity_data["entityId"],
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
				"value"        => $entity_id,
				"confidence"   => $entity_data["confidence"],
				"meta"         => $entity_data["meta"]
			);
		}

		return $autocomplete_entities;
	}
}
