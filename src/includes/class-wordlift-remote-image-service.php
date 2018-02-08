<?php
/**
 * Define a class that handle and upload all images from external sources.
 *
 * @since   3.18.0
 * @package Wordlift
 */
class Wordlift_Remote_Image_Service {
	/**
	 * The entity post type.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var array $http_response Response from the HTTP request.
	 */
	private static $http_response = array();

	/**
	 * Save the image with the specified URL locally.
	 *
	 * @param string $url The image remote URL.
	 *
	 * @since 3.18.0
	 *
	 * @return array|false An array with information about the saved image (*path*: the local path to the image, *url*: the local
	 * url, *content_type*: the image content type) or false on error.
	 */
	public static function save_from_url( $url ) {
		// Load `WP_Filesystem`.
		WP_Filesystem();
		global $wp_filesystem;

		// Parse the url.
		$parts = wp_parse_url( $url );

		// Get the bare filename (filename w/o the extension).
		$basename = pathinfo( $parts['path'], PATHINFO_FILENAME );

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
			wl_write_log( "save_image_from_url : failed creating upload dir $upload_dir \n" );

			return false;
		};

		// Bail if the response is not set.
		if ( ! self::set_response( $url ) ) {
			wl_write_log( "save_image_from_url : failed to fetch the response from: $url \n" );

			return false;
		}

		// Get the content type of response.
		$content_type = wp_remote_retrieve_header( self::$http_response, 'content-type' );

		// Get the file extension.
		$extension = self::get_extension_from_content_type( $content_type );

		// Bail if the content type is not supported.
		if ( empty( $extension ) ) {
			return false;
		}

		// Complete the local filename.
		$image_full_path .= $extension;
		$image_full_url  .= $extension;

		// Store the data locally.
		$wp_filesystem->put_contents( $image_full_path, wp_remote_retrieve_body( self::$http_response ) );

		// Return the path.
		return array(
			'path'         => $image_full_path,
			'url'          => $image_full_url,
			'content_type' => $content_type,
		);

	}

	/**
	 * Returns the file extension using the content type.
	 *
	 * @param string $content_type File content type.
	 *
	 * @since 3.18.0
	 *
	 * @return string|bool The file extension on success and
	 * false on fail or if the content type is not supported.
	 */
	private static function get_extension_from_content_type( $content_type ) {
		// Bail if the content type is now set.
		if ( empty( $content_type ) ) {
			return false;
		}

		// Get the extension type.
		switch ( $content_type ) {
			case 'image/jpeg':
			case 'image/jpg':
				$extension = '.jpg';
				break;
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

		// Return the extension.
		return $extension;
	}

	/**
	 * Retrieve the response from url and sets the response.
	 *
	 * @param string $url The url to retrieve.
	 *
	 * @since 3.18.0
	 *
	 * @return bool True on success and false on failure.
	 */
	private static function set_response( $url ) {
		// Request the remote file.
		$response = wp_remote_get( $url );

		// Bail out if the response is not ok.
		if (
			is_wp_error( $response )
			|| 200 !== (int) $response['response']['code']
			|| ! isset( $response['body'] )
		) {
			wl_write_log( "save_image_from_url : error fetching image $url \n" );

			return false;
		}

		// Set the response.
		self::$http_response = $response;

		return true;
	}
}
