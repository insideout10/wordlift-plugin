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
		$small  = get_the_post_thumbnail_url( $post_id, 'small' );
		$medium = get_the_post_thumbnail_url( $post_id, 'medium' );
		$large  = get_the_post_thumbnail_url( $post_id, 'large' );
		if ( $small ) {
			$srcset[] = $small;
		}
		if ( $medium ) {
			$srcset[] = $medium;
		}

		if ( $large ) {
			$srcset[] = $large;
		}

		$srcset_string = join( ",", $srcset );
		$srcset_string = apply_filters( "wordlift_${widget_name}_thumbnail_srcset", $srcset_string );

		return $srcset_string;

	}

}