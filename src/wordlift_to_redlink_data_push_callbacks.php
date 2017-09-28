<?php
/**
 * This file contains functions related to Redlink.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift
 */

/**
 * Get a string representing the NS prefixes for a SPARQL query.
 *
 * @return string The PREFIX lines.
 */
function rl_sparql_prefixes() {

	$prefixes = '';
	foreach ( wl_prefixes() as $prefix => $uri ) {
		$prefixes .= "PREFIX $prefix: <$uri>\n";
	}

	return $prefixes;
}

/**
 * Reindex Redlink triple store, enabling local entities to be found in future analyses.
 */
function wordlift_reindex_triple_store() {

	// If entity push is disabled, return.
	if ( get_transient( 'DISABLE_ENTITY_PUSH' ) ) {
		return true;
	}

	// Get the reindex URL.
	$url = wl_configuration_get_dataset_index_url();

	// Post the request.
	// wl_write_log( "wordlift_reindex_triple_store" );

	// Prepare the request.
	$args = array_merge_recursive( unserialize( WL_REDLINK_API_HTTP_OPTIONS ), array(
		'method'  => 'POST',
		'headers' => array(),
	) );

	$response = wp_remote_request( $url, $args );

	// If an error has been raised, return the error.
	if ( is_wp_error( $response ) || 200 !== $response['response']['code'] ) {

		$body = ( is_wp_error( $response ) ? $response->get_error_message() : $response['body'] );

		wl_write_log( "wordlift_reindex_triple_store : error [ url :: $url ][ args :: " );
		wl_write_log( "\n" . var_export( $args, true ) );
		wl_write_log( "[ response :: " );
		wl_write_log( "\n" . var_export( $response, true ) );
		wl_write_log( "][ body :: " );
		wl_write_log( "\n" . $body );
		wl_write_log( "]" );

		return false;
	}

	return true;
}
