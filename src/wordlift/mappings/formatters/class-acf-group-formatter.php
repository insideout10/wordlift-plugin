<?php

namespace Wordlift\Mappings\Formatters;

/**
 * This class formats the acf group data, and removes empty values.
 * Class Acf_Group_Formatter
 *
 * @package Wordlift\Mappings\Formatters
 */
class Acf_Group_Formatter {

	public function __construct() {
		// Hook in to format value filter and apply format value.
		add_filter( 'wl_mapping_acf_format_value', array( $this, 'format_group_value' ), 10, 2 );
	}

	public function format_group_value( $field_data, $field_type ) {
		if ( 'group' !== $field_type ) {
			// Return early if the field type is not group
			return $field_data;
		}
		// we need to check if atleast one key is present.
		$filtered_group_data = array_filter( $field_data, 'strlen' );
		if ( ! $filtered_group_data ) {
			return false;
		}

		return $filtered_group_data;

	}
}
