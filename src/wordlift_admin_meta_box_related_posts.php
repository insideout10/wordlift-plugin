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

    // get related posts.
    $related_posts = wordlift_get_related_posts( $post->ID );

    // there are no related posts.
    if ( 0 === count( $related_posts ) ) {
        _e('No related posts', 'wordlfift');
        return;
    }

    foreach ($related_posts as $related_post) {
        echo( '<a href="' . get_edit_post_link( $related_post->ID) . '">' . $related_post->post_title . '</a><br>');
    }
}

/**
 * Get an array of posts related to the specified post id.
 * @param int    $post_id     The post ID.
 * @param string $post_status The post status, by default 'published'.
 * @return array An array of related posts (or an empty array).
 */
function wordlift_get_related_posts( $post_id, $post_status = 'published' ) {

    // get related posts.
    $related_posts_ids = get_post_meta( $post_id, 'wordlift_related_posts', true );

    // there are no related posts.
    if ( !is_array( $related_posts_ids ) || 0 === count( $related_posts_ids ) ) {
        return array();
    }

    // The Query
    $args             = array(
        'post_type'   => 'any',
        'post_status' => $post_status,
        'post__in'    => $related_posts_ids
    );
    $query         = new WP_Query( $args );
    return $query->get_posts();
}

add_action('add_meta_boxes', 'wordlift_admin_add_related_posts_meta_box');