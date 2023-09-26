<?php
/**
 * Define the Mappings_Transform_Function Interface
 *
 * @since   3.25.0
 * @package Wordlift
 */

namespace Wordlift\Mappings;

/**
 * This interface defines the list of methods to be present for transform function.
 *
 * @since 3.25.0
 */
interface Mappings_Transform_Function {

	/**
	 * Returns unique name of the transform function.
	 *
	 * @return string $name Unique name of the transform function, it should not be repeated
	 * for any other transform function.
	 */
	public function get_name();

	/**
	 * Returns label of the transform function.
	 *
	 * @return string $label Label of the transform function to be used in UI, need not
	 * be unique.
	 */
	public function get_label();

	/**
	 * Transform data and map to the desired keys.
	 *
	 * @param array|string $data An Associative Array containing raw data or string.
	 * @param array        $jsonld The JSON-LD structure.
	 * @param int[]        $references An array of post IDs referenced by the JSON-LD structure.
	 * @param int          $post_id The post ID.
	 *
	 * @return array|string Return Mapped data.
	 */
	public function transform_data( $data, $jsonld, &$references, $post_id );

}
