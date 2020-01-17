<?php
/**
 * Define the Wordlift_Mapping_Transform_Function Interface
 *
 * @since   3.25.0
 * @package Wordlift
 */

/**
 * This abstract class defines template of methods to be present for the transform function.
 *
 * @since 3.25.0
 */
interface Wordlift_Mapping_Transform_Function {

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
	 * Tranform data and map to the desired keys.
	 *
	 * @param array|string $data An Associative Array containing raw data or string.
	 *
	 * @return array|string Return Mapped data.
	 */
	public function transform_data( $data );

}
