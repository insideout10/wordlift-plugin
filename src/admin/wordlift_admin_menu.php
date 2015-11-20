<?php
/**
 * This file provides functions to add and configure the administration menu for WordPress.
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

	// see http://codex.wordpress.org/Function_Reference/add_utility_page
	add_utility_page(
		__( 'WordLift', 'wordlift' ), // page title
		__( 'WordLift', 'wordlift' ), // menu title
		$capability,                 // capabilities
		$menu_slug,                  // menu slug
		'wl_admin_menu_callback',    // function callback to draw the menu
		WP_CONTENT_URL . '/plugins/wordlift/images/pink-logo-20x20.gif' );  // icon URL 20x20 px
	
	// Call hooked functions.
	do_action( 'wl_admin_menu', $menu_slug, $capability );

}

add_action( 'admin_menu', 'wl_admin_menu' );

/**
 * This function is called as a callback by the *wl_admin_menu* to display the actual page.
 *
 * @since 3.0.0
 */
function wl_admin_menu_callback() {

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	echo '<div class="wrap">';
	echo '<p>Here is where the form would go if I actually had options.</p>';
	echo '</div>';

}