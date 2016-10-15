<?php

/**
 * Receive some content, run a remote analysis task and return the results. The content is read from the body
 * input (php://input).
 *
 * @since 1.0.0
 *
 * @uses wl_analyze_content() to analyze the provided content.
 */
function wl_ajax_analyze_action() {
	
    if ( $analysis = wl_analyze_content( file_get_contents( "php://input" ) ) ) {
        header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );	        
        echo( $analysis );
        wp_die();
    }
	
    status_header( 500 );
    wp_send_json( __( 'An error occurred while request an analysis to the remote service. Please try again later.', 'wordlift' ) );

}

add_action( 'wp_ajax_wordlift_analyze', 'wl_ajax_analyze_action' );

/**
 * Analyze the provided content. The analysis will make use of the method *wl_ajax_analyze_action*
 * provided by the WordLift plugin.
 *
 * @since 1.0.0
 *
 * @uses wl_configuration_get_analyzer_url() to get the API for the analysis.
 *
 * @param string $content The content to analyze.
 *
 * @return string Returns null on failure, or the WP_Error, or a WP_Response with the response.
 */
function wl_analyze_content( $content ) {

	// Get the analyzer URL.
	$url = wl_configuration_get_analyzer_url();

	// Set the content type to the request content type or to text/plain by default.
	$content_type = $_SERVER['CONTENT_TYPE'] ?: 'text/plain';

	// Prepare the request.
	$args = array_merge_recursive( unserialize( WL_REDLINK_API_HTTP_OPTIONS ), array(
		'method'      => 'POST',
		'headers'     => array(
			'Accept'       => 'application/json',
			'Content-type' => $content_type
		),
		// we need to downgrade the HTTP version in this case since chunked encoding is dumping numbers in the response.
		'httpversion' => '1.0',
		'body'        => $content
	) );

	$response = wp_remote_post( $url, $args );

	// If an error has been raised, return the error.
	if ( is_wp_error( $response ) || 200 !== (int) $response['response']['code'] ) {

		$body = ( is_wp_error( $response ) ? $response->get_error_message() : $response['body'] );

		wl_write_log( "error [ url :: $url ][ args :: " );
		wl_write_log( var_export( $args, true ) );
		wl_write_log( '][ response :: ' );
		wl_write_log( "\n" . var_export( $response, true ) );
		wl_write_log( "][ body :: " );
		wl_write_log( "\n" . $body );
		wl_write_log( "]" );

		return null;
	}

	wl_write_log( "[ url :: $url ][ response code :: " . $response['response']['code'] . " ]" );

	return $response['body'];
}
