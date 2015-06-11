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

// We will keep track of ordinary posts that reference entities.
$referencing_posts_ids = array();
$referenced_metas = array(
    WL_CUSTOM_FIELD_IS_REFERENCED_BY_POSTS,
    WL_CUSTOM_FIELD_IS_WHAT_FOR_POSTS,
    WL_CUSTOM_FIELD_IS_WHEN_FOR_POSTS,
    WL_CUSTOM_FIELD_IS_WHERE_FOR_POSTS,
    WL_CUSTOM_FIELD_IS_WHO_FOR_POSTS
);

// Loop over entities and delete their meta.
// TODO: thumbnails?
echo 'Deleting entities and their meta... ';
foreach( $entities_array as $entity_id ) {
    
    // Get metas defined for this entity
    $entity_metas = array_keys( get_post_meta( $entity_id ) );
    
    // Loop over metas.
    foreach( $entity_metas as $meta_name ) {
        
        // Keep track of referencing posts.
        if( in_array( $meta_name, $referenced_metas ) ) {
            $involved_posts = get_post_meta( $entity_id, $meta_name );
            if( is_array( $involved_posts ) && !empty( $involved_posts ) ){
                $referencing_posts_ids = array_merge( $referencing_posts_ids, $involved_posts );
            }
        }
        
        // Actually delete the meta.
        delete_post_meta( $entity_id, $meta_name );
    }
    
    // Delete the whole entity.
    wp_delete_post( $entity_id, true);
}
echo 'Done.</br>';

$referencing_posts_ids = array_unique( $referencing_posts_ids );
//var_dump($referencing_posts_ids);

/*
 * Delete ordinary posts' meta related to entities.
 * Clean also their content from annotations.
 */
echo 'Cleaning ordinary posts from entities metadata... ';
foreach( $referencing_posts_ids as $post_id ) {
    delete_post_meta( $post_id, WL_CUSTOM_FIELD_REFERENCED_ENTITIES );
    delete_post_meta( $post_id, WL_CUSTOM_FIELD_WHAT_ENTITIES );
    delete_post_meta( $post_id, WL_CUSTOM_FIELD_WHEN_ENTITIES );
    delete_post_meta( $post_id, WL_CUSTOM_FIELD_WHERE_ENTITIES );
    delete_post_meta( $post_id, WL_CUSTOM_FIELD_WHO_ENTITIES );
    
    // TODO: clean post content... this is the major performance point.
    /*
    $post = get_post( $post_id );
    var_dump( $post->post_content );
    wp_update_post( array(
        'ID' => $post_id,
        'post_content' => 'A_' . $post->post_content    // call here the cleaning function
    ));
    
    
    $post = get_post( $post_id );
    var_dump( $post->post_content );
     */
}
echo 'Done.</br>';

/*
 * Delete taxonomy
 */
echo 'Cleaning entities taxonomy... ';
// Delte custom taxonomy terms.
// We loop over terms in this rude way because in the uninstall script
// is not possible to call WP custom taxonomy functions.
foreach ( range(0, 20) as $index ) {
    delete_option( WL_ENTITY_TYPE_TAXONOMY_NAME . '_' . $index );
    wp_delete_term( $index, WL_ENTITY_TYPE_TAXONOMY_NAME );
}
delete_option( WL_ENTITY_TYPE_TAXONOMY_NAME . '_children' );  // it's a hierarchical taxonomy
echo 'Done.</br>';

/**
 * Delete options
 */
echo 'Cleaning WordLift options... ';
delete_option( 'wl_option_prefixes' );
delete_option( 'wl_general_settings' );
delete_option( 'wl_advanced_settings' );
echo 'Done.</br>';
