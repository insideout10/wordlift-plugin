<?php

/**
 * The Entity Types Taxonomy service.
 *
 * @since 3.1.0
 */
class Wordlift_Entity_Type_Taxonomy_Service {

	// @@todo: rename using singular.

	const TAXONOMY_NAME = 'wl_entity_type';

	/**
	 * Register the taxonomies.
	 *
	 * @since 3.18.0
	 */
	public function init() {

		$labels = array(
			'name'              => _x( 'Entity Types', 'taxonomy general name', 'wordlift' ),
			'singular_name'     => _x( 'Entity Type', 'taxonomy singular name', 'wordlift' ),
			'search_items'      => __( 'Search Entity Types', 'wordlift' ),
			'all_items'         => __( 'All Entity Types', 'wordlift' ),
			'parent_item'       => __( 'Parent Entity Type', 'wordlift' ),
			'parent_item_colon' => __( 'Parent Entity Type:', 'wordlift' ),
			'edit_item'         => __( 'Edit Entity Type', 'wordlift' ),
			'update_item'       => __( 'Update Entity Type', 'wordlift' ),
			'add_new_item'      => __( 'Add New Entity Type', 'wordlift' ),
			'new_item_name'     => __( 'New Entity Type', 'wordlift' ),
			'menu_name'         => __( 'Entity Types', 'wordlift' ),
		);

		// Take away GUI for taxonomy editing.
		// TODO: read capabilities when editing of the WL <-> schema.org mapping is possible.
		$capabilities = array(
			// We enable editors to change the title/description of terms:
			//
			// See https://github.com/insideout10/wordlift-plugin/issues/398
			'manage_terms' => 'manage_options',
			'edit_terms'   => 'wl_entity_type_edit_term',
			'delete_terms' => 'wl_entity_type_delete_term',
			'assign_terms' => 'edit_posts',
		);

		$args = array(
			'labels'             => $labels,
			'capabilities'       => $capabilities,
			'hierarchical'       => true,
			'show_admin_column'  => true,
			'show_in_quick_edit' => false,
			'meta_box_cb'        => array( 'Wordlift_Admin_Schemaorg_Taxonomy_Metabox', 'render' ),
		);

		register_taxonomy(
			Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, // Taxonomy name.
			Wordlift_Entity_Service::valid_entity_post_types(), // Taxonomy post types.
			$args // Taxonomy args.
		);

		// Add filter to change the metabox CSS class
		add_filter( 'postbox_classes_entity_wl_entity_typediv', 'wl_admin_metaboxes_add_css_class' );
	}

}
