<?php

/**
 * Registers the entity custom post type (from the *init* hook).
 */
function wordlift_register_custom_type_entity() {

    $labels = array(
        'name'               => _x('Entities', 'post type general name', 'wordlift'),
        'singular_name'      => _x('Entity',   'post type singular name', 'wordlift'),
        'add_new'            => _x('Add New',  'entity', 'wordlift'),
        'add_new_item'       => __('Add New Entity',     'wordlift'),
        'edit_item'          => __('Edit Entity',        'wordlift'),
        'new_item'           => __('New Entity',         'wordlift'),
        'all_items'          => __('All Entities',       'wordlift'),
        'view_item'          => __('View Entity',        'wordlift'),
        'search_items'       => __('Search Entities',    'wordlift'),
        'not_found'          => __('No entities found',  'wordlift'),
        'not_found_in_trash' => __('No entities found in the Trash', 'wordlift'),
        'parent_item_colon'  => '',
        'menu_name'          => 'Entities'
    );

    $args = array(
        'labels'        => $labels,
        'description'   => 'Holds our entities and entity specific data',
        'public'        => true,
        'menu_position' => 20, // after the pages menu.
        'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
        'has_archive'   => true,
    );

    register_post_type('entity', $args);
}

/**
 * Add the type taxonomy to the entity (from the *init* hook).
 */
function wordlift_taxonomies_entity() {

    $labels = array(
        'name'              => _x( 'Entity Types', 'taxonomy general name' ),
        'singular_name'     => _x( 'Entity Type', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Entity Types' ),
        'all_items'         => __( 'All Entity Types' ),
        'parent_item'       => __( 'Parent Entity Type' ),
        'parent_item_colon' => __( 'Parent Entity Type:' ),
        'edit_item'         => __( 'Edit Entity Type' ),
        'update_item'       => __( 'Update Entity Type' ),
        'add_new_item'      => __( 'Add New Entity Type' ),
        'new_item_name'     => __( 'New Entity Type' ),
        'menu_name'         => __( 'Entity Types' ),
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => false
    );

    register_taxonomy('entity_type', 'entity', $args );
}

/**
 * Adds the Entity URL box and the Entity SameAs box (from the hook *add_meta_boxes*).
 */
function wordlift_entity_url_box() {
    add_meta_box(
        'wordlift_entity_box',
        __( 'Entity URL', 'wordlift' ),
        'wordlift_entity_box_content',
        'entity',
        'normal',
        'high'
    );
}

/**
 * Displays the content of the entity URL box (called from the *entity_url* method).
 * @param WP_Post $post The post.
 */
function wordlift_entity_box_content($post) {
    wp_nonce_field('wordlift_entity_box', 'wordlift_entity_box_nonce');

    $value = get_post_meta( $post->ID, 'entity_url', true );

    echo '<label for="entity_url">' . __('entity-url-label', 'wordlift') . '</label>';
    echo '<input type="text" id="entity_url" name="entity_url" placeholder="enter a URL" value="' . esc_attr( $value ) . '" style="width: 100%;" />';

    $value = get_post_meta( $post->ID, 'entity_same_as', true);

    echo '<label for="entity_same_as">' . __('entity-same-as-label', 'wordlift') . '</label>';
    echo '<textarea style="width: 100%;" id="entity_same_as" name="entity_same_as" placeholder="Same As URL">' . esc_attr( $value ) . '</textarea>';

    $value = get_post_meta( $post->ID, 'entity_related_posts', true);

    echo '<label for="entity_related_posts">' . __('entity-related-posts-label', 'wordlift') . '</label>';
    echo '<textarea style="width: 100%;" id="entity_related_posts" name="entity_related_posts" placeholder="Related Posts">' . esc_attr( $value ) . '</textarea>';
}

/**
 * Saves the entity URL for the specified post ID (set via the *save_post* hook).
 * @param int $post_id The post ID.
 */
function wordlift_save_entity_custom_fields($post_id) {

    // Check if our nonce is set.
    if ( ! isset( $_POST['wordlift_entity_box_nonce'] ) )
        return $post_id;

    $nonce = $_POST['wordlift_entity_box_nonce'];

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $nonce, 'wordlift_entity_box' ) )
        return $post_id;

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;

    // Check the user's permissions.
    if ( 'page' == $_POST['post_type'] ) {

        if ( ! current_user_can( 'edit_page', $post_id ) )
            return $post_id;

    } else {

        if ( ! current_user_can( 'edit_post', $post_id ) )
            return $post_id;
    }

    // save the entity URL.
    $entity_url = $_POST['entity_url'];
    update_post_meta( $post_id, 'entity_url', $entity_url);

    // save the same as values.
    $entity_same_as = $_POST['entity_same_as'];
    update_post_meta( $post_id, 'entity_same_as', $entity_same_as);

    // save the same as values.
    $entity_related_posts = $_POST['entity_related_posts'];
    update_post_meta( $post_id, 'entity_related_posts', $entity_related_posts);

}

add_action('init', 'wordlift_register_custom_type_entity');
add_action('init', 'wordlift_taxonomies_entity', 0);
add_action('add_meta_boxes', 'wordlift_entity_url_box');
add_action('save_post', 'wordlift_save_entity_custom_fields');
