<?php

/**
 * This file contains the methods to allow syncing the site to the triple store.
 */

/**
 * Sync the site to Redlink triple store and print out the post title being processed.
 */
function wl_admin_sync_to_redlink() {

	$posts = get_posts( array(
		'post_type'      => 'any',
		'posts_per_page' => - 1
	) );

	wl_write_log( "wl_admin_sync_to_redlink [ post count :: " . sizeof( $posts ) . " ]" );

	foreach ( $posts as $post ) {
		echo esc_html( $post->post_title ) . '<br/>';
		wl_linked_data_push_to_redlink( $post->ID );
	}

	// Schedule the execution of SPARQL.
	wl_shutdown();
}

/**
 * AJAX hook for the *wl_admin_sync_to_redlink* call.
 */
function wl_admin_ajax_sync_to_redlink() {

	// TODO: check appropriate permissions here.
	wl_admin_sync_to_redlink();
	die();

}

add_action( 'wp_ajax_wl_sync_to_redlink', 'wl_admin_ajax_sync_to_redlink' );
