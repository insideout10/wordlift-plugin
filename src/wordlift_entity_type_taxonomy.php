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
        'hierarchical' => false,
        'show_admin_column' => true
    );

    register_taxonomy(WL_ENTITY_TYPE_TAXONOMY_NAME, 'entity', $args);
}

/**
 * Update an entity type with the provided data.
 * @param int $term_id The numeric term ID.
 * @param string $css_class The stylesheet class.
 * @param string $uri The URI.
 * @param array $same_as An array of sameAs URIs.
 * @param array $custom_fields An array of custom fields and their properties mapping.
 * @param array $templates
 * @param array $export_fields An array of fields to be exported to the triple store.
 * @param array $microdata_template A template string to print microdata on the frontend.
 * @return True if option value has changed, false if not or if update failed.
 */
function wl_entity_type_taxonomy_update_term($term_id, $css_class, $uri, $same_as = array(), $custom_fields = array(), $templates = array(), $export_fields = array(), $microdata_template = array() )
{
    wl_write_log("wl_entity_type_taxonomy_update_term [ term id :: $term_id ][ css class :: $css_class ][ uri :: $uri ][ same as :: " . implode(',', $same_as) . " ]");
    
    return update_option(WL_ENTITY_TYPE_TAXONOMY_NAME . "_$term_id", array(
        'css_class'     => $css_class,
        'uri'           => $uri,
        'same_as'       => $same_as,
        'custom_fields' => $custom_fields,
        'templates'     => $templates,
        'export_fields' => $export_fields,
        'microdata_template' => $microdata_template
    ) );
}

/**
 * Get the entity main type for the specified post ID.
 *
 * @see wl_entity_type_taxonomy_update_term for a list of keys in the returned array.
 *
 * @param int $post_id The post ID
 * @return array|null An array of type properties or null if no term is associated
 */
function wl_entity_get_type( $post_id ) {

    wl_write_log( "wl_entity_get_type [ post ID :: $post_id ]" );

    $terms = wp_get_object_terms( $post_id, WL_ENTITY_TYPE_TAXONOMY_NAME, array(
        'fields' => 'ids'
    ) );

    if ( is_wp_error($terms) ) {
        // TODO: handle error
        return null;
    }

    // If there are not terms associated, return null.
    if ( 0 === count( $terms ) ) {
        return null;
    }

    // Return the entity type with the specified id.
    return wl_entity_type_taxonomy_get_term_options($terms[0]);
}

/**
 * Get the data for the specified entity type (term id).
 * @param int $term_id A numeric term ID.
 * @return mixed|void The entity type data.
 */
function wl_entity_type_taxonomy_get_term_options($term_id)
{

    $term = get_option( WL_ENTITY_TYPE_TAXONOMY_NAME . "_$term_id" );

    wl_write_log( "wl_entity_type_taxonomy_get_term_options [ term :: " . var_export( $term , true ) . " ]" );

    return $term;
}
