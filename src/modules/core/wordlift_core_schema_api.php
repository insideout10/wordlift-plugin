<?php


//TODO: move here the function converting schema slug <--> schema URI


/**
 * Retrieves the value of the specified property for the entity, where
 * 
 * @param $post_id numeric The numeric post ID.
 * @param $property_name string Name of the property (e.g. name, for the http://schema.org/name property)
 * 
 * @return array An array of values or NULL in case of no values (or error).
 */
function wl_schema_get_value( $post_id, $property_name ) {
    
    return wl_get_meta_value( $property_name, $post_id );
}

/**
 * Set the value for the specified property and post ID, where
 * 
 * @param $post_id numeric The numeric post ID.
 * @param $property_name string Name of the property (e.g. name, for the http://schema.org/name property)
 * @param $property_value mixed Value to save into the property.
 *
 * @return boolean The method returns true if everything went ok, an error string otherwise.  
 */
function wl_schema_set_value( $post_id, $property_name, $property_value ) {
    
    return true;
}


/**
 * Retrieves the entity types for the specified post ID, where
 * 
 * @param $post_id numeric The numeric post ID.
 * 
 * @return array Array of type(s) (e.g. Type, for the http://schema.org/Type)
 * or NULL in case of no values (or error).
 */
 function wl_schema_get_types( $post_id ) {
     return null;
 }

/**
 * Sets the entity type(s) for the specified post ID, where
 * 
 * @param $post_id numeric The numeric post ID
 * @param $type_names array An array of strings, each defining a type (e.g. Type, for the http://schema.org/Type)
 * 
 * @return boolean True if everything went ok, an error string otherwise.
 */
function wl_schema_set_types( $post_id, $type_names ) {
    
}

/**
 * Retrieves the list of supported properties for the specified type.
 * 
 * @param $type_name string Name of the type (e.g. Type, for the http://schema.org/Type)
 * 
 * @return array The method returns an array of supported properties for the type, e.g. (‘startDate’, ‘endDate’) for an Event.
 * You can call wl_schema_get_property_expected_type on each to know which data type they expect.
 */
function wl_schema_get_type_properties( $type_name ) {
    return array();
}

/**
 * Retrieves the property expected type, according to the schema.org specifications, where:
 * 
 * @param $property_name string Name of the property (e.g. name, for the http://schema.org/name property)
 * 
 * @return array of allowed types or NULL in case of property not found.
 * 
 * The following types are supported (defined as constants):
 * - WL_DATA_TYPE_URI
 * - WL_DATA_TYPE_DATE
 * - WL_DATA_TYPE_INTEGER
 * - WL_DATA_TYPE_DOUBLE
 * - WL_DATA_TYPE_BOOLEAN
 * - WL_DATA_TYPE_STRING
 * - a schema.org URI when the property type supports a schema.org entity (e.g. http://schema.org/Place)
 */
function wl_schema_get_property_expected_type( $property_name ) {
    return null;
}




// TODO: move here methods from wordlift_entity_functions.php