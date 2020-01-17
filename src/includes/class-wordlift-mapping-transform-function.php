<?php
/**
 * Define the Wordlift_Mapping_Transform_Function abstract class
 *
 * @since   3.25.0
 * @package Wordlift
 */

/**
 * This abstract class defines template of methods to be present for the transform function.
 *
 * @since 3.25.0
 */
abstract class Wordlift_Mapping_Transform_Function {
	/**
	 * Returns transformed data.
	 *
	 * @param int   $post_id The id of the post which the ACF data neeeded to be fetched.
	 * @param array $property_data An Associative Array containing all the property data.
	 *
	 * @return array Return transformed data.
	 */
	final public function get_transformed_data( $post_id, $property_data ) {

		$data = $this->get_data_from_data_source( $post_id, $property_data );
		// Send data to external hooks before processing.
		$data = apply_filters(
			'wordlift_sync_mappings_pre_transform_' . $this->get_name(),
			$data
		);

		$data = $this->transform_data( $data );
		// Send data to external hooks after processing.
		$data = apply_filters(
			'wordlift_sync_mappings_post_transform_' . $this->get_name(),
			$data
		);

		return $data;
	}

	/**
	 * Returns data from data source.
	 *
	 * @param int   $post_id Id of the post.
	 * @param array $property_data The property data for the post_id.
	 *
	 * @return array Returns key, value array, if the value is not found, then it
	 * returns null.
	 */
	final public function get_data_from_data_source( $post_id, $property_data ) {
		$value = $property_data['field_name'];
		// Do 1 to 1 mapping and return result.
		if ( 'acf' === $property_data['field_type'] && function_exists( 'get_field' ) ) {
			$value = get_field( $property_data['field_name'], $post_id );
			$value = ( null !== $value ) ? $value : '';
		}

		return array(
			'key'   => $property_data['property_name'],
			'value' => $value,
		);
	}

	/**
	 * Returns unique name of the transform function.
	 *
	 * @return string $name Unique name of the transform function, it should not be repeated
	 * for any other transform function.
	 */
	abstract public function get_name();

	/**
	 * Returns label of the transform function.
	 *
	 * @return string $label Label of the transform function to be used in UI, need not
	 * be unique.
	 */
	abstract public function get_label();

	/**
	 * Tranform data and map to the desired keys.
	 *
	 * @param array|string $data An Associative Array containing raw data or string.
	 *
	 * @return array|string Return Mapped data.
	 */
	abstract public function transform_data( $data );

}
