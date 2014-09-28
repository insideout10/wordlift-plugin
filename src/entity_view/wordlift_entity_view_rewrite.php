<?php

/**
 * For more information, see http://codex.wordpress.org/Class_Reference/WP_Rewrite
 */

define( 'WL_REWRITE_RULE_PATTERN', '_/([^/]+)/[^/]+/([^/]+)' );

/**
 * Flush the rules if our own rules is not there yet.
 */
function wl_entity_view_flush_rules() {

    $rules = get_option( 'rewrite_rules' );

    if ( WP_DEBUG || ! isset( $rules[WL_REWRITE_RULE_PATTERN] ) ) {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }

}
add_action( 'wp_loaded','wl_entity_view_flush_rules' );

/**
 * Add the WordLift Entity View rule to the rewrite rules.
 *
 * @param array $rules The array of existing rules.
 * @return array The rules array including ours.
 */
function wl_entity_view_add_rewrite_rules( $rules )
{

    $wl_rules = array();
    $wl_rules[WL_REWRITE_RULE_PATTERN] =
        'index.php?pagename=$matches[1]&' . WL_ENTITY_VIEW_ENTITY_ID_QUERY_VAR . '=$matches[2]';

    return $wl_rules + $rules;

}
add_action( 'rewrite_rules_array', 'wl_entity_view_add_rewrite_rules' );


/**
 * Add support for our *wl_uri* parameter.
 *
 * @param array $vars Existing query vars array.
 * @return array The query vars array including our *wl_uri* parameter.
 */
function wl_entity_view_add_query_vars( $vars )
{
    array_push( $vars, WL_ENTITY_VIEW_ENTITY_ID_QUERY_VAR );
    return $vars;

}
add_filter( 'query_vars','wl_entity_view_add_query_vars' );
