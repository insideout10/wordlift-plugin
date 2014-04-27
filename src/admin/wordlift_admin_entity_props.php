<?php
/**
 * This file provides Entity properties-related functions.
 */

/**
 * Save the entity properties.
 * @param string An entity URI.
 * @param array $props An array of entity properties.
 */
function wl_entity_props_save( $entity_uri, $props ) {

    $mappings = wl_entity_props_get_mappings();

    // Get the post by the URI.
    $post = wl_get_entity_post_by_uri( $entity_uri );

    // Return if there's no post.
    if ( null === $post ) {
        write_log( "wl_entity_props_save : no post found [ entity uri :: $entity_uri ]" );
        return;
    }

    // Save each property.
    foreach ( $props as $key => $values ) {
        wl_entity_props_save_prop( $post->ID, $key, $values, $mappings );
    }

}

/**
 * Save the specified prop.
 * @param int $post_id The post ID.
 * @param string $key The property name.
 * @param string $values The property values.
 * @param array $mappings An array of mappings from property URIs to field names.
 */
function wl_entity_props_save_prop( $post_id, $key, $values, $mappings ) {

    // The property is not found in mappings, then exit.
    if ( ! isset( $mappings[$key] ) ) {
        write_log( "wl_entity_props_save_prop : property not found in mappings [ post ID :: $post_id ][ key :: $key ][ values count :: " . count($values) . " ]" );

        return;
    }

    // Get the custom field name.
    $custom_field_name = $mappings[$key];

    write_log( "wl_entity_props_save_prop [ post ID :: $post_id ][ custom field name :: $custom_field_name ][ key :: $key ][ values count :: " . count($values) . " ]" );

    // Delete existing values for that custom field.
    delete_post_meta( $post_id, $custom_field_name );

    // Save the values.
    foreach ( $values as $value ) {
        add_post_meta( $post_id, $custom_field_name, $value );
    }
}

/**
 * Get the entity properties mappings to custom field names.
 */
function wl_entity_props_get_mappings() {

    write_log( "wl_entity_props_get_mappings");

    return array(
        'http://www.w3.org/2002/12/cal#dtstart'        => WL_CUSTOM_FIELD_CAL_DATE_START,
        'http://www.w3.org/2002/12/cal#dtend'          => WL_CUSTOM_FIELD_CAL_DATE_END,
        'http://www.w3.org/2003/01/geo/wgs84_pos#lat'  => WL_CUSTOM_FIELD_GEO_LATITUDE,
        'http://www.w3.org/2003/01/geo/wgs84_pos#long' => WL_CUSTOM_FIELD_GEO_LONGITUDE
    );
}