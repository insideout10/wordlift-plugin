<?php

/**
 * Receive some content, run a remote analysis task and return the results. The content is read from the body
 * input (php://input).
 *
 * @since 1.0.0
 *
 * @uses  wl_analyze_content() to analyze the provided content.
 */
function wl_ajax_analyze_action() {

	try {
		if ( $analysis = wl_analyze_content( file_get_contents( "php://input" ) ) ) {
			header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
			echo( $analysis );
			wp_die();
		}

		status_header( 500 );
		wp_send_json( __( 'An error occurred while request an analysis to the remote service. Please try again later.', 'wordlift' ) );

	} catch ( Exception $e ) {
		wp_send_json_error( array(
			'code'    => $e->getCode(),
			'message' => $e->getMessage(),
		) );
	}

}

add_action( 'wp_ajax_wordlift_analyze', 'wl_ajax_analyze_action' );

/**
 * Analyze the provided content. The analysis will make use of the method *wl_ajax_analyze_action*
 * provided by the WordLift plugin.
 *
 * @since 1.0.0
 *
 * @uses  wl_configuration_get_analyzer_url() to get the API for the analysis.
 *
 * @param string $content The content to analyze.
 *
 * @return string Returns null on failure, or the WP_Error, or a WP_Response with the response.
 */
function wl_analyze_content( $content ) {

	// Get the analyzer URL.
	$url = wl_configuration_get_analyzer_url();

	// Set the content type to the request content type or to text/plain by default.
	$content_type = isset( $_SERVER['CONTENT_TYPE'] ) ? $_SERVER['CONTENT_TYPE'] : 'text/plain';

	// Prepare the request.
	$args = array_merge_recursive( unserialize( WL_REDLINK_API_HTTP_OPTIONS ), array(
		'method'      => 'POST',
		'headers'     => array(
			'Accept'       => 'application/json',
			'Content-type' => $content_type,
		),
		// we need to downgrade the HTTP version in this case since chunked encoding is dumping numbers in the response.
		'httpversion' => '1.0',
		'body'        => $content,
	) );

	$response = wp_remote_post( $url, $args );

	// If it's an error log it.
	if ( is_wp_error( $response ) ) {

		$message = "An error occurred while requesting an analysis to $url: {$response->get_error_message()}";

		Wordlift_Log_Service::get_logger( 'wl_analyze_content' )->error( $message );

		throw new Exception( $response->get_error_message(), $response->get_error_code() );
	}

	// Get the status code.
	$status_code = (int) $response['response']['code'];

	// If status code is OK, return the body.
	if ( 200 === $status_code ) {
		return $response['body'];
	}

	// Invalid request, e.g. invalid key.
	if ( 400 === $status_code ) {
		$error = json_decode( $response['body'] );

		throw new Exception( $error->message, $error->code );
	}

	// Another generic error.
	throw new Exception( "An error occurred.", $status_code );;
}
