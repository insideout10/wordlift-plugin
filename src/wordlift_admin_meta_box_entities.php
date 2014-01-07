<?php

/**
 * Adds the entities meta box (called from *add_meta_boxes* hook).
 */
function wordlift_admin_add_entities_meta_box($post_type) {
    add_meta_box(
        'wordlift_entitities_box',
        __( 'Entities', 'wordlift' ),
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

    $pattern = '/<span class=\"textannotation[^\"]*\" id=\"[^\"]+\" itemid=\"([^\"]+)\"[^>]*><span itemprop=\"name\">([^<]+)<\/span>/i';

    $matches = array();
    $count   = preg_match_all ($pattern , $post->post_content, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        echo '<a href="' . $match[1] . '">' . esc_attr($match[2]) . '</a><br>';
    }
}

add_action('add_meta_boxes', 'wordlift_admin_add_entities_meta_box');
