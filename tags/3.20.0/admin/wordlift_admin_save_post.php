<?php
/**
 * This file gathers functions to execute when the post (any type) is saved or updated.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
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
 * @see  http://codex.wordpress.org/Post_Status_Transitions about WordPress post transitions.
 *
 * @param string $new_status The new post status
 * @param string $old_status The old post status
 * @param array  $post       An array with the post data
 */
function wl_transition_post_status( $new_status, $old_status, $post ) {

	// wl_write_log( "wl_transition_post_status [ new status :: $new_status ][ old status :: $old_status ][ post ID :: $post->ID ]" );

	// transition from *published* to any other status: delete the post.
	if ( 'publish' === $old_status && 'publish' !== $new_status ) {
		// Delete the post from the triple store.
		rl_delete_post( $post );

		// Remove all relation instances for the current post from `wl_relation_instances`.
		wl_core_delete_relation_instances( $post->ID );
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

	// Remove the post.
	Wordlift_Linked_Data_Service::get_instance()->remove( $post_id );

}

/**
 * Update the status of a post.
 *
 * @param int    $post_id The post ID
 * @param string $status  The new status
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

	wl_write_log( "wl_update_post_status, old and new post status do not match [ post ID :: $post_id ][ new status :: $status ][ old status :: $post->post_status ]." );

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
