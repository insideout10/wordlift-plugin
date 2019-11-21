<?php

class Wordlift_Mapping_Post_Type {
	public function init() {
		add_action( 'init', array( $this, 'register_mapping_post_type' ) );
		add_action( 'edit_form_after_title', array( $this, 'add_react_container_root' ) );
	}

	public function register_mapping_post_type() {
		$labels = array(
			'name'                  => _x( 'Mapping', 'Post type general name', 'textdomain' ),
			'singular_name'         => _x( 'Mapping', 'Post type singular name', 'textdomain' ),
			'menu_name'             => _x( 'Mapping', 'Admin Menu text', 'textdomain' ),
			'name_admin_bar'        => _x( 'Mapping', 'Add New on Toolbar', 'textdomain' ),
			'add_new'               => __( 'Add New', 'textdomain' ),
			'add_new_item'          => __( 'Add New mapping', 'textdomain' ),
			'new_item'              => __( 'New mapping', 'textdomain' ),
			'edit_item'             => __( 'Edit mapping', 'textdomain' ),
			'view_item'             => __( 'View mapping', 'textdomain' ),
			'all_items'             => __( 'All mapping', 'textdomain' ),
			'search_items'          => __( 'Search mapping', 'textdomain' ),
			'parent_item_colon'     => __( 'Parent mapping:', 'textdomain' ),
			'not_found'             => __( 'No mapping found.', 'textdomain' ),
			'not_found_in_trash'    => __( 'No mapping found in Trash.', 'textdomain' ),
			'featured_image'        => _x( 'Mapping Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
			'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
			'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
			'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
			'archives'              => _x( 'Mapping archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
			'insert_into_item'      => _x( 'Insert into mapping', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this mapping', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
			'filter_items_list'     => _x( 'Filter mapping list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
			'items_list_navigation' => _x( 'Mapping list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
			'items_list'            => _x( 'Mapping list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
		);
	 
		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'mapping' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title' ),
		);

		register_post_type( 'mapping', $args );
	}

	public function add_react_container_root() {
		echo '<div id="wl-mapping-root"></div>';
	}
}

$instance = new Wordlift_Mapping_Post_Type();
$instance->init();
