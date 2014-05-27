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
        $uri      = wl_get_entity_uri( $post_id );
        $uri_esc  =  esc_attr( $uri );
        $html     .= "<span id='view-post-btn'><a href='$uri_esc' class='button button-small'>" .
            __('View on Redlink', 'wordlift') .
            "</a></span>\n";
        $html     .= "<span id='view-post-btn'><a href='http://www.google.com/webmasters/tools/richsnippets?q=" .
            urlencode( $uri ) .
            "' class='button button-small'>" .
            __('Test Google Rich Snippets', 'wordlift') .
            "</a></span>\n";
    }

    return $html;
}

add_filter('get_sample_permalink_html', 'wl_admin_permalink_html', 10, 4 );