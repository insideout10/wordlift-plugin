<?php

/**
 * Registers the entity custom post type (from the *init* hook).
 */
function wl_sparql_query_post_type_register() {

	$labels = array(
		'name'               => _x( 'SPARQL Queries', 'as shown in the admin menu', 'wordlift' ),
		'singular_name'      => _x( 'SPARQL Query', 'post type singular name', 'wordlift' ),
		'add_new'            => _x( 'Add New SPARQL Query', 'entity', 'wordlift' ),
		'add_new_item'       => __( 'Add New SPARQL Query', 'wordlift' ),
		'edit_item'          => __( 'Edit SPARQL Query', 'wordlift' ),
		'new_item'           => __( 'New SPARQL Query', 'wordlift' ),
		'all_items'          => __( 'All SPARQL Queries', 'wordlift' ),
		'view_item'          => __( 'View SPARQL Query', 'wordlift' ),
		'search_items'       => __( 'Search in SPARQL Queries', 'wordlift' ),
		'not_found'          => __( 'No SPARQL Queries found', 'wordlift' ),
		'not_found_in_trash' => __( 'No SPARQL Queries found in the Trash', 'wordlift' ),
		'parent_item_colon'  => '',
		'menu_name'          => __( 'SPARQL Queries', 'wordlift' )
	);

	$args = array(
		'labels'              => $labels,
		'description'         => 'SPARQL Queries',
		'show_in_nav_menus'   => true,
		'show_ui'             => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => false,
		'menu_position'       => 20, // after the pages menu.
		'menu_icon'           => WP_CONTENT_URL . '/plugins/wordlift/images/sparql-20x20.png',  // icon URL 20x20 px
		'supports'            => array( 'title', 'excerpt', 'comments' ),
		'has_archive'         => true/*,
        'taxonomies' => array('category')*/
	);

	register_post_type( WL_SPARQL_QUERY_POST_TYPE, $args );
}

add_action( 'init', 'wl_sparql_query_post_type_register' );