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

    // find a suitable position for the menu.
    $position = 20; // after the pages menu.
    while (array_key_exists($position, $GLOBALS['menu'])) { $position++; };

    $args = array(
        'labels'        => $labels,
        'description'   => 'Holds our entities and entity specific data',
        'public'        => true,
        'menu_position' => $position,
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
 * Adds the Entity URL box (from the hook *add_meta_boxes*).
 */
function wordlift_entity_url_box() {
    add_meta_box(
        'wordlift_entity_url_box',
        __( 'Entity URL', 'wordlift' ),
        'wordlift_entity_url_box_content',
        'entity',
        'normal',
        'high'
    );
}

/**
 * Displays the content of the entity URL box (called from the *entity_url* method).
 * @param WP_Post $post The post.
 */
function wordlift_entity_url_box_content($post) {
    wp_nonce_field('wordlift_entity_url_box', 'wordlift_entity_url_box_content_nonce');

    $value = get_post_meta( $post->ID, 'entity_url', true );

    echo '<label for="entity_url"></label>';
    echo '<input type="text" id="entity_url" name="entity_url" placeholder="enter a URL" value="' . esc_attr( $value ) . '" style="width: 100%;" />';
}

/**
 * Saves the entity URL for the specified post ID (set via the *save_post* hook).
 * @param int $post_id The post ID.
 */
function wordlift_entity_url_box_save($post_id) {

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    if (!wp_verify_nonce( $_POST['wordlift_entity_url_box_content_nonce'], 'wordlift_entity_url_box'))
        return;

    if ( 'page' == $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_page', $post_id ) )
            return;
    } else {
        if ( !current_user_can( 'edit_post', $post_id ) )
            return;
    }
    $entity_url = $_POST['entity_url'];
    update_post_meta( $post_id, 'entity_url', $entity_url);
}

add_action('init', 'wordlift_register_custom_type_entity');
add_action('init', 'wordlift_taxonomies_entity', 0);
add_action('add_meta_boxes', 'wordlift_entity_url_box');
add_action('save_post', 'wordlift_entity_url_box_save');
