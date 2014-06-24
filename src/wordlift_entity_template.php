<?php

/**
 * This file provides functions used to alter the way an entity is rendered on the front-end.
 */

/**
 * Intercept the call to display the entity page and redirect it to the *home.php* template with a list of posts
 * referencing this entity.
 *
 * @uses wl_get_referencing_posts to get the list of posts referencing the current entity.
 *
 * @param string $template The template selected by WordPress.
 * @return string The template selected by this function.
 */
function wl_entity_template( $template ) {

    // Return the provided template name if this is not an *entity*.
    if ( WL_ENTITY_TYPE_NAME !== get_post_type() || 'index' !== wl_get_entity_display_as( get_the_ID() ) ) {

        wl_write_log( "wl_entity_template : not processing [ template :: $template ][ post type :: " . get_post_type() . " ]" );

        return $template;
    }

    wl_write_log( "wl_entity_template : processing [ template :: $template ]" );

    // Get the referencing posts for the query.
    $post_ids = array_map( function ( $item ) {
        return $item->ID;
    }, wl_get_referencing_posts( get_the_ID() ) );

    // Query for the referencing posts. The number of displayed posts should be limited by WordPress automatically.
    query_posts( array(
        'post__in' => $post_ids,
        'orderby' => 'date',
        'order' => 'DESC'
    ) );

    // Display the list of posts using the home.php template.
    return locate_template( array(
        'home.php',
        'paged.php',
        'index.php'
    ) );

}
add_filter( 'single_template', 'wl_entity_template', 10, 1 );

/**
 * Decide whether to show an entity as an index or as an entity page.
 *
 * @param int $post_id The entity post ID.
 *
 * @return string The display as code (default: *index*).
 */
function wl_get_entity_display_as( $post_id ) {

    $display_as = get_post_meta( $post_id, WL_CUSTOM_FIELD_ENTITY_DISPLAY_AS_SINGLE_PAGE, true );

    // return *index* by default
    return ( '' === $display_as ? 'index' : $display_as );
}

/**
 * Set the entity display mode.
 *
 * @param int $post_id The entity post ID.
 * @param string $display_as The display as value.
 */
function wl_set_entity_display_as( $post_id, $display_as ) {

    wl_write_log( "wl_set_entity_display_as [ post ID :: $post_id ][ display as :: $display_as ]" );

    delete_post_meta( $post_id, WL_CUSTOM_FIELD_ENTITY_DISPLAY_AS_SINGLE_PAGE );
    add_post_meta( $post_id, WL_CUSTOM_FIELD_ENTITY_DISPLAY_AS_SINGLE_PAGE, $display_as, true );
}