<?php

/**
 * This file gathers functions to execute when the post (any type) is saved or updated.
 */

/**
 * Receive events when a post (any type) status changes. We need to handle here the following cases:
 *  1. *published* to any other status:
 *      a) delete from the triple store.
 *      b) all the referenced entities that are not referenced by any other published post, are to be un-published.
 *  2. any other status to *published*: all referenced entities (only posts of type *entity*) must be published.
 *
 * Note that any status to *published* is handled by the save post routines.
 *
 * @see http://codex.wordpress.org/Post_Status_Transitions about WordPress post transitions.
 *
 * @uses wl_delete_post to delete a post when the status transitions from *published* to anything else.
 *
 * @param string $new_status The new post status
 * @param string $old_status The old post status
 * @param array $post An array with the post data
 */
function wl_transition_post_status( $new_status, $old_status, $post ) {

    write_log( "wl_transition_post_status [ new status :: $new_status ][ old status :: $old_status ][ post ID :: $post->ID ]" );

    // transition from *published* to any other status: delete the post.
    if ( 'publish' === $old_status && 'publish' !== $new_status ) {
        rl_delete_post( $post );
    }

}

/**
 * Delete the specified post from the triple store.
 *
 * @param array|int $post An array of post data
 */
function rl_delete_post( $post ) {

    $post_id = ( is_numeric( $post ) ? $post : $post->ID );
    $uri_esc = wordlift_esc_sparql( wl_get_entity_uri( $post_id ) );

    write_log( "rl_delete_post [ post id :: $post_id ][ uri esc :: $uri_esc ]" );

    // create the SPARQL statement by joining the SPARQL prefixes and deleting any known predicate.
    $stmt = rl_sparql_prefixes();
    foreach ( wl_predicates() as $predicate ) {
        $stmt .= "DELETE { <$uri_esc> $predicate ?o . } WHERE { <$uri_esc> $predicate ?o . };\n" .
                 "DELETE { ?s $predicate <$uri_esc> . } WHERE { ?s $predicate <$uri_esc> . };\n";
    }

    // if the post is an entity and has exported properties, delete the related predicates.
    if ( WL_ENTITY_TYPE_NAME === $post->post_type ) {
        $type = wl_entity_get_type( $post->ID );
        foreach ( $type['export_fields'] as $field => $params ) {
            // TODO: enclose in <> only if predicate starts with http(s)://
            $predicate = '<' . $params['predicate'] . '>';
            $stmt .= "DELETE { <$uri_esc> $predicate ?o . } WHERE { <$uri_esc> $predicate ?o . };\n";
        };
    }

    // finally execute the query.
    rl_execute_sparql_update_query( $stmt );
}

// hook save events.
add_action( 'transition_post_status', 'wl_transition_post_status', 10, 3 );
