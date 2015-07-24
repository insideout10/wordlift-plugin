<?php

// Plugin uninstall  routine.

// If uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

// Get a reference to WP and the Wordlift plugin
require_once 'wordlift.php';
global $wpdb;

/*
 * Delete entities and their meta.
 */

// Do a db search for posts of type entity
$args = array(
	'posts_per_page'   => -1,
	'post_type'        => WL_ENTITY_TYPE_NAME,
        'fields' => 'ids'
);
$entities_array = get_posts( $args );

// Loop over entities and delete them.
// TODO: thumbnails?
wl_write_log('Deleting entities and their meta... ');
foreach( $entities_array as $entity_id ) {    
    // Delete the whole entity and its metas.
    wp_delete_post( $entity_id, true);
}
wl_write_log('Done.');

/*
 * Delete post-entity relationships
 */
wl_write_log('Deleting post-entity relationships... ');
$sql = 'DROP TABLE IF_EXISTS ' . wl_core_get_relation_instances_table_name() . ';';
$wpdb->query( $sql );
delete_option( 'wl_db_version' );
wl_write_log('Done.');

/*
 * Delete taxonomy
 */
wl_write_log( 'Cleaning entities taxonomy... ');
// Delte custom taxonomy terms.
// We loop over terms in this rude way because in the uninstall script
// is not possible to call WP custom taxonomy functions.
foreach ( range(0, 20) as $index ) {
    delete_option( WL_ENTITY_TYPE_TAXONOMY_NAME . '_' . $index );
    wp_delete_term( $index, WL_ENTITY_TYPE_TAXONOMY_NAME );
}
delete_option( WL_ENTITY_TYPE_TAXONOMY_NAME . '_children' );  // it's a hierarchical taxonomy
wl_write_log('Done.');

/**
 * Delete options
 */
wl_write_log('Cleaning WordLift options... ');
delete_option( WL_OPTIONS_NAME );
delete_option( 'wl_option_prefixes' );
delete_option( 'wl_general_settings' );
delete_option( 'wl_advanced_settings' );
wl_write_log('Done. WordLift successfully uninstalled.');
