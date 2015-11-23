<?php
/**
 * This file contains methods related to the Entity Type taxonomy.
 */


/**
 * Add the type taxonomy to the entity (from the *init* hook).
 */
function wl_entity_type_taxonomy_register() {

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

	// Take away GUI for taxonomy editing.
	// TODO: readd capabilities when editing of the WL <-> schema.org mapping is possible
	$capabilities = array(
		'manage_terms' => null,
		'edit_terms'   => null,
		'delete_terms' => null,
		'assign_terms' => 'edit_posts'
	);

	$args = array(
		'labels'            => $labels,
		'capabilities'      => $capabilities,
		'hierarchical'      => true,
		'show_admin_column' => true
	);

	register_taxonomy( Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME, 'entity', $args );
	
	// Add filter to change the metabox CSS class
	add_filter( 'postbox_classes_entity_wl_entity_typediv', 'wl_admin_metaboxes_add_css_class' );
}

/**
 * Get the entity main type for the specified post ID.
 *
 * @param int $post_id The post ID
 *
 * @return array|null An array of type properties or null if no term is associated
 */
function wl_entity_type_taxonomy_get_type( $post_id ) {

	$terms = wp_get_object_terms( $post_id, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );

	if ( is_wp_error( $terms ) ) {
		// TODO: handle error
		return null;
	}

	// If there are not terms associated, return null.
	if ( 0 === count( $terms ) ) {
		return null;
	}

	// Return the entity type with the specified id.
	return Wordlift_Schema_Service::get_instance()->get_schema( $terms[0]->slug );
}

/**
 * Get the children types of given term.
 *
 * @param mixes $term Term ID (e.g. 12) or slug (e.g. 'creative-work') or name (e.g. 'CreativeWork').
 * @param string $by Search key. Must be one of: 'id', 'slug', 'name', or 'term_taxonomy_id'.
 */
function wl_entity_type_taxonomy_get_term_children( $term, $by = 'name' ) {
	// TODO: test this method
	// NOTE: WP taxonomy terms can have only one parent. This is a WP limit.

	$children_terms = array();

	$term = get_term_by( $by, $term, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );

	if ( isset( $term->term_id ) ) {

		$children_ids = get_term_children( $term->term_id, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );

		foreach ( $children_ids as $children_id ) {
			$children_terms[] = get_term( $children_id, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );
		}
	}

	return $children_terms;
}
