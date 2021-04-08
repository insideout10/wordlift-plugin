<?php
/**
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift_Videoobject;

/**
 * Class Config
 * @package Wordlift_Videoobject
 */
class Config {

	/**
	 * Jsonld would be applied only on the post types.
	 */
	const SUPPORTED_POST_TYPES = array(
		'vakantiepark'
	);


	public static function get_acf_location() {
		$location = array();
		foreach ( self::SUPPORTED_POST_TYPES as $post_type ) {
			$location[] = array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => $post_type,
				),
			);
		}
		return $location;
	}


}
