<?php
/**
 */

function wordlift_admin_add_related_posts_meta_box() {
    add_meta_box(
        'wordlift_related_posts_box',
        __( 'Related Posts', 'wordlift' ),
        'wordlift_admin_related_posts_meta_box_content',
        'entity',
        'side',
        'high'
    );
}

/**
 * Displays the meta box contents (called by *add_meta_box* callback).
 * @param WP_Post $post The current post.
 */
function wordlift_admin_related_posts_meta_box_content($post) {

    $related_posts_ids = get_post_meta( $post->ID, 'wordlift_related_posts', true );

    // The Query
    $args             = array(
        'post_status' => 'any',
        'post__in'    => $related_posts_ids
    );
    $query         = new WP_Query( $args );
    $related_posts = $query->get_posts();

    foreach ($related_posts as $related_post) {
        echo( '<a href="' . get_edit_post_link( $related_post->ID) . '">' . $related_post->post_title . '</a><br>');
    }
}

add_action('add_meta_boxes', 'wordlift_admin_add_related_posts_meta_box');