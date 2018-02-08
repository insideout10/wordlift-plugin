<?php

/**
 * Save the image with the specified URL locally. To the local filename a uniqe serial is appended to ensure its uniqueness.
 *
 * @param string $url The image remote URL.
 *
 * @return array|false An array with information about the saved image (*path*: the local path to the image, *url*: the local
 * url, *content_type*: the image content type) or false on error.
 */
function wl_save_image( $url ) {

	$parts = wp_parse_url( $url );
	$path  = $parts['path'];

	// Get the bare filename (filename w/o the extension).
	$basename = pathinfo( $path, PATHINFO_FILENAME );

	// Get the base dir.
	$wp_upload_dir = wp_upload_dir();

	// Set the upload directory and URL.
	$upload_dir = $wp_upload_dir['basedir'] . '/wl' . $wp_upload_dir['subdir'];
	$upload_url = $wp_upload_dir['baseurl'] . '/wl' . $wp_upload_dir['subdir'];

	// Get the full path to the local filename.
	$image_path      = '/' . $basename;
	$image_full_path = $upload_dir . $image_path;
	$image_full_url  = $upload_url . $image_path;

	// Create the WL directory.
	$is_directory_created = wp_mkdir_p( $upload_dir );

	// Bail out if the directory still doesn't exists.
	if ( empty( $is_directory_created ) ) {
		wl_write_log( "wl_save_image : failed creating upload dir $upload_dir \n" );

		return false;
	};

	// Request the remote file.
	$response = wp_remote_get( $url );

	// Bail out if the response is not ok.
	if (
		is_wp_error( $response )
		|| 200 !== (int) $response['response']['code']
		|| ! isset( $response['body'] )
	) {
		wl_write_log( "wl_save_image : error fetching image $url \n" );

		return false;
	}

	// Get the content type of response.
	$content_type = wp_remote_retrieve_header( $response, 'content-type' );

	switch ( $content_type ) {
		case 'image/jpeg':
		case 'image/jpg':
			$extension = '.jpg';
			break;
		case 'image/svg+xml':
			// SVG not supported anymore:
			// https://github.com/insideout10/wordlift-plugin/issues/325
			return false;
		case 'image/gif':
			$extension = '.gif';
			break;
		case 'image/png':
			$extension = '.png';
			break;
		default:
			// Do not support unknown mime types.
			return false;
	}

	// Complete the local filename.
	$image_full_path .= $extension;
	$image_full_url  .= $extension;

	// Store the data locally.
	file_put_contents( $image_full_path, wp_remote_retrieve_body( $response ) );

	// Return the path.
	return array(
		'path'         => $image_full_path,
		'url'          => $image_full_url,
		'content_type' => $content_type,
	);

}
