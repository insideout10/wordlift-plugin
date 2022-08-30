<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://wordlift.io
 * @since      1.0.0
 *
 * @package    Wordlift
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Get a reference to WP and the Wordlift plugin
require_once 'wordlift.php';
global $wpdb;

/*
 * Delete entities and their meta.
 */

// Do a db search for posts of type entity
$args           = array(
	'posts_per_page' => - 1,
	'post_type'      => Wordlift_Entity_Service::TYPE_NAME,
	'post_status'    => array( 'publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash' ),
	'fields'         => 'ids',
);
$entities_array = get_posts( $args );

// Loop over entities and delete them.
// TODO: thumbnails?
wl_write_log( 'Deleting entities and their meta... ' );
wl_write_log( $entities_array );
foreach ( $entities_array as $entity_id ) {
	// Delete the whole entity and its metas.
	wp_delete_post( $entity_id, true );
}
wl_write_log( 'Done.' );

/*
 * Delete post-entity relationships
 */
wl_write_log( 'Deleting post-entity relationships... ' );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wl_relation_instances" );
delete_option( 'wl_db_version' );
wl_write_log( 'Done.' );

/*
 * Delete taxonomy
 */
wl_write_log( 'Cleaning entities taxonomy... ' );
// Delte custom taxonomy terms.
// We loop over terms in this rude way because in the uninstall script
// is not possible to call WP custom taxonomy functions.
foreach ( range( 0, 100 ) as $index ) {
	delete_option( Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME . '_' . $index );
	wp_delete_term( $index, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
}
delete_option( Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME . '_children' );  // it's a hierarchical taxonomy
wl_write_log( 'Done.' );

/**
 * Delete options
 */
wl_write_log( 'Cleaning WordLift options... ' );
// delete_option( WL_OPTIONS_NAME );
delete_option( 'wl_option_prefixes' );
delete_option( 'wl_general_settings' );
delete_option( 'wl_advanced_settings' );
wl_write_log( 'Done. WordLift successfully uninstalled.' );
