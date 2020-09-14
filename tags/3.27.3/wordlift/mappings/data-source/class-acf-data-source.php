<?php
/**
 * @since      3.27.0
 * @package    Wordlift
 * @subpackage Wordlift/Mappings/Data_Source
 */
namespace Wordlift\Mappings\Data_Source;

class Acf_Data_Source implements Abstract_Data_Source {

	public function get_data( $post_id, $property_data ) {

		if ( ! function_exists( 'get_field' ) || ! function_exists( 'get_field_object' ) ) {
			return array();
		}

		return $this->get_data_for_acf_field( $property_data['field_name'], $post_id );
	}

	/**
	 * Gets data from acf, format the data if it is a repeater field.
	 *
	 * @param $field_name
	 * @param $post_id
	 *
	 * @return array|mixed
	 */
	// @todo Check usage of get_queried_object
	private function get_data_for_acf_field( $field_name, $post_id ) {
		if ( get_queried_object() instanceof \WP_Term ) {
			// Data fetching method for term is different.
			$term       = get_queried_object();
			$field_data = get_field_object( $field_name, $term );
			$data       = get_field( $field_name, $term );
		} else {
			$field_data = get_field_object( $field_name, $post_id );
			$data       = get_field( $field_name, $post_id );
		}
		// only process if it is a repeater field, else return the data.
		if ( is_array( $field_data ) && array_key_exists( 'type', $field_data )
		     && $field_data['type'] === 'repeater' ) {
			/**
			 * check if we have only one sub field, currently we only support one subfield,
			 * so each repeater item should be checked if there is a single sub field.
			 */
			if ( is_array( $data ) &&
			     count( $data ) > 0 &&
			     count( array_keys( $data[0] ) ) === 1 ) {
				$repeater_formatted_data = array();
				foreach ( $data as $item ) {
					$repeater_formatted_data = array_merge( $repeater_formatted_data, array_values( $item ) );
				}
				// Remove non unique values.
				$repeater_formatted_data = array_unique( $repeater_formatted_data );
				// Remove empty values
				$repeater_formatted_data = array_filter( $repeater_formatted_data, 'strlen' );

				// re-index all the values.
				return array_values( $repeater_formatted_data );
			}
		}

		// Return normal acf data if it is not a repeater field.
		return $data;
	}
}
