<?php
/**
 * This file defines the JSON-LD endpoint, this is an example request for this new end-point:
 *
 * https://wordlift.localhost/wp-json/wordlift/v1/jsonld?id[]=http://data.wordlift.io/wl01093/entity/masashi_kishimoto&id[]=http://data.wordlift.io/wl01093/entity/shueisha&website
 *
 * @author David Riccitelli <david@wordlift.io>
 * @package Wordlift\Jsonld
 * @since 3.26.0
 */

namespace Wordlift\Jsonld;

use Wordlift_Jsonld_Service;
use WP_Error;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Class Jsonld_Endpoint
 *
 * @package Wordlift\Jsonld
 */
class Jsonld_By_Id_Endpoint {

	/**
	 * The {@link Wordlift_Jsonld_Service} instance.
	 *
	 * @var Wordlift_Jsonld_Service The {@link Wordlift_Jsonld_Service} instance.
	 */
	private $jsonld_service;

	/**
	 * @var \Wordlift_Entity_Uri_Service
	 */
	private $entity_uri_service;

	/**
	 * Jsonld_Endpoint constructor.
	 *
	 * @param \Wordlift_Jsonld_Service     $jsonld_service
	 * @param \Wordlift_Entity_Uri_Service $entity_uri_service
	 */
	public function __construct( $jsonld_service, $entity_uri_service ) {

		$this->jsonld_service     = $jsonld_service;
		$this->entity_uri_service = $entity_uri_service;

		add_action( 'rest_api_init', array( $this, 'register_routes' ) );

	}

	/**
	 * Get the JSON-LD.
	 *
	 * @param \WP_REST_Request $request The incoming {@link \WP_REST_Request}.
	 *
	 * @return WP_REST_Response The outgoing {@link WP_REST_Response}.
	 * @throws \Exception when an error occurs.
	 */
	public function jsonld_by_id( $request ) {

		// Get the ids.
		$ids = (array) $request->get_param( 'id' );

		// Preload the URIs to reduce the number of DB roundtrips.
		$this->entity_uri_service->preload_uris( array_map( 'urldecode', $ids ) );

		$that = $this;

		// Get the posts, filtering out those not found.
		$posts = array_filter(
			array_map(
				function ( $item ) use ( $that ) {
					return $that->entity_uri_service->get_entity( urldecode( $item ) );
				},
				$ids
			)
		);

		// Get the posts' IDs and make the unique.
		$post_ids = array_unique(
			array_map(
				function ( $item ) {
					return $item->ID;
				},
				$posts
			)
		);

		// Get the JSON-LD.
		$data = array();
		foreach ( $post_ids as $post_id ) {
			$data = array_merge( $data, $that->jsonld_service->get_jsonld( false, $post_id ) );
		}

		// Add the WebSite fragment if requested.
		if ( $request->get_param( 'website' ) ) {
			$data[] = $this->jsonld_service->get_jsonld( true );
		}

		return Jsonld_Response_Helper::to_response( $data );
	}

	public function register_routes() {

		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/jsonld',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'jsonld_by_id' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'id'      => array(
						'description'       => __( 'One ore more itemids (e.g. http://data.wordlift.io/wordlift).', 'wordlift' ),
						// We expect an array of strings.
						'type'              => 'array',
						'items'             => array(
							'type' => 'string',
						),
						// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
						'validate_callback' => function ( $values, $request, $param ) {

							if ( ! is_array( $values ) ) {
								return new WP_Error( 'rest_invalid_param', esc_html__( 'The id argument must be an array (try passing `id[]=...`.', 'wordlift' ), array( 'status' => 400 ) );
							}

							foreach ( $values as $value ) {
								if ( 0 !== strpos( $value, 'http' ) ) {
									return new WP_Error( 'rest_invalid_param', esc_html__( 'Ids must start with http.', 'wordlift' ), array( 'status' => 400 ) );
								}
							}
						},
					),
					'website' => array(
						'description' => __( 'Whether to include the WebSite markup.', 'wordlift' ),
					),
				),
			)
		);

	}

}
