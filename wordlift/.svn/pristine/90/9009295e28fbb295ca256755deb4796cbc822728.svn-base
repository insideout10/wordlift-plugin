<?php

/**
 * Load an image from Freebase.
 *
 * @since 3.0.0
 */
function wl_freebase_image_proxy() {

	// get the url.
	$url = $_GET['url'];

	$matches = array();
	if ( 1 === preg_match( '/http:\/\/rdf.freebase.com\/ns\/(\w)\.([\w|\d]+)/i', $url, $matches ) ) {
		$prefix    = $matches[1];
		$path      = $matches[2];
		$image_url = "https://usercontent.googleapis.com/freebase/v1/image/$prefix/$path";
		$response  = wp_remote_get( $image_url );

		if ( is_wp_error( $response ) ) {
			die( 'An error occurred.' );
		}

		// dump out the image.
		$content_type = $response['headers']['content-type'];
		header( "Content-Type: $content_type" );
		echo( $response['body'] );

	} else {
		die( 'Invalid URL' );
	}

	die(); // this is required to return a proper result
}

add_action( 'wp_ajax_wordlift_freebase_image', 'wl_freebase_image_proxy' );
