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
foreach( $entities_array as $entity_id ) {
    
    // Get metas defined for this entity
    $entity_metas = array_keys( get_post_meta( $entity_id ) );
    
    foreach( $entity_metas as $meta_name ) {
        
        if( in_array( $meta_name, $referenced_metas ) ) {
            $involved_posts = get_post_meta( $entity_id, $meta_name );
            if( is_array( $involved_posts ) && !empty( $involved_posts ) ){
                $referencing_posts_ids = array_merge( $referencing_posts_ids, $involved_posts );
            }
        }
        
        ///////////////////////////////////////////////
        //delete_post_meta( $entity_id, $meta_name );
        ////////////////////////////////////////////
    }
}

$referencing_posts_ids = array_unique( $referencing_posts_ids );
//var_dump($referencing_posts_ids);

/*
 * Delete ordinary posts' meta related to entities.
 * Clean also their content from annotations.
 */
foreach( $referencing_posts_ids as $post_id ) {
    ////////////////////////////////////////////////////////////
    //delete_post_meta( $post_id, WL_CUSTOM_FIELD_REFERENCED_ENTITIES );
    //delete_post_meta( $post_id, WL_CUSTOM_FIELD_WHAT_ENTITIES );
    //delete_post_meta( $post_id, WL_CUSTOM_FIELD_WHEN_ENTITIES );
    //delete_post_meta( $post_id, WL_CUSTOM_FIELD_WHERE_ENTITIES );
    //delete_post_meta( $post_id, WL_CUSTOM_FIELD_WHO_ENTITIES );
    /////////////////////////////////////////////////////////////
    
    
    // TODO: clean post content
    /*
    $post = get_post( $post_id );
    var_dump( $post->post_content );
    wp_update_post( array(
        'ID' => $post_id,
        'post_content' => 'A_' . $post->post_content
    ));
    
    
    $post = get_post( $post_id );
    var_dump( $post->post_content );
     */
}

/*
 * Delete taxonomy
 */

/**
 * Delete options
 */
var_dump( wl_entity_taxonomy_get_custom_fields() );
//delete_option('wl_entity_type_8');

exit('dunno if something happens from here on');