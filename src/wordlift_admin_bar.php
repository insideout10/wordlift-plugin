<?php

/**
 * Adds WordLift to the Admin bar.
 * @param WP_Admin_Bar $wp_admin_bar The existing admin bar.
 */
function wl_admin_bar($wp_admin_bar)
{

    if (!is_super_admin() || !is_admin_bar_showing()) {
        return;
    }

    $defaults = array(
        'href' => false,
        'parent' => false, // false for a root menu, pass the ID value for a sub-menu of that menu.
        'id' => 'wordlift', // defaults to a sanitized title value.
        'title' => '' // the title is replaced by an icon using stylesheets.
        // 'meta' => false // array of any of the following options: array( 'html' => '', 'class' => '', 'onclick' => '', 'target' => '', 'title' => '' );
    );

    $wp_admin_bar->add_menu($defaults);

    // Add a link to the WordLift settings page.
    $wp_admin_bar->add_menu(array(
        'parent' => 'wordlift',
        'id' => 'wordlift-settings',
        'title' => 'Settings',
        'href' => get_admin_url(null, 'options-general.php?page=wordlift')
    ));

    // Add a link to the WordLift web site.
    $wp_admin_bar->add_menu(array(
        'parent' => 'wordlift',
        'id' => 'wordlift-about',
        'title' => 'About WordLift',
        'href' => 'http://wordlift.it'
    ));
}

add_action('admin_bar_menu', 'wl_admin_bar', 100);
