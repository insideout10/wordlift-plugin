<?php
/**
 * This file contains methods related to the Entity Type taxonomy.
 * The file admin/wordlift_admin_entity_type_taxonomy.php contains admin methods.
 */


/**
 * Add the type taxonomy to the entity (from the *init* hook).
 */
function wl_entity_type_taxonomy_register()
{

    $labels = array(
        'name' => _x('Entity Types', 'taxonomy general name'),
        'singular_name' => _x('Entity Type', 'taxonomy singular name'),
        'search_items' => __('Search Entity Types'),
        'all_items' => __('All Entity Types'),
        'parent_item' => __('Parent Entity Type'),
        'parent_item_colon' => __('Parent Entity Type:'),
        'edit_item' => __('Edit Entity Type'),
        'update_item' => __('Update Entity Type'),
        'add_new_item' => __('Add New Entity Type'),
        'new_item_name' => __('New Entity Type'),
        'menu_name' => __('Entity Types'),
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => false
    );

    register_taxonomy('wl_entity_type', 'entity', $args);
}

