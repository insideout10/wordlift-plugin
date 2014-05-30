<?php

/**
 * This file contains admin methods for the *wl-chord* and *wl-timeline* shortcode.
 */


/**
 * Loads the buttons in TinyMCE.
 */
function wl_admin_shortcode_buttons()
{
    // Only add hooks when the current user has permissions AND is in Rich Text editor mode
    if ((current_user_can('edit_posts') || current_user_can('edit_pages')) && get_user_option('rich_editing')) {
        add_filter('mce_external_plugins', 'wl_admin_shortcode_buttons_register_tinymce_javascript');
        add_filter('mce_buttons', 'wl_admin_shortcode_register_buttons');
        add_action('admin_footer', 'wl_admin_inject_chord_dialog');
    }
}

/**
 * Load the TinyMCE editor plugin.
 *
 * @param $plugin_array
 * @return mixed
 */
function wl_admin_shortcode_buttons_register_tinymce_javascript($plugin_array)
{

    $plugin_array['wl_shortcodes'] = plugins_url('js-client/wordlift_shortcode_tinymce_plugin.js', __FILE__);
    return $plugin_array;
}

/**
 * Register the *wl-chord* and *wl-timeline* button.
 *
 * @param $buttons
 * @return mixed
 */
function wl_admin_shortcode_register_buttons($buttons)
{
	array_push($buttons, 'wl_shortcodes_menu');
    return $buttons;
}


/**
 * Loads the styles and scripts. Echoes the HTML dialog on the page.
 *
 * This method is called by the *admin_footer* hook.
 */
function wl_admin_inject_chord_dialog()
{

    // TODO: load the required styles in WordLift CSS. Fix.
    wp_enqueue_style('wp-jquery-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery.ui.all.css');
    //wp_enqueue_style('wp-jquery-ui-css');//, plugins_url('css-client/my.css', __FILE__));
	
    wp_enqueue_style('wp-color-picker');

    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-slider');
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_script('wl_chord_dialog', plugins_url('js-client/wordlift_chord_tinymce_dialog.js', __FILE__));
}

// init process for button control
add_action('admin_init', 'wl_admin_shortcode_buttons');
