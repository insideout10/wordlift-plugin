<?php


//TODO: move here the function converting schema slug <--> schema URI


/**
 * Retrieves the value of the specified property for the entity, where
 * 
 * @param $post_id numeric The numeric post ID.
 * @param $property_name string Name of the property (e.g. name, for the http://schema.org/name property).
 * 
 * @return array An array of values or NULL in case of no values (or error).
 */
function wl_schema_get_value( $post_id, $property_name ) {
    
	// Property name must be defined.
	if ( ! isset( $property_name ) || is_null( $property_name ) ) {
		return null;
	}
        
        // store eventual schema name in  different variable
        $property_schema_name = wl_build_full_schema_uri_from_schema_slug( $property_name );

	// Establish entity id.
	if ( is_null( $post_id ) || ! is_numeric( $post_id ) ) {
		$post_id = get_the_ID();
		if ( is_null( $post_id ) || ! is_numeric( $post_id ) ) {
			return null;
		}
	}
        
        // Get custom fields.
	$term_mapping = wl_entity_taxonomy_get_custom_fields( $post_id );
        wl_write_log('piedo term'. var_export( $term_mapping, true) );

        // Search for the required meta value (by constant name or schema name)
	foreach ( $term_mapping as $wl_constant => $property_info ) {
		$found_constant  = ( $wl_constant == $property_name );
		$found_predicate = ( isset( $property_info['predicate'] ) && $property_info['predicate'] == $property_schema_name );
		if ( $found_constant || $found_predicate ) {
			return get_post_meta( $post_id, $wl_constant );
		}
	}

	return null;
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
    
    // Some checks on the parameters
    if ( !is_numeric( $post_id ) || is_null( $property_name ) ||empty( $property_value ) || is_null( $property_value ) ) {
            return false;
    }
    
    // Build full schema uri if necessary
    $property_name = wl_build_full_schema_uri_from_schema_slug( $property_name );
    
    // Get accepted properties
    $accepted_fields = wl_entity_taxonomy_get_custom_fields( $post_id );
    
    // Find the name of the custom-field managing the schema property
    foreach( $accepted_fields as $wl_constant => $field ) {
        if( $field['predicate'] == $property_name ) {
            
            add_post_meta( $post_id, $field['predicate'], $property_value );
            // TODO: manage complementary relation as made for posts           
         
            return true;
        }
    }
    
    return false;
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
     
    // Some checks on the parameters
    if ( !is_numeric( $post_id ) ) {
            return null;
    }
    
    $type = wl_entity_type_taxonomy_get_type( $post_id );
    
    if( isset( $type['uri'] ) ) {
        return array( $type['uri'] );
    }
    
    return null;
 }

/**
 * Sets the entity type(s) for the specified post ID. Support is now for only one type per entity.
 * 
 * @param $post_id numeric The numeric post ID
 * @param $type_names array An array of strings, each defining a type (e.g. Type, for the http://schema.org/Type)
 * 
 * @return boolean True if everything went ok, an error string otherwise.
 */
function wl_schema_set_types( $post_id, $type_names ) {
    
    // Some checks on the parameters
    if ( !is_numeric( $post_id ) || empty( $type_names ) || is_null( $type_names ) ) {
            return null;
    }
    
    // TODO: support more than one type
    if( is_array( $type_names ) ) {
        $type_names = $type_names[0];
    }
    
    // Build full schema uri if necessary
    $type_names = wl_build_full_schema_uri_from_schema_slug( $type_names );
    
    // Actually sets the taxonomy type
    wl_set_entity_main_type( $post_id, $type_names );
}

/**
 * Retrieves the list of supported properties for the specified type.
 * @uses *wl_entity_taxonomy_get_custom_fields* to retrieve all custom fields (type properties)
 * @uses *wl_build_full_schema_uri_from_schema_slug* to convert a schema slug to full uri
 * 
 * @param $type_name string Name of the type (e.g. Type, for the http://schema.org/Type)
 * 
 * @return array The method returns an array of supported properties for the type, e.g. (‘startDate’, ‘endDate’) for an Event.
 * You can call wl_schema_get_property_expected_type on each to know which data type they expect.
 */
function wl_schema_get_type_properties( $type_name ) {
    
    // Build full schema uri if necessary
    $type_name = wl_build_full_schema_uri_from_schema_slug( $type_name );
    
    // Get all custom fields
    $all_types_and_fields = wl_entity_taxonomy_get_custom_fields();
    
    $schema_root_address = 'http://schema.org/';
    $type_properties = array();
    
    // Search for the entity type which has the requested name as uri
    if( isset( $all_types_and_fields[$type_name] ) ) {
        foreach( $all_types_and_fields[$type_name] as $field ) {
            // Convert to schema slug and store in array
            $type_properties[] = str_replace( $schema_root_address, '', $field['predicate']);
        }
    }
    
    return $type_properties;
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
    
    // This is the actual structure of a custom_field.
    /*
     * WL_CUSTOM_FIELD_LOCATION       => array(
     *      'predicate'   => 'http://schema.org/location',
     *      'type'        => WL_DATA_TYPE_URI,
     *      'export_type' => 'http://schema.org/PostalAddress',
     *      'constraints' => array(
     *              'uri_type' => 'Place'
     *      )
     *  )
     */
    
    // Build full schema uri if necessary
    $property_name = wl_build_full_schema_uri_from_schema_slug( $property_name );
    
    // Get all custom fields
    $all_types_and_fields = wl_entity_taxonomy_get_custom_fields();

    $expected_types = null;
    
    // Search for the entity type which has the requested name as uri
    $found = false;
    foreach( $all_types_and_fields as $type_fields ) {
        foreach( $type_fields as $field ) {
            if( $field['predicate'] == $property_name ) {
                
                $expected_types = array();
                
                // Does the property accept a specific schema type?
                if( isset( $field['constraints'] ) && isset( $field['constraints']['uri_type'] ) ) {
                    // Take note of expected schema type
                    $expected_types[] = wl_build_full_schema_uri_from_schema_slug( $field['constraints']['uri_type'] );
                } else {
                    // Take note of expected type
                    $expected_types[] = $field['type'];
                }
                
                // We found the property, we can exit the cycles
                $found = true;
            }
            
            if( $found ) {
                break;
            }
        }
        
        if( $found ) {
            break;
        }
    }
    
    return $expected_types;
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