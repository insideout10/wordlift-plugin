<?php

namespace Wordlift_Videoobject\Yt_Markup;
/**
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Yt_Config {

	public static function get_supported_post_types() {
		return apply_filters( 'wordlift_videoobject_supported_post_types', get_post_types() );
	}


	public static function get_locations_for_acf() {
		$post_type_locations = array();
		$post_types          = self::get_supported_post_types();
		foreach ( $post_types as $post_type ) {
			$post_type_locations[] = array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => $post_type,
				),
			);
		}

		return $post_type_locations;
	}


}