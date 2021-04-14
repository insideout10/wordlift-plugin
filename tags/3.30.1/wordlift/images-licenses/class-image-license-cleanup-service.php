<?php

namespace Wordlift\Images_Licenses;

class Image_License_Cleanup_Service {

	public function __construct() {

		add_action( 'wl_post_get_non_public_domain_images', array( $this, 'post_get_non_public_domain_images' ) );

	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function post_get_non_public_domain_images( $data ) {

		return array_values( array_filter( $data, function ( $item ) {

			// Keep images that are referenced either as embeds or featured image.
			$posts_ids_as_embed          = $item['posts_ids_as_embed'];
			$posts_ids_as_featured_image = $item['posts_ids_as_featured_image'];

			if ( ! empty( $posts_ids_as_embed ) || ! empty( $posts_ids_as_featured_image ) ) {
				return true;

			}

			// Remove other images.
			$result = wp_delete_attachment( $item['attachment_id'], true );

			// If result is `WP_Post` it means we successfully deleted the attachment, and therefore we can remove
			// it from the results.
			return ! is_a( $result, 'WP_Post' );
		} ) );
	}

}
