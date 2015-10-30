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

    // If the post is published, add the button to view Redlink's linked data.
    if ( 'publish' == get_post_status( $post_id ) ) {
        if ( $uri = wl_get_entity_uri( $post_id ) ) {
            $uri_esc  =  esc_attr( wl_get_entity_uri( $post_id ) );
            $lod_view_href = 'http://lodview.it/lodview/?IRI=' . $uri_esc;
            $html     .= "<span id='view-post-btn'><a href='$lod_view_href' class='button button-small' target='_blank'>" .
                __('View Linked Data', 'wordlift') .
                "</a></span>\n";
        }
        $html     .= "<span id='view-post-btn'><a href='" . WL_CONFIG_TEST_GOOGLE_RICH_SNIPPETS_URL .
            urlencode( get_permalink( $post_id ) ) .
            "' class='button button-small' target='_blank'>" .
            __('Test Google Rich Snippets', 'wordlift') .
            "</a></span>\n";
    }
    return $html;
}
add_filter('get_sample_permalink_html', 'wl_admin_permalink_html', 10, 4 );


/*
 * Let user know if he is creating a duplicated entity (performing a live AJAX check on the entity title)
 */
function wl_admin_search_duplicated_entity_while_editing_title( $hook ) {
    
    // Only enqueueing script when editing an entity
    if( $hook == 'post.php' ) {
        
        // Get entity object (the auto-draft created automagically by WP)
        $entity_being_edited = get_post();
        
        // Exit if we are not dealing with an entity
        if( $entity_being_edited->post_type == WL_ENTITY_TYPE_NAME ) {
            
            wp_enqueue_script( 'wl-entity-duplicated-titles-live-search', plugins_url( 'js-client/wl_entity-duplicated-titles-live-search.js', __FILE__ ) );
            wp_localize_script( 'wl-entity-duplicated-titles-live-search', 'wlEntityDuplicatedTitlesLiveSearchParams', array(
                    'ajax_url'   => admin_url('admin-ajax.php'),
                    'action'     => 'entity_by_title',
                    'post_id'    => get_the_ID()
                )
            );
        }
    }
}
add_action('admin_enqueue_scripts', 'wl_admin_search_duplicated_entity_while_editing_title');