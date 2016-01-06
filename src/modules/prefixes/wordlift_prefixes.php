<?php
/*
Plugin Name: WordLift Prefixes
Plugin URI: http://wordlift.it
Description: Supercharge your WordPress Site with Smart Tagging and #Schemaorg support - a brand new way to write, organise and publish your contents to the Linked Data Cloud.
Version: 3.0.0-SNAPSHOT
Author: InsideOut10
Author URI: http://www.insideout.io
License: APL
*/

require_once( 'class-wl-prefixes-list-table.php' );

/**
 * Add the specified prefix / namespace.
 *
 * @since 3.0.0
 *
 * @param string $prefix The prefix
 * @param string $namespace The namespace
 */
function wl_prefixes_add( $prefix, $namespace ) {

	// Get the items, ensure that the current $prefix is not there.
	$items = wl_prefixes_delete( $prefix );

	array_push( $items, array( 'prefix' => $prefix, 'namespace' => $namespace ) );
	update_option( 'wl_option_prefixes', $items );

}


/**
 * Delete the specified prefix.
 *
 * @see 3.0.0
 *
 * @param string $prefix The prefix to delete.
 *
 * @return array The updated prefixes array.
 */
function wl_prefixes_delete( $prefix ) {

	$items = get_option( 'wl_option_prefixes', array() );

	// Ensure $items is an array.
	if ( ! is_array( $items ) ) {
		$items = array();
	}

	foreach ( $items as $key => $item ) {
		if ( $prefix === $item['prefix'] ) {
			unset ( $items[ $key ] );
		}
	}
	update_option( 'wl_option_prefixes', $items );

	return $items;

}


/**
 * Get the list of prefixes.
 *
 * @since 3.0.0
 *
 * @return array An array of prefixes, each made of a *prefix* and *namespace* key-values.
 */
function wl_prefixes_list() {

	// If the parameter is false, default prefixes have never been installed.
	if ( false === ( $prefixes = get_option( 'wl_option_prefixes' ) ) ) {

		$prefixes = array(
			array( 'prefix' => 'geo', 'namespace' => 'http://www.w3.org/2003/01/geo/wgs84_pos#' ),
			array( 'prefix' => 'dct', 'namespace' => 'http://purl.org/dc/terms/' ),
			array( 'prefix' => 'rdfs', 'namespace' => 'http://www.w3.org/2000/01/rdf-schema#' ),
			array( 'prefix' => 'owl', 'namespace' => 'http://www.w3.org/2002/07/owl#' ),
			array( 'prefix' => 'schema', 'namespace' => 'http://schema.org/' ),
                        array( 'prefix' => 'xsd', 'namespace' => 'http://www.w3.org/2001/XMLSchema#' )
		);
		add_option( 'wl_option_prefixes', $prefixes );
	}

	return $prefixes;

}

/**
 * Compacts the provided URI by replacing the namespaces with prefixes.
 *
 * @since 3.0.0
 *
 * @param string $uri The uri to compact
 *
 * @return string The compacted uri.
 */
function wl_prefixes_compact( $uri ) {

	foreach ( wl_prefixes_list() as $prefix ) {
		if ( 0 === strpos( $uri, $prefix['namespace'] ) ) {
			// Return the URI with the prefix.
			return $prefix['prefix'] . ':' . substr( $uri, strlen( $prefix['namespace'] ) );
		}
	}

	// Return the normal URI.
	return $uri;

}


/**
 * Expands the provided URI by replacing the prefixes with namespaces.
 *
 * @since 3.0.0
 *
 * @param string $uri The uri to expand
 *
 * @return string The expanded uri.
 */
function wl_prefixes_expand( $uri ) {

	foreach ( wl_prefixes_list() as $prefix ) {
		if ( 0 === strpos( $uri, $prefix['prefix'] . ':' ) ) {
			// Return the URI with the prefix.
			return $prefix['namespace'] . substr( $uri, strlen( $prefix['prefix'] ) + 1 );
		}
	}

	// Return the normal URI.
	return $uri;

}

/**
 * Get the namespace for a prefix.
 *
 * @since 3.0.0
 *
 * @param string $prefix
 *
 * @return string|false The namespace or false if not found.
 */
function wl_prefixes_get( $prefix ) {

	// Get the namespace.
	foreach ( wl_prefixes_list() as $item ) {
		if ( $prefix === $item['prefix'] ) {
			return $item['namespace'];
		}
	}

	// Return false if the prefix is not found.
	return false;

}

