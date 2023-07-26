<?php
/**
 * This file defines the JSON-LD endpoint.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @package Wordlift\Jsonld
 */

namespace Wordlift\Jsonld;

use Wordlift\Object_Type_Enum;
use Wordlift_Jsonld_Service;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Class Jsonld_Endpoint
 *
 * @package Wordlift\Jsonld
 */
class Jsonld_Endpoint {

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
	 * @param Jsonld_Service               $jsonld_service
	 * @param \Wordlift_Entity_Uri_Service $entity_uri_service
	 */
	public function __construct( $jsonld_service, $entity_uri_service ) {

		$this->jsonld_service     = $jsonld_service;
		$this->entity_uri_service = $entity_uri_service;

		// PHP 5.3 compatibility.
		$that = $this;
		add_action(
			'rest_api_init',
			function () use ( $that ) {
				register_rest_route(
					WL_REST_ROUTE_DEFAULT_NAMESPACE,
					'/jsonld/(?P<id>\d+)',
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $that, 'jsonld_using_post_id' ),
						'permission_callback' => '__return_true',
						'args'                => array(
							'id' => array(
								// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
								'validate_callback' => function ( $param, $request, $key ) {
									return is_numeric( $param );
								},
								'sanitize_callback' => 'absint',
							),
						),
					)
				);

				register_rest_route(
					WL_REST_ROUTE_DEFAULT_NAMESPACE,
					'/jsonld/http/(?P<item_id>.*)',
					array(
						'methods'             => 'GET',
						'callback'            => array( $that, 'jsonld_using_item_id' ),
						'permission_callback' => '__return_true',
					)
				);

				register_rest_route(
					WL_REST_ROUTE_DEFAULT_NAMESPACE,
					'/jsonld/post-meta/(?P<meta_key>[^/]+)',
					array(
						'methods'             => 'GET',
						'callback'            => array( $that, 'jsonld_using_post_meta' ),
						'permission_callback' => '__return_true',
					)
				);

				register_rest_route(
					WL_REST_ROUTE_DEFAULT_NAMESPACE,
					'/jsonld/meta/(?P<meta_key>[^/]+)',
					array(
						'methods'             => 'GET',
						'callback'            => array( $that, 'jsonld_using_meta' ),
						'permission_callback' => '__return_true',
					)
				);

				register_rest_route(
					WL_REST_ROUTE_DEFAULT_NAMESPACE,
					'/jsonld/(?P<post_type>.*)/(?P<post_name>.*)',
					array(
						'methods'             => 'GET',
						'callback'            => array( $that, 'jsonld_using_get_page_by_path' ),
						'permission_callback' => '__return_true',
					)
				);

			}
		);

	}

	/**
	 * Callback for the JSON-LD request.
	 *
	 * @param array $request {
	 *  The request array.
	 *
	 * @type int $id The post id.
	 * }
	 *
	 * @return WP_REST_Response
	 * @throws \Exception when an error occurs.
	 */
	public function jsonld_using_post_id( $request ) {

		$post_id = $request['id'];
		$type    = ( 0 === $post_id ? Object_Type_Enum::HOMEPAGE : Object_Type_Enum::POST );

		// Send the generated JSON-LD.
		$data = $this->jsonld_service->get( $type, $post_id, Jsonld_Context_Enum::REST );

		return Jsonld_Response_Helper::to_response( $data );
	}

	/**
	 * Provide a JSON-LD given the itemId.
	 *
	 * @param array $request {
	 *  The request array.
	 *
	 * @type string $item_id The entity item id.
	 * }
	 *
	 * @return WP_REST_Response
	 * @throws \Exception when an error occurs.
	 */
	public function jsonld_using_item_id( $request ) {

		$item_id = 'http://' . $request['item_id'];
		$post    = $this->entity_uri_service->get_entity( $item_id );

		if ( ! is_a( $post, 'WP_Post' ) ) {
			return new WP_REST_Response( esc_html( "$item_id not found." ), 404, array( 'Content-Type' => 'text/html' ) );
		}

		return $this->jsonld_using_post_id( array( 'id' => $post->ID ) );
	}

	public function jsonld_using_get_page_by_path( $request ) {

		$post_name = $request['post_name'];
		$post_type = $request['post_type'];

		global $wpdb;

		$post_id = $wpdb->get_var(
			$wpdb->prepare(
				"
			SELECT ID
			FROM $wpdb->posts
			WHERE post_name = %s
			 AND post_type = %s
		",
				$post_name,
				$post_type
			)
		);

		if ( $post_id === null ) {
			return new WP_REST_Response( esc_html( "$post_name of type $post_type not found." ), 404, array( 'Content-Type' => 'text/html' ) );
		}

		return $this->jsonld_using_post_id( array( 'id' => $post_id ) );
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 * @throws \Exception when an error occurs.
	 */
	public function jsonld_using_post_meta( $request ) {

		$meta_key   = $request['meta_key'];
		$meta_value = urldecode( current( $request->get_query_params( 'meta_value' ) ) );

		global $wpdb;

		$post_id = $wpdb->get_var(
			$wpdb->prepare(
				"
			SELECT post_id AS ID
			FROM $wpdb->postmeta
			WHERE meta_key = %s
			 AND meta_value = %s
			LIMIT 1
		",
				$meta_key,
				$meta_value
			)
		);

		if ( $post_id === null ) {
			return new WP_REST_Response( esc_html( "Post with meta key $meta_key and value $meta_value not found." ), 404, array( 'Content-Type' => 'text/html' ) );
		}

		return $this->jsonld_using_post_id( array( 'id' => $post_id ) );
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function jsonld_using_meta( $request ) {

		global $wpdb;

		$meta_key    = $request['meta_key'];
		$params      = $request->get_query_params();
		$meta_value  = urldecode( $params['meta_value'] );
		$meta_values = array( $meta_value );
		// Merchant Sync stores spaces as plus, so we need to restore them.
		if ( strpos( $meta_value, ' ' ) > 0 ) {
			$meta_values[] = str_replace( ' ', '+', $meta_value );
		} elseif ( strpos( $meta_value, '+' ) > 0 ) {
			$meta_values[] = str_replace( '+', ' ', $meta_value );
		}

		$sql = "
			SELECT pm.post_id AS id, %s AS type
			 FROM {$wpdb->postmeta} pm
			 	INNER JOIN {$wpdb->posts} p
			 		ON p.ID = pm.post_id AND p.post_status = 'publish'
			 WHERE pm.meta_key = %s AND pm.meta_value IN ( '" . implode( "', '", array_map( 'esc_sql', $meta_values ) ) . "' )
			 UNION
			 SELECT term_id AS id, %s AS type
			 FROM {$wpdb->termmeta}
			 WHERE meta_key = %s AND meta_value = %s
			";

		$results = $wpdb->get_results(
			$wpdb->prepare(
			// The query uses an IN clause, we escape the single values.
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				$sql,
				Object_Type_Enum::POST,
				$meta_key,
				Object_Type_Enum::TERM,
				$meta_key,
				$meta_value
			)
		);

		$jsonld_service = $this->jsonld_service;

		$data = array_reduce(
			$results,
			function ( $carry, $result ) use ( $jsonld_service ) {
				$jsonld = $jsonld_service->get( $result->type, $result->id, Jsonld_Context_Enum::REST );

				return array_merge( $carry, $jsonld );
			},
			array()
		);

		return Jsonld_Response_Helper::to_response( $data );
	}

}
