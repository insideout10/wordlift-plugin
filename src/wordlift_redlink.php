<?php
/**
 * This file contains functions related to Redlink.
 */

/**
 * @since 3.0.0
 *
 * @uses  wl_queue_sparql_update_query() to queue a query if query buffering is on.
 *
 * @param string $query The query to execute.
 * @param bool   $queue Whether to queue the update.
 *
 * @return bool True if successful otherwise false.
 */
function rl_execute_sparql_update_query( $query, $queue = WL_ENABLE_SPARQL_UPDATE_QUERIES_BUFFERING ) {

	$log = Wordlift_Log_Service::get_logger( 'rl_execute_sparql_update_query' );

	if ( get_transient( 'DISABLE_ENTITY_PUSH' ) ) {
		return true;
	}

	// Queue the update query.
	if ( $queue ) {
		$log->debug( 'Queueing query...' );

		Wordlift_Sparql_Service::get_instance()->queue( $query );

		return true;
	}

	// Get the update end-point.
	$url = wl_configuration_get_query_update_url();

	// Prepare the request.
	$args = array_merge_recursive( unserialize( WL_REDLINK_API_HTTP_OPTIONS ), array(
		'method'  => 'POST',
		'headers' => array(
			'Accept'       => 'application/json',
			'Content-type' => 'application/sparql-update; charset=utf-8',
		),
		'body'    => $query,
	) );

	// Send the request.
	$response = wp_remote_post( $url, $args );

	// If an error has been raised, return the error.
	if ( is_wp_error( $response ) || 200 !== (int) $response['response']['code'] ) {

		$body = ( is_wp_error( $response ) ? $response->get_error_message() : $response['body'] );

		$log->debug( "rl_execute_sparql_update_query : error [ url :: $url ][ args :: " );
		$log->debug( "\n" . var_export( $args, true ) );
		$log->debug( "[ response :: " );
		$log->debug( "\n" . var_export( $response, true ) );
		$log->debug( "][ body :: " );
		$log->debug( "\n" . $body );
		$log->debug( "]" );

		return false;
	}

	if ( WP_DEBUG ) {
		global $wl_logger;
		$wl_logger->trace( "Query executed successfully [ query :: $query ]" );
	}

	return true;
}
