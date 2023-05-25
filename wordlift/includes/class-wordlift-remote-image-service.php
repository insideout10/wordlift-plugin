<?php

/**
 * Define a class that handle and upload all images from external sources.
 *
 * @since   3.18.0
 * @package Wordlift
 */
class Wordlift_Remote_Image_Service {

	/**
	 * Save the image with the specified URL locally.
	 *
	 * @param string $url The image remote URL.
	 *
	 * @return array|false|WP_Error An array with information about the saved image (*path*: the local path to the image, *url*: the local
	 * url, *content_type*: the image content type) or false on error.
	 * @since 3.18.0
	 * @since 3.23.4 the function may return a WP_Error.
	 */
	public static function save_from_url( $url ) {

		// Required for REST API calls
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		// Load `WP_Filesystem`.
		add_filter( 'filesystem_method', 'Wordlift_Remote_Image_Service::_return_direct' );
		WP_Filesystem();
		global $wp_filesystem;

		// Parse the url.
		$parts = wp_parse_url( $url );

		// Get the bare filename (filename w/o the extension).
		$basename = str_replace(
			DIRECTORY_SEPARATOR,
			'_',
			rawurldecode(
				pathinfo( $parts['path'], PATHINFO_FILENAME )
			)
		);

		// Get the base dir.
		$wp_upload_dir = wp_upload_dir();

		// Set the upload directory and URL.
		$upload_dir = $wp_upload_dir['basedir'] . '/wl' . $wp_upload_dir['subdir'];
		$upload_url = $wp_upload_dir['baseurl'] . '/wl' . $wp_upload_dir['subdir'];

		// Get the full path to the local filename.
		$image_full_path = $upload_dir . '/' . $basename;
		$image_full_url  = $upload_url . '/' . $basename;

		// Create custom directory and bail on failure.
		if ( ! wp_mkdir_p( $upload_dir ) ) {
			Wordlift_Log_Service::get_logger( 'Wordlift_Remote_Image_Service' )
								->warn( "save_image_from_url : failed creating upload dir $upload_dir \n" );

			return new WP_Error( 'image_error', "save_image_from_url : failed creating upload dir $upload_dir \n" );
		};

		$response = wp_remote_get(
			$url,
			array(
				'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.135 Safari/537.36',
			)
		);

		// Bail if the response is not set.
		if ( self::is_response_error( $response ) ) {
			Wordlift_Log_Service::get_logger( 'Wordlift_Remote_Image_Service' )
								// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export
								->warn( "save_image_from_url : failed to fetch the response from: $url\nThe response was:\n" . var_export( $response, true ) );

			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export
			return new WP_Error( 'image_error', "save_image_from_url : failed to fetch the response from: $url\nThe response was:\n" . var_export( $response, true ) );
		}

		// Get the content type of response.
		$content_type = wp_remote_retrieve_header( $response, 'content-type' );

		// Get the file extension.
		$extension = self::get_extension_from_content_type( $content_type );

		// Bail if the content type is not supported.
		if ( empty( $extension ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export
			return new WP_Error( 'image_error', "Unsupported content type [ $content_type ]:\n" . var_export( $response, true ) );
		}

		// Complete the local filename.
		$image_full_path .= $extension;
		$image_full_url  .= $extension;

		// Store the data locally.
		$wp_filesystem->put_contents( $image_full_path, wp_remote_retrieve_body( $response ) );
		remove_filter( 'filesystem_method', 'Wordlift_Remote_Image_Service::_return_direct' );

		// Return the path.
		return array(
			'path'         => $image_full_path,
			'url'          => $image_full_url,
			'content_type' => $content_type,
		);
	}

	// phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
	public static function _return_direct() {
		return 'direct';
	}

	/**
	 * Returns the file extension using the content type.
	 *
	 * @param string $content_type File content type.
	 *
	 * @return string|bool The file extension on success and
	 * false on fail or if the content type is not supported.
	 * @since 3.18.0
	 */
	private static function get_extension_from_content_type( $content_type ) {

		// Return the extension if match.
		switch ( $content_type ) {
			case 'image/jpeg':
			case 'image/jpg':
				return '.jpg';
			case 'image/gif':
				return '.gif';
			case 'image/png':
				return '.png';
		}

		// Otherwise return false.
		return false;
	}

	/**
	 * Checks whether a response is an error.
	 *
	 * @param array|WP_Error $response The response.
	 *
	 * @return bool True if the response is an error, otherwise false.
	 * @since 3.23.4
	 */
	private static function is_response_error( $response ) {

		return ( is_wp_error( $response )
				 || 200 !== (int) $response['response']['code']
				 || ! isset( $response['body'] )
		);
	}

}
