<?php

/**
 * Interfaces: Post Converter.
 *
 * Define the basic interface for post converters.
 *
 * @since   3.10.0
 * @package Wordlift
 */

use Wordlift\Relation\Relations_Interface;

/**
 * Interface Wordlift_Post_Converter
 *
 * @since   3.10.0
 * @package Wordlift
 */
interface Wordlift_Post_Converter {

	/**
	 * Convert the specified post id.
	 *
	 * @param int                 $post_id The post id.
	 * @param array               $references An array of posts referenced by the specified post.
	 * @param array               $references_infos
	 * @param Relations_Interface $relations The relations
	 *
	 * @return mixed The conversion result.
	 * @since 3.42.1 $reference_objects argument added.
	 * @since 3.16.0 $references argument added.
	 * @since 3.10.0
	 */
	public function convert( $post_id, &$references = array(), &$references_infos = array(), $relations = null );

}
