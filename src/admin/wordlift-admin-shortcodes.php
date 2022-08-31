<?php

/**
 * This file contains admin methods for the *wl_chord* and *wl_timeline* shortcode.
 */

/**
 * Loads the buttons in TinyMCE.
 */
function wl_admin_shortcode_buttons() {
	// Only add hooks when the current user has permissions AND is in Rich Text editor mode
	if ( ( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) && get_user_option( 'rich_editing' ) ) {
		// add_filter( 'mce_external_plugins', 'wl_admin_shortcode_buttons_register_tinymce_javascript' );
		add_filter( 'mce_buttons', 'wl_admin_shortcode_register_buttons' );
	}
}

/**
 * Register shortcodes menu.
 *
 * @param array $buttons An array of buttons.
 *
 * @return array The buttons array including the *wl_shortcodes_menu*.
 */
function wl_admin_shortcode_register_buttons( $buttons ) {
	array_push( $buttons, 'wl_shortcodes_menu' );

	return $buttons;
}

// init process for button control
add_action( 'admin_init', 'wl_admin_shortcode_buttons' );
