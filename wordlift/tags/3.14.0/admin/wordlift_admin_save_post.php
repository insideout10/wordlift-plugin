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
 * @uses wl_delete_post() to delete a post when the status transitions from *published* to anything else.
 *
 * @param string $new_status The new post status
 * @param string $old_status The old post status
 * @param array $post An array with the post data
 */
function wl_transition_post_status( $new_status, $old_status, $post ) {

	// wl_write_log( "wl_transition_post_status [ new status :: $new_status ][ old status :: $old_status ][ post ID :: $post->ID ]" );

	// transition from *published* to any other status: delete the post.
	if ( 'publish' === $old_status && 'publish' !== $new_status ) {
		rl_delete_post( $post );
	}

	// when a post is published, then all the referenced entities must be published.
	if ( 'publish' !== $old_status && 'publish' === $new_status ) {
		foreach ( wl_core_get_related_entity_ids( $post->ID ) as $entity_id ) {
			wl_update_post_status( $entity_id, 'publish' );
		}
	}
}

// hook save events.
add_action( 'transition_post_status', 'wl_transition_post_status', 10, 3 );


/**
 * Delete the specified post from the triple store.
 *
 * @param array|int $post An array of post data
 */
function rl_delete_post( $post ) {

	$post_id = ( is_numeric( $post ) ? $post : $post->ID );

	// hide all entities that are not referenced by any published post.
	foreach ( wl_core_get_related_entity_ids( $post_id ) as $entity_id ) {

		// check if there is at least one referencing post published.
		$is_published = array_reduce( wl_core_get_related_post_ids( $entity_id ), function ( $carry, $item ) {
                    $post = get_post( $item );
                    return ( $carry || ( 'publish' === $post->post_status ) );
		} );
		// set the entity to draft if no referencing posts are published.
		if ( ! $is_published ) {
			wl_update_post_status( $entity_id, 'draft' );
		}
	}

	// get the entity URI (valid also for posts)
	$uri_esc = wl_sparql_escape_uri( wl_get_entity_uri( $post_id ) );

	wl_write_log( "rl_delete_post [ post id :: $post_id ][ uri esc :: $uri_esc ]" );

	// create the SPARQL statement by joining the SPARQL prefixes and deleting any known predicate.
	$stmt = rl_sparql_prefixes();
	foreach ( wl_predicates() as $predicate ) {
		$stmt .= "DELETE { <$uri_esc> $predicate ?o . } WHERE { <$uri_esc> $predicate ?o . };\n" .
		         "DELETE { ?s $predicate <$uri_esc> . } WHERE { ?s $predicate <$uri_esc> . };\n";
	}

	// if the post is an entity and has exported properties, delete the related predicates.
	if ( Wordlift_Entity_Service::TYPE_NAME === $post->post_type ) {
		$type = wl_entity_type_taxonomy_get_type( $post->ID );

		if ( isset( $type['custom_fields'] ) ) {
			foreach ( $type['custom_fields'] as $field => $params ) {
				// TODO: enclose in <> only if predicate starts with http(s)://
				$predicate = '<' . $params['predicate'] . '>';
				$stmt .= "DELETE { <$uri_esc> $predicate ?o . } WHERE { <$uri_esc> $predicate ?o . };\n";
			}
		}
	}

	// finally execute the query.
	rl_execute_sparql_update_query( $stmt );
}

/**
 * Update the status of a post.
 *
 * @param int $post_id The post ID
 * @param string $status The new status
 */
function wl_update_post_status( $post_id, $status ) {

	wl_write_log( "wl_update_post_status [ post ID :: $post_id ][ status :: $status ]" );

	global $wpdb;

	if ( ! $post = get_post( $post_id ) ) {
		return;
	}

	if ( $status === $post->post_status ) {
		return;
	}

	$wpdb->update( $wpdb->posts, array( 'post_status' => $status ), array( 'ID' => $post->ID ) );

	clean_post_cache( $post->ID );

	$old_status        = $post->post_status;
	$post->post_status = $status;

	wp_transition_post_status( $status, $old_status, $post );

	/** This action is documented in wp-includes/post.php */
	do_action( 'edit_post', $post->ID, $post );
	/** This action is documented in wp-includes/post.php */
	do_action( "save_post_{$post->post_type}", $post->ID, $post, true );
	/** This action is documented in wp-includes/post.php */
	do_action( 'wl_linked_data_save_post', $post->ID );
	/** This action is documented in wp-includes/post.php */
	do_action( 'wp_insert_post', $post->ID, $post, true );
}
