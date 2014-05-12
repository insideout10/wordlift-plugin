<?php

function wordlift_ajax_search() {
    ob_clean();

    $request_params = json_decode( file_get_contents('php://input'), true );
    
    $args = array(
        'post_type'   => 'entity',
        'post_status' => 'any',
        's'           =>  $request_params['term']
    );
    $posts = get_posts( $args );

    array_walk ( $posts, function( &$post, $key ) {
        $post = wl_serialize_entity($post);
    });

    echo json_encode( $posts );

    die();
}

if ( is_admin() ) {
    add_action( 'wp_ajax_wordlift_search', 'wordlift_ajax_search' );
}