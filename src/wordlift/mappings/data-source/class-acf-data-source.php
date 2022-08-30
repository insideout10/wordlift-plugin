<?php
/**
 * @since      3.27.0
 * @package    Wordlift
 * @subpackage Wordlift/Mappings/Data_Source
 */

namespace Wordlift\Mappings\Data_Source;

use Wordlift\Mappings\Jsonld_Converter;

class Acf_Data_Source implements Abstract_Data_Source {

	public function get_data( $identifier, $property_data, $type ) {

		if ( ! function_exists( 'get_field' ) || ! function_exists( 'get_field_object' ) ) {
			return array();
		}

		return $this->get_data_for_acf_field( $property_data['field_name'], $identifier, $type );
	}

	/**
	 * Gets data from acf, format the data if it is a repeater field.
	 *
	 * @param $field_name string
	 * @param $identifier int Identifier ( post id or term id )
	 *
	 * @return array|mixed
	 */
	private function get_data_for_acf_field( $field_name, $identifier, $type ) {
		if ( Jsonld_Converter::TERM === $type ) {
			$term = get_term( $identifier );
			// Data fetching method for term is different.
			$field_data = get_field_object( $field_name, $term );
			$data       = get_field( $field_name, $term );
		} else {
			$field_data = get_field_object( $field_name, $identifier );
			$data       = get_field( $field_name, $identifier );
		}
		// only process if it is a repeater field, else return the data.
		if ( is_array( $field_data ) && array_key_exists( 'type', $field_data )
			 && 'repeater' === $field_data['type'] ) {
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
				$repeater_formatted_data = array_filter(
					$repeater_formatted_data,
					function ( $item ) {
						return is_array( $item ) || strlen( $item );
					}
				);

				// re-index all the values.
				return array_values( $repeater_formatted_data );
			}
		}

		// Return normal acf data if it is not a repeater field.
		return $data;
	}
}
