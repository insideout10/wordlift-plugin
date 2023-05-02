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
			'wl-super-resolution/v1',
			'/posts/(?P<post_id>\d+)/featured-image',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'get_post_featured_image' ),
			)
		);

		register_rest_route(
			'wl-super-resolution/v1',
			'/posts/(?P<post_id>\d+)/featured-image',
			array(
				'methods'  => 'PUT',
				'callback' => array( $this, 'replace_post_featured_image' ),
			)
		);

		register_rest_route(
			'wl-super-resolution/v1',
			'/posts/(?P<post_id>\d+)/featured-image-upsample',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'create_post_featured_image_upsample' ),
			)
		);
	}

	/**
	 * @param WP_REST_Request $request
	 */
	public function get_post_featured_image( $request ) {
		$post_id = $request->get_param( 'post_id' );

		// Check if the post has a featured image
		if ( has_post_thumbnail( $post_id ) ) {
			// Get the attachment ID of the featured image
			$attachment_id = get_post_thumbnail_id( $post_id );

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
		} else {
			// Return null if the post does not have a featured image
			return null;
		}

	}

	/**
	 * @param WP_REST_Request $request
	 */
	public function create_post_featured_image_upsample( $request ) {
		$post_id = $request->get_param( 'post_id' );

		// Check if the post has a featured image
		if ( has_post_thumbnail( $post_id ) ) {
			// Get the attachment ID of the featured image
			$attachment_id = get_post_thumbnail_id( $post_id );

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
		} else {
			// Return null if the post does not have a featured image
			return null;
		}

	}

	public function replace_post_featured_image() {

		// It receives the image binary data (**not** base64 encoded) in the `image` field.

		// Will replace the original image for the post featured image with the upscaled image.

		// Will make sure that the WL image sizes (1:1, 4:3 and 16:9) will be regenerated.
	}

}
