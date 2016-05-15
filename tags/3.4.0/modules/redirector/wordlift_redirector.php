<?php
/**
 * Provides the redirector module. The redirector module takes an URI and redirects to the page that can
 * display that URI.
 */

/**
 * @since 3.0.0
 */
function wordlift_ajax_redirect() {

	$url = $_GET['url'];
	$link = get_permalink( get_page_by_path( 'timeline-event' ) );
	header( 'Location: ' . $link . '?url=' . urlencode( $url ) );
	wp_die();

}

add_action( 'wp_ajax_wl_redirect', 'wordlift_ajax_redirect' );
add_action( 'wp_ajax_nopriv_wl_redirect', 'wordlift_ajax_redirect' );