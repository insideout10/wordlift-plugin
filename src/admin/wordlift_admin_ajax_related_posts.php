<?php

function wordlift_ajax_related_posts() {
    
    // Extract filtering conditions
    $post_id = isset( $_GET["post_id"] ) ? intval( $_GET["post_id"] ) : 0;
    wl_write_log( "Going to find posts related to current with post id: $post_id ..." );
    
    $request_body = file_get_contents('php://input');
    $filtering_entity_uris = json_decode( $request_body );    
    $filtering_entity_ids = array();
    $related_posts = array();
                       
    foreach( $filtering_entity_uris as $uri) {
        wl_write_log( "Find entity with uri $uri ..." );
                   
        if ( $entity = wl_get_entity_post_by_uri( $uri ) ) {
            $entity = wl_get_entity_post_by_uri( $uri );
            array_push( $filtering_entity_ids, $entity->ID );
        }
    }

    wl_write_log( "Going to find posts related to the following entities ..." );
    
    if ( !empty( $filtering_entity_ids ) ) {
    
        $related_posts = wl_core_get_posts( array(
            'get'             =>    'posts',  
            'related_to__in'  =>    $filtering_entity_ids,
            'related_to__not' =>    $post_id,
            'post_type'       =>    'post', 
            'as'              =>    'object',
        ) );
        
        foreach ( $related_posts as $post_obj ) {
                
            $thumbnail = wp_get_attachment_url( get_post_thumbnail_id( $post_obj[ 'ID' ], 'thumbnail' ) );
            $post_obj[ 'thumbnail' ] = ( $thumbnail ) ? $thumbnail : WL_DEFAULT_THUMBNAIL_PATH;    
        }
    }

    // Get ready to fire a JSON
    header( 'Content-Type: application/json' );
    echo json_encode( $related_posts );

    die();
}

if ( is_admin() ) {
    add_action( 'wp_ajax_wordlift_related_posts', 'wordlift_ajax_related_posts' );
}