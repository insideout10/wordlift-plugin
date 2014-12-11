<?php
/**
 * This file provides Entity properties-related functions.
 */


/**
 * Get the entity properties mappings to custom field names.
 */
function wl_entity_props_get_mappings() {

    wl_write_log( "wl_entity_props_get_mappings");

    return array(
        'http://www.w3.org/2002/12/cal#dtstart'        => WL_CUSTOM_FIELD_CAL_DATE_START,
        'http://www.w3.org/2002/12/cal#dtend'          => WL_CUSTOM_FIELD_CAL_DATE_END,
        'http://www.w3.org/2003/01/geo/wgs84_pos#lat'  => WL_CUSTOM_FIELD_GEO_LATITUDE,
        'http://www.w3.org/2003/01/geo/wgs84_pos#long' => WL_CUSTOM_FIELD_GEO_LONGITUDE
    );
}