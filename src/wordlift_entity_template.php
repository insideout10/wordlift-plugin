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
    if ( WL_ENTITY_TYPE_NAME !== get_post_type() || false === wl_entity_display_as_index( get_the_ID() ) ) {
        return $template;
    }

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
 * @return bool True if display as index is on, otherwise false.
 */
function wl_entity_display_as_index( $post_id ) {

    return ( '' === get_post_meta( $post_id, WL_CUSTOM_FIELD_ENTITY_DISPLAY_AS_SINGLE_PAGE, true ) );
}