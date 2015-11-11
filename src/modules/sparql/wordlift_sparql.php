<?php

/*
Plugin Name: WordLift SPARQL Queries
Plugin URI: http://wordlift.it
Description: Supercharge your WordPress Site with Smart Tagging and #Schemaorg support - a brand new way to write, organise and publish your contents to the Linked Data Cloud.
Version: 3.0.0-SNAPSHOT
Author: InsideOut10
Author URI: http://www.insideout.io
License: APL
*/

// Include constants.
require_once( 'wordlift_sparql_constants.php' );

// Include the SPARQL Query entity type.
require_once( 'wordlift_sparql_query_post_type.php' );

// Include the SPARQL Query metabox.
require_once( 'wordlift_sparql_meta_box.php' );

// Include the SPARQL Ajax.
require_once( 'wordlift_sparql_ajax.php' );


/**
 * Get the SPARQL Query associated with the SPARQL Query post with the specified slug.
 *
 * @param string $slug The post name.
 *
 * @return string The SPARQL Query or an empty string if not found.
 */
function wl_sparql_get_query_by_slug( $slug ) {

	$posts = get_posts( array(
		'name'        => $slug,
		'post_type'   => WL_SPARQL_QUERY_POST_TYPE,
		'post_status' => 'any',
		'numberposts' => 1
	) );

	return ( $posts ? wl_sparql_get_query_by_post_id( $posts[0]->ID ) : "" );

}

/**
 * Get the SPARQL Query dataset to use for a SPARQL query.
 *
 * @since 3.0.0
 *
 * @param string $slug The post slug.
 *
 * @return string The dataset name.
 */
function wl_sparql_get_query_dataset_by_slug( $slug ) {

	$posts = get_posts( array(
		'name'        => $slug,
		'post_type'   => WL_SPARQL_QUERY_POST_TYPE,
		'post_status' => 'any',
		'numberposts' => 1
	) );

	return ( $posts ? wl_sparql_get_query_dataset_by_post_id( $posts[0]->ID ) : "" );

}


/**
 * Get the SPARQL Query associated with the SPARQL Query post with the specified Id.
 *
 * @since 3.0.0
 *
 * @param int $post_id The post Id.
 *
 * @return string The SPARQL Query or an empty string if not found.
 */
function wl_sparql_get_query_by_post_id( $post_id ) {

	return get_post_meta( $post_id, WL_SPARQL_QUERY_META_KEY, true );

}

/**
 * Get the SPARQL Query dataset to use for a SPARQL query.
 *
 * @since 3.0.0
 *
 * @param int $post_id The post Id.
 *
 * @return string The dataset name.
 */
function wl_sparql_get_query_dataset_by_post_id( $post_id ) {

	return get_post_meta( $post_id, WL_SPARQL_QUERY_DATASET_META_KEY, true );

}