<?php
/**
 * Services: Attachment Service.
 *
 * Provide functions to manipulate attachments.
 *
 * @since   3.10.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Attachment_Service} class.
 *
 * @since   3.10.0
 * @package Wordlift
 */
class Wordlift_Attachment_Service {

	/**
	 * Create a {@link Wordlift_Attachment_Service} instance.
	 *
	 * @since 3.20.0
	 */
	protected function __construct() {

	}

	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return \Wordlift_Attachment_Service The singleton instance.
	 * @since 3.20.0
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get an attachment ID given a URL.
	 *
	 * Inspired from https://wpscholar.com/blog/get-attachment-id-from-wp-image-url/
	 *
	 * @param string $url The attachment URL.
	 *
	 * @return int|false Attachment ID on success, false on failure
	 * @since 3.10.0
	 */
	public function get_attachment_id( $url ) {

		// Get the upload directory data, we need the base URL to check whether
		// the URL we received is within WP.
		$dir = wp_upload_dir();

		// Get the filename, the extension is kept.
		if ( 1 !== preg_match( "@^{$dir['baseurl']}/(.+?)(?:-\d+x\d+)?(\.\w+)$@", $url, $matches ) ) {
			return false;
		}

		$filename = $matches[1] . $matches[2];

		// The following query is CPU-intensive, we need to review it.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/689.
		//
		// Query for attachments with the specified filename.
		$query = new WP_Query(
			array(
				'post_type'           => 'attachment',
				'post_status'         => 'inherit',
				'fields'              => 'ids',
				'meta_query'          => array(
					array(
						'value'   => $filename,
						'compare' => '=',
						'key'     => '_wp_attached_file',
					),
				),
				'posts_per_page'      => 1,
				'ignore_sticky_posts' => true,
			)
		);

		// If there are no posts, return.
		if ( $query->have_posts() ) {
			return $query->posts[0];
			// foreach ( $query->posts as $attachment_id ) {
			//
			// Get the attachment metadata, we need the filename.
			// $metadata          = wp_get_attachment_metadata( $attachment_id );
			// $original_filename = basename( $metadata['file'] );
			//
			// Get the cropped filenames, or an empty array in case there are no files.
			// $sizes_filenames = isset( $metadata['sizes'] ) ? wp_list_pluck( $metadata['sizes'], 'file' ) : array();
			//
			// If the provided filename matches the attachment filename (or one of its resized images), return the id.
			// if ( $original_filename === $filename || in_array( $filename, $sizes_filenames ) ) {
			// return $attachment_id;
			// }
			// }
		}

		// If we got here, we couldn't find any attachment.
		return false;
	}

	/**
	 * Get images embedded in the post content.
	 *
	 * @param string $content The post content.
	 *
	 * @return array An array of attachment ids.
	 * @since 3.10.0
	 */
	public function get_image_embeds( $content ) {

		// Go over all the images included in the post content, check if they are
		// in the DB, and if so include them.
		$images = array();
		if ( false === preg_match_all( '#<img [^>]*src="([^\\">]*)"[^>]*>#', $content, $images ) ) {
			return array();
		}

		// Map the image URLs to attachment ids.
		$that = $this;
		$ids  = array_map(
			function ( $url ) use ( $that ) {
				return $that->get_attachment_id( $url );
			},
			$images[1]
		);

		// Filter out not found ids (i.e. id is false).
		return array_filter(
			$ids,
			function ( $item ) {
				return false !== $item;
			}
		);
	}

	/**
	 * Get images linked via the `gallery` shortcode.
	 *
	 * @param \WP_Post $post A {@link WP_Post} instance.
	 *
	 * @return array An array of attachment ids.
	 * @since 3.10.0
	 */
	public function get_gallery( $post ) {

		// @todo: the `gallery` shortcode has an `exclude` attribute which isn't
		// checked at the moment.

		// Prepare the return value.
		$ids = array();

		// As the above for images in galleries.
		// Code inspired by http://wordpress.stackexchange.com/questions/80408/how-to-get-page-post-gallery-attachment-images-in-order-they-are-set-in-backend
		$pattern = get_shortcode_regex();

		if ( preg_match_all( '/' . $pattern . '/s', $post->post_content, $matches )
			 && array_key_exists( 2, $matches )
			 && in_array( 'gallery', $matches[2], true )
		) {

			$keys = array_keys( $matches[2], 'gallery', true );

			foreach ( $keys as $key ) {
				$atts = shortcode_parse_atts( $matches[3][ $key ] );

				if ( is_array( $atts ) && array_key_exists( 'ids', $atts ) ) {
					// gallery images insert explicitly by their ids.

					foreach ( explode( ',', $atts['ids'] ) as $attachment_id ) {
						// Since we do not check for actual image existence
						// when generating the json content, check it now.
						if ( wp_get_attachment_image_src( $attachment_id, 'full' ) ) {
							$ids[ $attachment_id ] = true;
						}
					}
				} else {
					// gallery shortcode with no ids uses all the images
					// attached to the post.
					$images = get_attached_media( 'image', $post->ID );
					foreach ( $images as $attachment ) {
						$ids[ $attachment->ID ] = true;
					}
				}
			}
		}

		return array_keys( $ids );
	}

}
