<?php
/**
 */

function wordlift_admin_add_related_posts_meta_box() {
    add_meta_box(
        'wordlift_related_posts_box',
        __( 'Referencing Posts', 'wordlift' ),
        'wordlift_admin_referencing_posts_meta_box_content',
        'entity',
        'side',
        'high'
    );
}

/**
 * Displays the meta box contents (called by *add_meta_box* callback).
 * @param WP_Post $post The current post.
 */
function wordlift_admin_referencing_posts_meta_box_content( $post ) {

    // get related posts.
    $posts = wl_get_referencing_posts( $post->ID );

    // there are no related posts.
    if ( 0 === count( $posts ) ) {
        _e( 'No referencing posts', 'wordlift' );
        return;
    }

    foreach ( $posts as $post_id ) {
        $referencing_post = get_post( $post_id );
        echo( '<a href="' . get_edit_post_link( $referencing_post->ID ) . '">' . $referencing_post->post_title . '</a><br>' );
    }
}

add_action('add_meta_boxes', 'wordlift_admin_add_related_posts_meta_box');