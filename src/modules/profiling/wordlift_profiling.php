<?php

/*
Plugin Name: WordLift Profiling
Plugin URI: http://wordlift.it
Description: Profiles remote SPARQL queries.
Version: 3.0.0-SNAPSHOT
Author: InsideOut10
Author URI: http://www.insideout.io
License: APL
*/


/**
 * Records the execution of a query.
 *
 * @since 3.0.0
 *
 * @param string $url  The remote URL.
 * @param string $args The request parameters.
 */
function wl_profiling_sparql_pre_request( $url, $args ) {

    global $wl_profiling_started_at;
    $wl_profiling_started_at = round(microtime(true) * 1000);

}
add_action( 'wl_sparql_pre_request', 'wl_profiling_sparql_pre_request', 10, 2 );

/**
 * Records the end of the execution of a query.
 *
 * @since 3.0.0
 *
 * @uses wl_caching_is_response_cached to determine if the response is cached. Cached responses are ignored.
 *
 * @param string $url     The remote URL.
 * @param string $args    The request parameters.
 * @param array $response The response.
 */
function wl_profiling_sparql_post_request( $url, $args, $response ) {

    global $wl_profiling_started_at;

    // Ignore cached calls.
    if ( function_exists( 'wl_caching_is_response_cached' ) && wl_caching_is_response_cached( $response ) ) {
        return;
    }

    $interval = round(microtime(true) * 1000) - $wl_profiling_started_at;
    wl_write_log( '[ wl_profiling ] Query took ' . $interval . ' ms.' );

}
add_action( 'wl_sparql_post_request', 'wl_profiling_sparql_post_request', 10, 3 );
