<?php

function wordlift_ajax_search() {
    ob_clean();
    $args = array(
        'post_type'        => 'entity',
        'post_status'      => 'any'
    );
    $posts = get_posts( $args );

    array_walk ( $posts, function( &$post, $key ) {
        $post = array(
            'id'       => "id-" . $post->ID,
            'label'    => $post->post_title,
            'value'    => $post->post_title,
        );
    });

    echo json_encode( $posts );

    die();
}

if ( is_admin() ) {
    add_action( 'wp_ajax_wordlift_search', 'wordlift_ajax_search' );
}