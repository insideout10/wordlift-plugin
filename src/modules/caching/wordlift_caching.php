<?php

/*
Plugin Name: WordLift Caching
Plugin URI: http://wordlift.it
Description: Provides caching of remote queries
Version: 3.0.0-SNAPSHOT
Author: InsideOut10
Author URI: http://www.insideout.io
License: APL
*/

/**
 * Perform a remote request and return the local copy if any.
 *
 * @since 3.0.0
 *
 * @uses wl_caching_hash to get the hash for a request.
 * @uses wl_caching_get_filename to check whether a request is cached.
 * @uses wl_caching_get to get a cached response.
 * @uses wl_caching_put to store a response in the cache.
 *
 * @param string $url   The remote URL.
 * @param array $args   The request parameters.
 * @param bool $refresh If true, a remote request will be made and the cache will be refreshed.
 * @param int $expires_in_seconds How many seconds the cache is valid.
 * @return array The remote response.
 */
function wl_caching_remote_request( $url, $args, $refresh = false, $expires_in_seconds = 3600 ) {

    // Create an hash of the request.
    $hash = wl_caching_hash( $url, $args );

    // If the document is cached, return the cached copy.
    if ( ! $refresh && false !== ( $response = wl_caching_get( $hash ) ) ) {
        return $response;
    }

    // Make the request, put the response in the cache and return it to the client.
    $response = wp_remote_request( $url, $args );

    // Cache only valid responses.
    if ( ! is_wp_error( $response ) && 200 === (int)$response['response']['code'] ) {
        wl_caching_put( $hash, $response, $expires_in_seconds );
    }

    return $response;

}

/**
 * Create an hash for the specified url and parameters.
 *
 * @since 3.0.0
 *
 * @param string $url  The remote URL.
 * @param string $args The request parameters.
 * @return string The hash.
 */
function wl_caching_hash( $url, $args ) {

    return hash( 'md5', $url ) . '-' . hash( 'md5', serialize( $args ) );

}


/**
 * Get the cache filename. If a file is found but is expired, it is deleted and false is returned.
 *
 * @since 3.0.0
 *
 * @uses wl_caching_get_temp_path to get the base cache filename.
 * @uses wl_caching_delete_file to delete the cached file.
 *
 * @param string $hash The hash.
 * @return string|false The cache filename or false if not found.
 */
function wl_caching_get_filename( $hash ) {

    $files = glob( wl_caching_get_temp_path( $hash ) . '_*' );
    if ( ! is_array( $files ) || 0 === sizeof( $files ) ) {
        return false;
    }

    // Get the filename.
    $filename = $files[0];

    // Return false if the filename doesn't conform.
    $matches = array();
    if ( 1 !== preg_match( '/_(\d+)$/', $filename, $matches ) ) {
        return false;
    }

    // echo 'time: ' . time() . ' > expires at: ' . (int)$matches[1] . "\n";

    // Delete the file and return false if it's expired.
    if ( time() >= (int)$matches[1] ) {
        wl_caching_delete_file( $filename );
        return false;
    }

    wl_write_log( "[ wl_caching ] Found a cached response [ filename :: $filename ]" );

    return $filename;

}


/**
 * Get a cached response for the specified hash.
 *
 * @since 3.0.0
 *
 * @uses wl_caching_get_filename
 * @uses wl_caching_get_temp_path
 *
 * @param string $hash The document hash
 * @return array|false The cached response or false if the document is not found in the cache.
 */
function wl_caching_get( $hash ) {

    if ( false !== ( $filename = wl_caching_get_filename( $hash ) ) ) {
        return json_decode( file_get_contents( $filename ), true );
    }

    return false;

}


/**
 * Stores a response with the specified hash.
 *
 * @since 3.0.0
 *
 * @param string $hash    The hash.
 * @param array $response The response.
 * @param int $expires_in_seconds How many seconds the cache is valid.
 */
function wl_caching_put( $hash, $response, $expires_in_seconds = 3600 ) {

    // Add the cached flag.
    $now                       = time();
    $expires_at                = $now + $expires_in_seconds;
    $response['wl_cached']     = true;
    $response['wl_created_at'] = $now;
    $response['wl_expires_at'] = $expires_at;


    // According to http://stackoverflow.com/questions/804045/preferred-method-to-store-php-arrays-json-encode-vs-serialize
    // the fastest serialization/deserialization function is json_encode/json_decode
    $filename = wl_caching_get_temp_path( $hash ) . '_' . $expires_at;
    $pathname = dirname( $filename );

    if ( ! file_exists( dirname( $pathname ) ) ) {
        mkdir($pathname, 0777, true);
    }

    file_put_contents( $filename, json_encode( $response ) );

}


/**
 * Get the base cache folder path.
 *
 * @since 3.0.0
 *
 * @return string The base cache folder path.
 */
function wl_caching_get_cache_folder() {

    $temp_dir = sys_get_temp_dir();
    $temp_dir .= ( '/' === substr( $temp_dir, -1, 1 ) ? '' : '/' ); // add a trailing slash
    return $temp_dir . 'wordlift.tmp/cache/';

}


/**
 * Return the full path of a temporary file for the specified hash.
 *
 * @since 3.0.0
 *
 * @uses wl_caching_get_cache_folder to get the cache folder.
 *
 * @param string $hash The hash.
 * @return string The full path to the file.
 */
function wl_caching_get_temp_path( $hash ) {

    // By chunking the hash we ensure we don't put too many files in the same folder
    return wl_caching_get_cache_folder() . chunk_split( substr( $hash, 0, 15 ), 3, '/' ) . $hash;

}


/**
 * Delete the cache file bound to the specified hash.
 *
 * @since 3.0.0
 * @uses wl_caching_get_filename to get the filename of the cache file.
 * @uses wl_caching_delete_file to delete the cache file.
 *
 * @param string $hash The hash file.
 */
function wl_caching_delete( $hash ) {

    // Delete all cached versions of the provided hash.
    while ( false !== ( $filename = wl_caching_get_filename( $hash ) ) ) {
        wl_caching_delete_file( $filename );
    }

}

/**
 * Delete a cache file.
 *
 * @since 3.0.0
 *
 * @param string $filename The cache file to delete.
 */
function wl_caching_delete_file( $filename ) {

    $cache_folder = wl_caching_get_cache_folder();

    if ( file_exists( $filename ) ) {
        unlink( $filename );
    }

    // Delete folders contained in the cache folder - rmdir is safe: it doesn't delete non-empty folders.
    while ( 0 === strpos( $filename = dirname( $filename ), $cache_folder ) ) {
        @rmdir( $filename );
    }

}