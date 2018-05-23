<?php

/**
 * Save the image with the specified URL locally. To the local filename a uniqe serial is appended to ensure its uniqueness.
 *
 * @param string $url The image remote URL.
 *
 * @return array An array with information about the saved image (*path*: the local path to the image, *url*: the local
 * url, *content_type*: the image content type)
 */
function wl_save_image( $url ) {

	$parts = parse_url( $url );
	$path  = $parts['path'];

	// Get the bare filename (filename w/o the extension).
	// Sanitize filename before saving the current image as attachment
	// See https://codex.wordpress.org/Function_Reference/sanitize_file_name
	$basename = sanitize_file_name( pathinfo( $path, PATHINFO_FILENAME ) . '-' . uniqid( date( 'YmdH-' ) ) );

	// Chunk the bare name to get a subpath.
	$chunks = chunk_split( strtolower( $basename ), 3, DIRECTORY_SEPARATOR );

	// Get the base dir.
	$wp_upload_dir = wp_upload_dir();
	$base_dir      = $wp_upload_dir['basedir'];
	$base_url      = $wp_upload_dir['baseurl'];

	// Get the full path to the local filename.
	$image_path      = '/' . $chunks;
	$image_full_path = $base_dir . $image_path;
	$image_full_url  = $base_url . $image_path;

	// Create the folders.
	if ( ! ( file_exists( $image_full_path ) && is_dir( $image_full_path ) ) ) {
		if ( false === mkdir( $image_full_path, 0777, true ) ) {
			wl_write_log( "wl_save_image : failed creating dir [ image full path :: $image_full_path ]\n" );
		}
	};

	// Request the remote file.
	$response     = wp_remote_get( $url );
	$content_type = wp_remote_retrieve_header( $response, 'content-type' );

	switch ( $content_type ) {
		case 'image/jpeg':
		case 'image/jpg':
			$extension = ".jpg";
			break;
		case 'image/svg+xml':
			$extension = ".svg";
			break;
		case 'image/gif':
			$extension = ".gif";
			break;
		case 'image/png':
			$extension = ".png";
			break;
		default:
			$extension = '';
	}

	// Complete the local filename.
	$image_full_path .= $basename . $extension;
	$image_full_url .= $basename . $extension;

	// Store the data locally.
	file_put_contents( $image_full_path, wp_remote_retrieve_body( $response ) );

	// wl_write_log( "wl_save_image [ url :: $url ][ content type :: $content_type ][ image full path :: $image_full_path ][ image full url :: $image_full_url ]\n" );

	// Return the path.
	return array(
		'path'         => $image_full_path,
		'url'          => $image_full_url,
		'content_type' => $content_type,
	);
}
