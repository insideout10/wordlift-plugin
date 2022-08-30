<?php
/**
 * This file provides a trait to be used by the JSON-LD endpoints to add the correct headers, including caching and
 * JSON-LD media type.
 *
 * @since 3.26.0
 * @package Wordlift\Jsonld
 * @author David Riccitelli <david@wordlift.io>
 */

namespace Wordlift\Jsonld;

use DateInterval;
use DateTime;
use DateTimeZone;
use WP_REST_Response;

class Jsonld_Response_Helper {

	/**
	 * Converts the provided data into a {@link WP_REST_Response} by adding the required caching and content-type
	 * headers.
	 *
	 * @param mixed $data The output data.
	 *
	 * @return WP_REST_Response The {@link WP_REST_Response}.
	 */
	public static function to_response( $data ) {

		$response = new WP_REST_Response( $data );

		$cache_in_seconds = 86400;
		$date_timezone    = new DateTimeZone( 'GMT' );
		$date_now         = new DateTime( 'now', $date_timezone );
		$date_interval    = new DateInterval( "PT{$cache_in_seconds}S" );
		$expires          = $date_now->add( $date_interval )->format( 'D, j M Y H:i:s T' );

		$response->set_headers(
			array(
				'Content-Type'  => 'application/ld+json; charset=' . get_option( 'blog_charset' ),
				'Cache-Control' => "max-age=$cache_in_seconds",
				'Expires'       => $expires,
			)
		);

		return $response;
	}

}
