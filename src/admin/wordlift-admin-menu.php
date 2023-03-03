<?php
/**
 * This file provides functions to add and configure the administration menu for WordPress.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * This function is called by the *admin_menu* hook to create and configure the WordLift administration menu. It raises
 * the *wl_admin_menu* action to have modules add their own submenu.
 *
 * @since 3.0.0
 */
function wl_admin_menu() {

	$menu_slug  = 'wl_admin_menu';
	$capability = 'manage_options';

	// see http://codex.wordpress.org/Function_Reference/add_menu_page
	add_menu_page(
		__( 'WordLift', 'wordlift' ), // page title
		__( 'Dashboard', 'wordlift' ), // menu title
		$capability,                 // capabilities
		$menu_slug,                  // menu slug
		'__wl_dashboard__main',  // TODO: function callback to draw the coming dashboard
		'wl_configuration_admin_menu_callback',
		WP_CONTENT_URL . '/plugins/wordlift/images/svg/wl-logo-icon.svg'
	);  // icon URL 20x20 px

	// Call hooked functions.
	do_action( 'wl_admin_menu', $menu_slug, $capability );

	// Remove duplicate 'WordLift' subpage created by WordPress.
	remove_submenu_page( $menu_slug, $menu_slug );

}

add_action( 'admin_menu', 'wl_admin_menu' );

/**
 * This function is called by the *admin_menu* hook to remove for the admin menu
 * links to the entity type admin page from the menu hierarchy of all post types
 * which are not the entity post type one.
 *
 * @since 3.15.0
 */
function wl_remove_entity_type_menu() {
	/*
	 * Remove from the menu links to the entity type admin page when
	 * under non entity hierarchy.
	 */
	foreach ( Wordlift_Entity_Service::valid_entity_post_types() as $post_type ) {
		// In the context of admin menues post has no explicit indication of post type in the urls.
		if ( 'post' !== $post_type ) {
			remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=' . Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
		}

		if ( Wordlift_Entity_Service::TYPE_NAME !== $post_type ) {
			remove_submenu_page( 'edit.php?post_type=' . $post_type, 'edit-tags.php?taxonomy=' . Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME . '&amp;post_type=' . $post_type );
		}
	}
}

add_action( 'admin_menu', 'wl_remove_entity_type_menu', 100 );

/**
 * This function is called as a callback by the *wl_admin_menu* to display the actual page.
 *
 * @since 3.0.0
 */
function wl_admin_menu_callback() {

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'default' ) );
	}

	echo '<div class="wrap">';
	echo '<p>Here is where the form would go if I actually had options.</p>';
	echo '</div>';

}
