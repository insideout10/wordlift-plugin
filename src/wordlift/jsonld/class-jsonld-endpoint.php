<?php
/**
 * This file defines the JSON-LD endpoint.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @package Wordlift\Jsonld
 */

namespace Wordlift\Jsonld;

use DateInterval;
use DateTime;
use DateTimeZone;
use Wordlift_Jsonld_Service;
use WP_REST_Response;

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
	 * @param \Wordlift_Jsonld_Service $jsonld_service
	 * @param \Wordlift_Entity_Uri_Service $entity_uri_service
	 */
	public function __construct( $jsonld_service, $entity_uri_service ) {

		$this->jsonld_service     = $jsonld_service;
		$this->entity_uri_service = $entity_uri_service;

		// PHP 5.3 compatibility.
		$that = $this;
		add_action( 'rest_api_init', function () use ( $that ) {
			register_rest_route( WL_REST_ROUTE_DEFAULT_NAMESPACE, '/jsonld/(?P<id>\d+)', array(
				'methods'  => 'GET',
				'callback' => array( $that, 'jsonld_using_post_id' ),
				'args'     => array(
					'id' => array(
						'validate_callback' => function ( $param, $request, $key ) {
							return is_numeric( $param );
						},
						'sanitize_callback' => 'absint',
					),
				)
			) );

			register_rest_route( WL_REST_ROUTE_DEFAULT_NAMESPACE, '/jsonld/http/(?P<item_id>.*)', array(
				'methods'  => 'GET',
				'callback' => array( $that, 'jsonld_using_item_id' ),
			) );

			register_rest_route( WL_REST_ROUTE_DEFAULT_NAMESPACE, '/jsonld/(?P<post_type>.*)/(?P<post_name>.*)', array(
				'methods'  => 'GET',
				'callback' => array( $that, 'jsonld_using_get_page_by_path' ),
			) );

		} );

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
	 * @throws \Exception
	 */
	public function jsonld_using_post_id( $request ) {

		$post_id     = $request['id'];
		$is_homepage = ( 0 === $post_id );

		// Send the generated JSON-LD.
		$data     = $this->jsonld_service->get_jsonld( $is_homepage, $post_id );
		$response = new WP_REST_Response( $data );

		$cache_in_seconds = 86400;
		$date_timezone    = new DateTimeZone( 'GMT' );
		$date_now         = new DateTime( 'now', $date_timezone );
		$date_interval    = new DateInterval( "PT{$cache_in_seconds}S" );
		$expires          = $date_now->add( $date_interval )->format( 'D, j M Y H:i:s T' );

		$response->set_headers( array(
			'Content-Type'  => 'application/ld+json; charset=' . get_option( 'blog_charset' ),
			'Cache-Control' => "max-age=$cache_in_seconds",
			'Expires'       => $expires
		) );

		return $response;
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
	 * @throws \Exception
	 */
	public function jsonld_using_item_id( $request ) {

		$item_id = 'http://' . $request['item_id'];
		$post    = $this->entity_uri_service->get_entity( $item_id );

		if ( ! is_a( $post, 'WP_Post' ) ) {
			return new WP_REST_Response( esc_html( "$item_id not found." ), 404, array( 'Content-Type' => 'text/html' ) );
		}

		return $this->jsonld_using_post_id( array( 'id' => $post->ID, ) );
	}

	public function jsonld_using_get_page_by_path( $request ) {

		$post_name = $request['post_name'];
		$post_type = $request['post_type'];

		global $wpdb;

		$sql     = "
			SELECT ID
			FROM $wpdb->posts
			WHERE post_name = %s
			 AND post_type = %s
		";

		$post_id = $wpdb->get_var( $wpdb->prepare( $sql, $post_name, $post_type ) );

		if ( is_null( $post_id ) ) {
			return new WP_REST_Response( esc_html( "$post_name of type $post_type not found." ), 404, array( 'Content-Type' => 'text/html' ) );
		}

		return $this->jsonld_using_post_id( array( 'id' => $post_id, ) );
	}

}