<?php
/**
 * Provide http clients for service which requires making http requests.
 *
 * @since 1.0.0
 * @package Wordlift_Geo
 */

namespace Wordlift\Http;

/**
 * Define the Http_Client interface.
 *
 * @since 1.0.0
 */
interface Http_Client {

	/**
	 * Perform a `GET` operation to the specified `$url`.
	 *
	 * @param string $url The URL.
	 * @param array  $options An array of options to pass to WordPress {@link wp_remote_request} function, default: array().
	 *
	 * @return \WP_Error|array The response or WP_Error on failure.
	 * @since 1.0.0
	 */
	public function get( $url, $options = array() );

	/**
	 * Perform a request to the specified `$url`.
	 *
	 * @param string $url The URL.
	 * @param array  $options An array of options to pass to WordPress {@link wp_remote_request} function, default: array().
	 *
	 * @return \WP_Error|array The response or WP_Error on failure.
	 * @since 1.0.0
	 */
	public function request( $url, $options = array() );

}
