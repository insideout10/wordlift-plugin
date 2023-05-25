<?php
/**
 * Schema API functions.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/modules/core
 */

/**
 * Retrieves the value of the specified property for the entity.
 *
 * @param int    $post_id       The numeric post ID.
 * @param string $property_name Name of the property (e.g. name, for the http://schema.org/name property).
 *
 * @return array|null An array of values or NULL in case of no values (or error).
 */
function wl_schema_get_value( $post_id, $property_name ) {

	// Property name must be defined.
	if ( ! isset( $property_name ) || $property_name === null ) {
		return null;
	}

	// store eventual schema name in  different variable
	$property_schema_name = wl_build_full_schema_uri_from_schema_slug( $property_name );

	// Establish entity id.
	if ( $post_id === null || ! is_numeric( $post_id ) ) {
		$post_id = get_the_ID();
		if ( $post_id === null || ! is_numeric( $post_id ) ) {
			return null;
		}
	}

	// Get custom fields.
	$term_mapping = wl_entity_taxonomy_get_custom_fields( $post_id );
	// Search for the required meta value (by constant name or schema name)
	foreach ( $term_mapping as $wl_constant => $property_info ) {
		$found_constant  = ( $wl_constant === $property_name );
		$found_predicate = ( isset( $property_info['predicate'] ) && $property_info['predicate'] === $property_schema_name );
		if ( $found_constant || $found_predicate ) {
			return get_post_meta( $post_id, $wl_constant );
		}
	}

	return null;
}

/**
 * Set the value for the specified property and post ID, deleting what was there before.
 *
 * @param int    $post_id        The numeric post ID.
 * @param string $property_name  Name of the property (e.g. name, for the http://schema.org/name property)
 * @param mixed  $property_value Value to save into the property.
 *
 * @return boolean The method returns true if everything went ok, an error string otherwise.
 */
function wl_schema_set_value( $post_id, $property_name, $property_value ) {

	// Some checks on the parameters
	if ( ! is_numeric( $post_id ) || $property_name === null || empty( $property_value ) || $property_value === null ) {
		return false;
	}

	// Build full schema uri if necessary
	$property_name = wl_build_full_schema_uri_from_schema_slug( $property_name );

	// Get accepted properties
	$accepted_fields = wl_entity_taxonomy_get_custom_fields( $post_id );

	// Find the name of the custom-field managing the schema property
	foreach ( $accepted_fields as $wl_constant => $field ) {
		if ( $field['predicate'] === $property_name ) {

			// Deal with single values
			if ( ! is_array( $property_value ) ) {
				$property_value = array( $property_value );
			}

			// Delete present meta
			delete_post_meta( $post_id, $wl_constant );

			foreach ( $property_value as $value ) {
				add_post_meta( $post_id, $wl_constant, $value );
			}

			return true;
		}
	}

	return false;
}

/**
 * Retrieves the entity types for the specified post ID, where
 *
 * @param int $post_id The numeric post ID.
 *
 * @return array Array of type(s) (e.g. Type, for the http://schema.org/Type)
 *               or NULL in case of no values (or error).
 */
function wl_schema_get_types( $post_id ) {

	// Some checks on the parameters
	if ( ! is_numeric( $post_id ) ) {
		return null;
	}

	$type = Wordlift_Entity_Type_Service::get_instance()->get( $post_id );

	if ( isset( $type['uri'] ) ) {
		return array( $type['uri'] );
	}

	return null;
}

/**
 * Sets the entity type(s) for the specified post ID. Support is now for only one type per entity.
 *
 * @param int   $post_id    The numeric post ID
 * @param array $type_names An array of strings, each defining a type (e.g. Type, for the http://schema.org/Type)
 *
 * @return boolean True if everything went ok, an error string otherwise.
 */
function wl_schema_set_types( $post_id, $type_names ) {

	// Some checks on the parameters
	if ( ! is_numeric( $post_id ) || empty( $type_names ) || $type_names === null ) {
		return null;
	}

	// TODO: support more than one type.
	if ( is_array( $type_names ) ) {
		$type_names = $type_names[0];
	}

	// Get the schema URI (e.g. http://schema.org/Thing)
	$type_names = wl_build_full_schema_uri_from_schema_slug( $type_names );

	// Actually sets the taxonomy type
	wl_set_entity_main_type( $post_id, $type_names );

}

/**
 * Build full schema uri starting from a slug. If the uri is already correct, nothing is done.
 *
 * @param string $schema_name Slug or full uri of a schema property or type (es. 'location' or 'http://schema.org/location')
 *
 * @return string The full schema uri (es. 'latitude' returns 'http://schema.org/latitude')
 */
function wl_build_full_schema_uri_from_schema_slug( $schema_name ) {

	$schema_root_address = 'http://schema.org/';

	if ( strpos( $schema_name, $schema_root_address ) === false ) {   // === necessary
		$schema_name = $schema_root_address . $schema_name;
	}

	return $schema_name;
}
