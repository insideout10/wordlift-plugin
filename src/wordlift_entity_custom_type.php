<?php

/**
 * Registers the entity custom post type.
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

add_action('init', 'wordlift_register_custom_type_entity');