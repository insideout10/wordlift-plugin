<?php
/**
 * This file contains functions related to Redlink.
 */



/**
 * Execute a query on Redlink.
 *
 * @since 3.0.0
 *
 * @uses wl_queue_sparql_update_query to queue a query if query buffering is on.
 *
 * @param string $query The query to execute.
 * @param bool $queue Whether to queue the update.
 *
 * @return bool True if successful otherwise false.
 */
function rl_execute_sparql_update_query( $query, $queue = WL_ENABLE_SPARQL_UPDATE_QUERIES_BUFFERING ) {

	// Get the calling function for debug purposes.
	$callers          = debug_backtrace();
	$calling_function = $callers[1]['function'];
	wl_write_log( "[ calling function :: $calling_function ][ queue :: " . ( $queue ? 'true' : 'false' ) . ' ]' );

	// Queue the update query.
	if ( $queue ) {
		wl_queue_sparql_update_query( $query );

		return true;
	}

	// Get the update end-point.
	$url = wl_configuration_get_query_update_url();

	// Prepare the request.
	$args = array_merge_recursive( unserialize( WL_REDLINK_API_HTTP_OPTIONS ), array(
		'method'  => 'POST',
		'headers' => array(
			'Accept'       => 'application/json',
			'Content-type' => 'application/sparql-update; charset=utf-8'
		),
		'body'    => $query
	) );

	// Send the request.
	$response = wp_remote_post( $url, $args );

	// Remove the key from the query.
	if ( ! WP_DEBUG ) {
		$scrambled_url = preg_replace( '/key=.*$/i', 'key=<hidden>', $url );
	} else {
		$scrambled_url = $url;
	}

	// If an error has been raised, return the error.
	if ( is_wp_error( $response ) || 200 !== $response['response']['code'] ) {

		$body = ( is_wp_error( $response ) ? $response->get_error_message() : $response['body'] );

		wl_write_log( "rl_execute_sparql_update_query : error [ url :: $scrambled_url ][ args :: " );
		wl_write_log( "\n" . var_export( $args, true ) );
		wl_write_log( "[ response :: " );
		wl_write_log( "\n" . var_export( $response, true ) );
		wl_write_log( "][ body :: " );
		wl_write_log( "\n" . $body );
		wl_write_log( "]" );

		return false;
	}

	wl_write_log( "rl_execute_sparql_query [ url :: $scrambled_url ][ response code :: " . $response['response']['code'] . " ][ query :: " );
	wl_write_log( "\n" . $query );
	wl_write_log( "]" );

	return true;
}
