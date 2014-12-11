<?php

/**
 * Receive some content, run a remote analysis task and return the results. The content is read from the body
 * input (php://input).
 *
 * @since 1.0.0
 *
 * @uses wl_analyze_content to analyze the provided content.
 */
function wl_ajax_analyze_action() {

	echo( wl_analyze_content( file_get_contents( "php://input" ) ) );
	wp_die();

}

add_action( 'wp_ajax_wordlift_analyze', 'wl_ajax_analyze_action' );

/**
 * Analyze the provided content. The analysis will make use of the method *wl_ajax_analyze_action*
 * provided by the WordLift plugin.
 *
 * @since 1.0.0
 *
 * @uses wl_redlink_enhance_url to get the API for the analysis.
 *
 * @param string $content The content to analyze.
 *
 * @return string Returns null on failure, or the WP_Error, or a WP_Response with the response.
 */
function wl_analyze_content( $content ) {

	// Get the Redlink enhance URL.
	$url = wl_redlink_enhance_url();

	// Prepare the request.
	$args = array_merge_recursive( unserialize( WL_REDLINK_API_HTTP_OPTIONS ), array(
		'method'  => 'POST',
		'headers' => array(
			'Accept'       => 'application/json',
			'Content-type' => 'text/plain'
		),
		'body'    => $content
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

		return __( 'An error occurred while request an analysis to the remote service. Please try again later.', 'wordlift' );
	}

	// Remove the key from the query.
	$scrambled_url = preg_replace( '/key=.*$/i', 'key=<hidden>', $url );

	wl_write_log( "[ url :: $scrambled_url ][ response code :: " . $response['response']['code'] . " ]" );

	return $response['body'];
}

/**
 * Get the Redlink API enhance URL.
 *
 * @since 1.0.0
 *
 * @uses wl_config_get_application_key to get the application key.
 * @uses wl_config_get_analysis to get the analysis name.
 *
 * @return string The Redlink API enhance URL.
 */
function wl_redlink_enhance_url() {

	$wordlift_key = '';

	// If the WordLift Key is set, run the analysis on WordLift, otherwise use Redlink.
	if ( ! empty( $wordlift_key ) ) {
		return 'http://localhost:8080/analyses?key=' . $wordlift_key;
	}

	// remove configuration keys from here.
	$app_key       = wl_config_get_application_key();
	$analysis_name = wl_config_get_analysis();

	$ldpath = <<<EOF
        @prefix ex: <http://example.org/>;
        @prefix cal: <http://www.w3.org/2002/12/cal#>;
        @prefix gn: <http://www.geonames.org/ontology#>;
        @prefix lode: <http://linkedevents.org/ontology/>;
        @prefix vcard: <http://www.w3.org/2006/vcard/ns#>;
        vcard:locality = lode:atPlace/gn:name :: xsd:string;
EOF;

	return wl_config_get_api_url() . '/analysis/' . $analysis_name . '/enhance?key=' . $app_key .
	       '&enhancer.engines.dereference.ldpath=' . urlencode( $ldpath );
}

