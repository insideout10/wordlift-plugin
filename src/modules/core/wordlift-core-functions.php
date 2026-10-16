<?php
/**
 * Wordlift wrapper for wl_send_json function
 *
 * @see https://codex.wordpress.org/Function_Reference/wp_send_json
 * Ensure output buffer is cleaned
 *
 * @param mixed $response The response to send to the client as JSON.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function wl_core_send_json( $response ) {
	if ( ob_get_contents() ) {
		ob_clean();
	}

	wp_send_json( $response );
}

/**
 * Check whether the current user can see a post: either the post is published in a public post type, or the user
 * can read it.
 *
 * @param int|WP_Post|null $post The post or post ID.
 *
 * @return bool True if the current user can see the post, otherwise false.
 * @since 3.54.18
 */
function wl_core_can_see_post( $post ) {
	$post = get_post( $post );
	if ( null === $post ) {
		return false;
	}

	return ( 'publish' === $post->post_status && is_post_type_viewable( $post->post_type ) )
		|| current_user_can( 'read_post', $post->ID );
}
