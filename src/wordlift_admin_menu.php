<?php

/**
 * Adds the entities entry in the admin menu (hook from the *admin_menu*).
 */
function wordlift_admin_menu_entities() {

    // find a suitable position for the menu.
    $position = 20; // after the pages menu.
    while (array_key_exists($position, $GLOBALS['menu'])) { $position++; };

    // 1: page title
    // 2: menu title
    // 3: required capability
    // 4: unique id
    // 5: callback used to display the page content.
    // 6: icon url
    // 7: position
    add_menu_page(
        __('entities-admin-page-title', 'wordlift'),
        __('entities-admin-menu-title', 'wordlift'),
        'manage_options',
        'wordlift-admin-entities',
        'wordlift_admin_entities_page',
        null,
        $position
    );
}

/**
 * Displays the entities page (this is a callback set from the *wordlift_admin_menu_entities* method).
 */
function wordlift_admin_entities_page() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    echo '<div class="wrap">';
    echo '<p>Here is where the form would go if I actually had options.</p>';
    echo '</div>';
}

add_action('admin_menu', 'wordlift_admin_menu_entities');
