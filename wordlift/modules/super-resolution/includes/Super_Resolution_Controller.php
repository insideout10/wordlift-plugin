<?php

namespace Wordlift\Modules\Super_Resolution;

use WP_Error;
use WP_REST_Request;

class Super_Resolution_Controller {

	public function register_hooks() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	public function rest_api_init() {
		register_rest_route(
			'wordlift/v1/super-resolution',
			'/attachments/(?P<attachment_id>\d+)/image',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_post_featured_image' ),
				'permission_callback' => function ( $request ) {
					$post_id = $request->get_param( 'attachment_id' );
					return current_user_can( 'edit_post', $post_id );
				},
			)
		);

		register_rest_route(
			'wordlift/v1/super-resolution',
			'/attachments/(?P<attachment_id>\d+)/image',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'replace_post_featured_image' ),
				'permission_callback' => function ( $request ) {
					$post_id = $request->get_param( 'attachment_id' );
					return current_user_can( 'edit_post', $post_id );
				},
			)
		);

		register_rest_route(
			'wordlift/v1/super-resolution',
			'/attachments/(?P<attachment_id>\d+)/image-upscale',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'create_post_featured_image_upscale' ),
				'permission_callback' => function ( $request ) {
					$post_id = $request->get_param( 'attachment_id' );
					return current_user_can( 'edit_post', $post_id );
				},
			)
		);
	}

	/**
	 * @param WP_REST_Request $request
	 */
	public function get_post_featured_image( $request ) {

		$attachment_id = $request->get_param( 'attachment_id' );
		// Get the path to the image file on the local disk
		$image_path = get_attached_file( $attachment_id );

		// Read the contents of the **local file** image file into a string
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$image_data = file_get_contents( $image_path );

		if ( ! $image_data ) {
			// If image data is false, return a 404 response
			return new WP_Error( '404', 'Image not found.', array( 'status' => 404 ) );
		}

		// Set the content type header to the appropriate image MIME type
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		header( 'Content-Type: image/jpeg' );

		// Sending image binary data.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		exit( $image_data );
	}

	/**
	 * @param WP_REST_Request $request
	 */
	public function create_post_featured_image_upscale( $request ) {
		$attachment_id = $request->get_param( 'attachment_id' );

		// Get the path to the image file on the local disk
		$image_path = get_attached_file( $attachment_id );

		// Read the contents of the **local** image file into a string
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$image_data = file_get_contents( $image_path );

		if ( ! $image_data ) {
			// If image data is false, return a 404 response
			return new WP_Error( '404', 'Image not found.', array( 'status' => 404 ) );
		}

			$boundary = '__X_SUPER_RESOLUTION__';
			$body     = "--$boundary\r\n";
			$body    .= "content-disposition: form-data; name=\"image\"; filename=\"image.jpg\"\r\n";
			$body    .= "content-type: image/jpeg\r\n\r\n";
			$body    .= $image_data . "\r\n";
			$body    .= "--$boundary\r\n";

			$endpoint = 'https://super-resolution.wordlift.io/upscales';
			// Create a new HTTP POST request
			$request = wp_remote_post(
				$endpoint,
				array(
					// Set the content type header to multipart/form-data
					'headers' => array(
						'Accept'       => 'image/jpeg',
						'Content-Type' => "multipart/form-data; boundary=$boundary",
					),
					// Set the request body to the image file
					'body'    => $body,
					// Set the timeout to 30 seconds
					'timeout' => 100,
				)
			);

		if ( is_wp_error( $request ) ) {
			// If the request resulted in an error, return it
			return $request;
		}

		if ( wp_remote_retrieve_response_code( $request ) !== 200 ) {
			// If the response code is not 200 OK, return an error
			return new WP_Error(
				'api_error',
				wp_remote_retrieve_response_message( $request ),
				array(
					'status' => wp_remote_retrieve_response_code( $request ),
				)
			);
		}

		// Get the response body, which contains the binary data of the upscaled image
		$response_body = wp_remote_retrieve_body( $request );

		// Set the content type header to the appropriate image MIME type
		header( 'Content-Type: image/jpeg' );

		// Sending the image binary data.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		exit( $response_body );

	}

	/**
	 * @param $request WP_REST_Request
	 */
	public function replace_post_featured_image( $request ) {

		// It receives the image binary data (**not** base64 encoded) in the `image` field.

		// Will replace the original image for the post featured image with the upscaled image.

		// Will make sure that the WL image sizes (1:1, 4:3 and 16:9) will be regenerated.

		// Get the post ID from the request.
		$attachment_id = $request->get_param( 'attachment_id' );

		$files = $request->get_file_params();

		if ( ! array_key_exists( 'image', $files ) ) {
			return new WP_Error( '404', 'Image not found.', array( 'status' => 404 ) );
		}

		$request_file = $files['image'];

		if ( ! isset( $request_file['type'] ) ) {
			return new WP_Error( '400', 'File mime type is not supported', array( 'status' => 400 ) );
		}

		if ( strpos( $request_file['type'], 'image' ) === false ) {
			return new WP_Error( '400', 'Only image files are supported', array( 'status' => 400 ) );
		}

		// Get the attachment metadata
		$attachment_metadata = wp_get_attachment_metadata( $attachment_id );
		if ( ! $attachment_metadata ) {
			return new WP_Error( '404', 'Image not found.', array( 'status' => 404 ) );
		}

		// Get the original image file path
		$original_image_path = get_attached_file( $attachment_id );
		if ( ! $original_image_path ) {
			return new WP_Error( '404', 'Image path not found.', array( 'status' => 404 ) );
		}

		// Delete the existing resized images
		foreach ( $attachment_metadata['sizes'] as $size ) {
			$resized_image_path = path_join( dirname( $original_image_path ), $size['file'] );
			if ( file_exists( $resized_image_path ) ) {
				unlink( $resized_image_path );
			}
		}

		// Copy the new image to the old image path.
		rename( $request_file['tmp_name'], $original_image_path );

		// Regenerate the resized images
		$metadata_updated = wp_generate_attachment_metadata( $attachment_id, get_attached_file( $attachment_id ) );

		if ( is_wp_error( $metadata_updated ) ) {
			/**
			 * @var $metadata_updated WP_Error
			 */
			return new WP_Error( '500', 'Unable to generate resized images.', array( 'status' => 500 ) );
		}

		// Update the attachment metadata with the regenerated sizes
		wp_update_attachment_metadata( $attachment_id, $metadata_updated );

	}

}
