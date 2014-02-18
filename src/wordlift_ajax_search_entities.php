<?php

function wordlift_ajax_search() {
    ob_clean();

    $term = $_GET['term'];

    $args = array(
        'post_type'   => 'entity',
        'post_status' => 'any',
        's'           =>  $term
    );
    $posts = get_posts( $args );

    array_walk ( $posts, function( &$post, $key ) {
        $types       = '';
        $types_array = wordlift_get_entity_types( $post->ID );

        if ( $types_array ) {
            foreach( $types_array as $type ) {
                $types .= strtolower( $type->name ) . (empty($types) ? '' : ' ');
            }
        }

        $post = array(
            'id'       => "id-" . $post->ID,
            'label'    => $post->post_title,
            'value'    => $post->post_title,
            'types'    => $types
        );
    });

    echo json_encode( $posts );

    die();
}

if ( is_admin() ) {
    add_action( 'wp_ajax_wordlift_search', 'wordlift_ajax_search' );
}