<?php
/**
 * This file provides methods for the shortcode *wl_field*.
 */

/**
 * Print wl_field shortcode
 *
 * @param array $atts An array of shortcode attributes.
 * @return string A dom element with requested property value(s).
 */
function wl_shortcode_field( $atts ) {

	// Extract attributes and set default values.
	$field_atts = shortcode_atts(
		array(
			'id'   => null,
			'name' => null,
		),
		$atts
	);

	// Get id of the post
	$entity_id = $field_atts['id'];
	if ( $field_atts['id'] === null || ! is_numeric( $field_atts['id'] ) ) {
		$entity_id = get_the_ID();
	}

	$property_name = $field_atts['name'];
	if ( $property_name !== null ) {
		$values = wl_schema_get_value( $entity_id, $property_name );
	}

	// Return
	if ( is_array( $values ) ) {
		return implode( ', ', $values );
	} else {
		return null;
	}
}

function wl_register_shortcode_field() {
	add_shortcode( 'wl_field', 'wl_shortcode_field' );
}

add_action( 'init', 'wl_register_shortcode_field' );

