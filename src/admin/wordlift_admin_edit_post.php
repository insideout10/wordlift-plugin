<?php

/**
 * This file provides function that enhance the post edit screen.
 */

/**
 * Add custom buttons to the buttons below the post title.
 *
 * @param string $html The current html.
 * @param int $post_id The post ID.
 * @param string $new_title Optional. New title.
 * @param string $new_slug Optional. New slug.
 *
 * @return The enhanced html.
 */
function wl_admin_permalink_html( $html, $post_id, $new_title, $new_slug ) {

    // If the post is published, add the button view on Redlink.
    if ( 'publish' == get_post_status( $post_id ) ) {
        $uri_esc  =  esc_attr( wl_get_entity_uri( $post_id ) );
        $html     .= "<span id='view-post-btn'><a href='$uri_esc' class='button button-small' target='_blank'>" .
            __('View on Redlink', 'wordlift') .
            "</a></span>\n";
        $html     .= "<span id='view-post-btn'><a href='" . WL_CONFIG_TEST_GOOGLE_RICH_SNIPPETS_URL .
            urlencode( get_permalink( $post_id ) ) .
            "' class='button button-small' target='_blank'>" .
            __('Test Google Rich Snippets', 'wordlift') .
            "</a></span>\n";
    }

    return $html;
}

add_filter('get_sample_permalink_html', 'wl_admin_permalink_html', 10, 4 );