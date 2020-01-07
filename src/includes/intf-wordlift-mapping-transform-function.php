<?php
/**
 * Define the Wordlift_Mapping_Transform_Function Interface
 *
 * @since   3.25.0
 * @package Wordlift
 */

/**
 * This interface defines list of methods to be present for the transform function.
 *
 * @since 3.25.0
 */

interface Wordlift_Mapping_Transform_Function {
	/**
	 * Returns unique name of the transform function.
	 *
	 * @return String $name Unique name of the transform function, it should not be repeated
	 * for any other transform function.
	 */
	public function get_name();

	/**
	 * Returns label of the transform function.
	 *
	 * @return String $label Label of the transform function to be used in UI, need not
	 * be unique.
	 */
	public function get_label();


	/**
	 * Returns transformed data.
	 *
	 * @param Array $property_data An Associative Array containing all the property data.
	 * @return Array Return transformed data.
	 */
	public function transform_data( $property_data );
}
