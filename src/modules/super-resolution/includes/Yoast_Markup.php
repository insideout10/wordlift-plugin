<?php

namespace Wordlift\Modules\Super_Resolution;

use Exception;

class Yoast_Markup {

	private static $image_sizes = array( 'wl-16x9', 'wl-4x3', 'wl-1x1' );

	public function register_hooks() {
		add_filter( 'wpseo_schema_article', array( $this, 'wpseo_schema_article' ) );
	}

	public function wpseo_schema_article( $data ) {

		// Bail out if Yoast doesn't exist.
		if ( ! function_exists( 'YoastSEO' ) ) {
			return $data;
		}

		// Try to get the post or bail out.
		try {
			$post_id = YoastSEO()->meta->for_current_page()->post_id;

			// Get the image URL and dimensions for each image size
			$images = array();
			foreach ( self::$image_sizes as $size ) {
				$thumbnail_id = get_post_thumbnail_id( $post_id );
				$image        = wp_get_attachment_image_src( $thumbnail_id, $size );

				if ( ! $image ) {
					continue;
				}

				$image_array = array(
					'@type'      => 'ImageObject',
					'url'        => $image[0],
					'contentUrl' => $image[0],
					'width'      => $image[1],
					'height'     => $image[2],
				);

				$this->add_caption( $thumbnail_id, $image_array );

				$images[] = $image_array;
			}

			// Bail out if there are no image.
			if ( empty( $images ) ) {
				return $data;
			}

			// Ensure image is an array.
			$this->prepare_image_property( $data );

			$data['image'] = array_merge( $data['image'], $images );

			// Return the modified data
			return $data;
		} catch ( Exception $e ) {
			return $data;
		}

	}

	private function prepare_image_property( &$value ) {

		// If `image` isn't set, set it.
		if ( ! isset( $value['image'] ) ) {
			$value['image'] = array();
		}

		// If `image` isn't an array, make it an array.
		if ( ! is_array( $value['image'] ) ) {
			$value['image'] = (array) $value['image'];
		}

		// If the `image` array isn't empty and the 1st value isn't an array, it means that we have
		// a situation like:
		// ```
		// "image": {
		// "@type": "..."
		// }
		// ```
		//
		// and we want to push it to:
		// ```
		// "image": [ {
		// "@type": "..."
		// } ]
		// ```
		if ( ! empty( $value['image'] ) && ! is_array( current( $value['image'] ) ) ) {
			$value['image'] = array( $value['image'] );
		}

	}

	private function add_caption( $thumbnail_id, &$value ) {

		// Try with `wp_get_attachment_caption`.
		$caption = wp_get_attachment_caption( $thumbnail_id );
		if ( ! empty( $caption ) ) {
			$value['caption'] = $caption;

			return;
		}

		// Try with `get_post_meta`.
		$alt_text = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );
		if ( ! empty( $alt_text ) ) {
			$value['caption'] = $alt_text;

			return;
		}

	}

}
