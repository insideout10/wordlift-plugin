<?php

namespace Wordlift\Jsonld;

use DateInterval;
use DateTime;
use DateTimeZone;
use WP_REST_Response;

class Jsonld_Endpoint {
	/**
	 * @var Wordlift_Jsonld_Service
	 */
	private $jsonld_service;

	/**
	 * Jsonld_Endpoint constructor.
	 *
	 * @param \Wordlift_Jsonld_Service $jsonld_service
	 */
	public function __construct( $jsonld_service ) {

		add_action( 'rest_api_init', function () {
			register_rest_route( WL_REST_ROUTE_DEFAULT_NAMESPACE, '/jsonld/(?P<id>\d+)', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'callback' ),
				'args'     => array(
					'id' => array(
						'validate_callback' => function ( $param, $request, $key ) {
							return is_numeric( $param );
						},
						'sanitize_callback' => 'absint',
					),
				)
			) );
		} );

		$this->jsonld_service = $jsonld_service;
	}

	public function callback( $request ) {

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

}