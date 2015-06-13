<?php

function wordlift_ajax_related_posts() {
    
    // Extract filtering conditions
    $request_body = file_get_contents('php://input');
    $filtering_entity_uris = json_decode( $request_body );    
    $filtering_entity_ids = array();
    $related_posts = array();
                   
    foreach( $filtering_entity_uris as $uri) {
               
        if ( $entity = wl_get_entity_post_by_uri( $uri ) ) {
            array_push( $filtering_entity_ids, $entity->ID );
        }
    }

    wl_write_log( "Going to find posts related to the following entities ...", $filtering_entity_ids );

    if ( !empty( $filtering_entity_ids ) ) {
        // TODO - Exclude the corrent post from $related_posts
        $query = new WP_Query();
        $related_posts = $query->query(
            array(
                'post_type' => 'post',
                'posts_per_page' =>-1,
                'meta_query' => array(
                'key' => WL_CUSTOM_FIELD_REFERENCED_ENTITIES,
                'value' => $filtering_entity_ids,
                'compare' => 'IN'
            )
            )
        );
        
        $default_thumbnail = plugins_url( 'js-client/slick/missing-image-150x150.png', __FILE__ );
        foreach ( $related_posts as $post_obj ) {
                
            $thumbnail = wp_get_attachment_url( get_post_thumbnail_id( $post_obj->ID, 'thumbnail' ) );
            $post_obj->thumbnail = ( $thumbnail ) ? $thumbnail : $default_thumbnail;    
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