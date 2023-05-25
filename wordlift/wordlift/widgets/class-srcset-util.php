<?php

namespace Wordlift\Widgets;

/**
 * @since 3.28.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Srcset_Util {

	const NAVIGATOR_WIDGET = 'navigator';

	const FACETED_SEARCH_WIDGET = 'faceted_search';

	public static function get_srcset( $post_id, $widget_name ) {

		$srcset = array();
		$medium = get_the_post_thumbnail_url( $post_id, 'medium' );
		$large  = get_the_post_thumbnail_url( $post_id, 'large' );

		$width = self::get_image_width( $post_id, 'medium' );
		if ( $medium && $width ) {
			$srcset[] = $medium . ' ' . $width . 'w';
		}

		$width = self::get_image_width( $post_id, 'large' );
		if ( $large && $width ) {
			$srcset[] = $large . ' ' . $width . 'w';
		}

		$srcset_string = join( ',', $srcset );

		/**
		 * Filter name: wordlift_${widget_name}_thumbnail_srcset
		 * Filters the srcset string supplied to widgets for each post.
		 *
		 * @param $srcset_string string The srcset string
		 *
		 * @since 3.28.0
		 */
		$srcset_string = apply_filters( "wordlift_${widget_name}_thumbnail_srcset", $srcset_string );

		return $srcset_string;

	}

	private static function get_image_width( $post_id, $size ) {
		$thumbnail_id = get_post_thumbnail_id( $post_id );
		if ( ! $thumbnail_id ) {
			return false;
		}
		$data = wp_get_attachment_image_src( $thumbnail_id, $size );
		if ( ! $data ) {
			return false;
		}

		return array_key_exists( 2, $data ) ? $data[2] : false;
	}

}
