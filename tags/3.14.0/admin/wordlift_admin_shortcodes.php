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
		add_action( 'admin_footer', 'wl_admin_inject_chord_dialog_dependencies' );
	}
}

///**
// * Registers the WordLift shortcodes plugin in TinyMCE.
// *
// * @param array $plugin_array An array of TinyMCE plugins.
// *
// * @return array The TinyMCE plugins array including WordLift shortcodes plugin.
// */
//function wl_admin_shortcode_buttons_register_tinymce_javascript( $plugin_array ) {
//
//	$plugin_array['wl_shortcodes'] = plugin_dir_url( __FILE__ ) . 'js/wordlift_shortcode_tinymce_plugin.js?ver=3.12.0';
//
//	return $plugin_array;
//}

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


/**
 * Loads the styles and scripts. Echoes the HTML dialog on the page.
 *
 * This method is called by the *admin_footer* hook.
 */
function wl_admin_inject_chord_dialog_dependencies() {

	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );
	wp_enqueue_style( 'jquery-ui-slider' );
	// Not included by default :|
	// TODO include jquery ui css from the plugin creates css issues on the slider
	// wp_enqueue_style('jquery-ui-css', plugins_url( 'css/jquery-ui/jquery-ui.min.css', __FILE__ ) );
	wp_enqueue_style( 'wp-jquery-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery.ui.all.css' );

	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-slider' );

}
