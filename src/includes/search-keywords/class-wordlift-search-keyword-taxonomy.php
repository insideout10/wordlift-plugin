<?php
/**
 * Taxonomies: Search Keyword Taxonomy.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/includes/search-keywords
 */

/**
 * Define the Wordlift_Search_Keyword_Taxonomy class.
 *
 * @since 3.20.0
 */
class Wordlift_Search_Keyword_Taxonomy {

	/**
	 * Create a Wordlift_Search_Keyword_Taxonomy instance.
	 *
	 * @since 3.20.0
	 */
	public function __construct() {

		// Register the taxonomy.
		add_action( 'init', array( $this, 'init' ) );

		// Add the menu entry.
		add_action( 'wl_admin_menu', array( $this, 'admin_menu' ), 30, 2 );

	}

	/**
	 * Register the taxonomy.
	 *
	 * @since 3.20.0
	 */
	public function init() {

		// Add new taxonomy, NOT hierarchical (like tags)
		$labels = array(
			'name'                       => _x( 'Search Keywords', 'taxonomy general name', 'wordlift' ),
			'singular_name'              => _x( 'Search Keyword', 'taxonomy singular name', 'wordlift' ),
			'search_items'               => __( 'Search Search Keywords', 'wordlift' ),
			'popular_items'              => __( 'Popular Search Keywords', 'wordlift' ),
			'all_items'                  => __( 'All Search Keywords', 'wordlift' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Search Keyword', 'wordlift' ),
			'update_item'                => __( 'Update Search Keyword', 'wordlift' ),
			'add_new_item'               => __( 'Add New Search Keyword', 'wordlift' ),
			'new_item_name'              => __( 'New Search Keyword Name', 'wordlift' ),
			'separate_items_with_commas' => __( 'Separate search keywords with commas', 'wordlift' ),
			'add_or_remove_items'        => __( 'Add or remove search keywords', 'wordlift' ),
			'choose_from_most_used'      => __( 'Choose from the most used search keywords', 'wordlift' ),
			'not_found'                  => __( 'No search keywords found.', 'wordlift' ),
			'menu_name'                  => __( 'Search Keywords', 'wordlift' ),
		);

		$args = array(
			'hierarchical'       => false,
			'labels'             => $labels,
			'show_ui'            => true,
			'show_tagcloud'      => false,
			'show_in_quick_edit' => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'wl_search_keywords' ),
			'public'             => false,
		);

		register_taxonomy( 'wl_search_keywords', null, $args );

	}

	/**
	 * Add the menu entry.
	 *
	 * @since 3.20.0
	 *
	 * @param string $menu_slug The WordLift parent menu slug.
	 * @param string $capability The capability.
	 */
	public function admin_menu( $menu_slug, $capability ) {

		add_submenu_page( $menu_slug, _x( 'Search Keywords', 'taxonomy general name', 'wordlift' ), _x( 'Search Keywords', 'taxonomy general name', 'wordlift' ), $capability, 'edit-tags.php?taxonomy=wl_search_keywords', null );

	}

}
