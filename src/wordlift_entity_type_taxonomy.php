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

/**
 * Update an entity type with the provided data.
 * @param int $term_id The numeric term ID.
 * @param string $css_class The stylesheet class.
 * @param string $uri The URI.
 * @param array $same_as An array of sameAs URIs.
 * @return True if option value has changed, false if not or if update failed.
 */
function wl_entity_type_taxonomy_update_term($term_id, $css_class, $uri, $same_as = array())
{
    write_log("wl_entity_type_taxonomy_update_term [ term id :: $term_id ][ css class :: $css_class ][ uri :: $uri ][ same as :: " . implode(',', $same_as) . " ]");

    return update_option("wl_entity_type_${term_id}", array(
        'css_class' => $css_class,
        'uri' => $uri,
        'same_as' => $same_as
    ));
}

