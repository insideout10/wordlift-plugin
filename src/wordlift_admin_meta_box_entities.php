<?php

/**
 * Adds the entities meta box (called from *add_meta_boxes* hook).
 */
function wordlift_admin_add_entities_meta_box($post_type) {
    add_meta_box(
        'wordlift_entitities_box',
        __( 'Related Entities', 'wordlift' ),
        'wordlift_entities_box_content',
        $post_type,
        'side',
        'high'
    );
}

/**
 * Displays the meta box contents (called by *add_meta_box* callback).
 * @param WP_Post $post The current post.
 */
function wordlift_entities_box_content($post) {

    $related_entities_ids = get_post_meta( $post->ID, 'wordlift_related_entities', true );

    // The Query
    $args             = array(
        'post_status' => 'any',
        'post__in'    => $related_entities_ids,
        'post_type'   => 'entity'
    );
    $query            = new WP_Query( $args );
    $related_entities = $query->get_posts();

    foreach ( $related_entities as $related_entity ) {
        echo( '<a href="' . get_edit_post_link( $related_entity->ID) . '">' . $related_entity->post_title . '</a><br>');
    }

}

add_action('add_meta_boxes', 'wordlift_admin_add_entities_meta_box');
