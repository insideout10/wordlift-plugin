<?php
/**
 * Storage: Post Image Storage.
 *
 * Provides access to {@link WP_Post} properties.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/linked-data/storage
 */

/**
 * Define the {@link Wordlift_Post_Image_Storage} class.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/linked-data/storage
 */
class Wordlift_Post_Image_Storage extends Wordlift_Storage {

	/**
	 * Get the property value.
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return array|string|null A single string, or an array of values or null
	 *                           if the property isn't recognized.
	 * @since 3.15.0
	 */
	public function get( $post_id ) {

		// Prepare the return array.
		$image_urls = array();

		// If there is a featured image it has the priority.
		$featured_image_id = get_post_thumbnail_id( $post_id );
		if ( is_numeric( $featured_image_id ) && 0 < $featured_image_id ) {
			$image_url = wp_get_attachment_url( $featured_image_id );

			$image_urls[] = $image_url;
		}

		$images = get_children(
			array(
				'post_parent'    => $post_id,
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
			)
		);

		// Collect the URLs.
		foreach ( $images as $attachment_id => $attachment ) {
			$image_url = wp_get_attachment_url( $attachment_id );
			// Ensure the URL isn't collected already.
			if ( ! in_array( $image_url, $image_urls, true ) ) {
				array_push( $image_urls, $image_url );
			}
		}

		return $image_urls;
	}

}
