<?php

/**
 * Checks if a relation is supported
 * 
 * @param string $predicate Name of the relation: 'what' | 'where' | 'when' | 'who'
 * 
 * @return boolean Return true if supported, false otherwise
 */
function wl_core_check_relation_predicate_is_supported( $predicate ) {
    
    return in_array( $predicate, array(
        WL_WHAT_RELATION, WL_WHEN_RELATION, WL_WHERE_RELATION, WL_WHO_RELATION
    ) );
}

/**
 * Return the wordlift relation instances table name
 * 
 * @return string Return the wordlift relation instances table name
 */
function wl_core_get_relation_instances_table_name() { 

    global $wpdb;
    $table_name = $wpdb->prefix . WL_DB_RELATION_INSTANCES_TABLE_NAME;
    return $table_name;
}



/**
* Create a single relation instance if the given instance does not exist on the table
*
* @param int $subject_id The post ID | The entity post ID.
* @param string $predicate Name of the relation: 'what' | 'where' | 'when' | 'who'
* @param int $object_id The entity post ID.
*
* @return (integer|boolean) Return then relation instance ID or false
*/
function wl_core_add_relation_instance( $subject_id, $predicate, $object_id ) {
    
    // Checks on subject and object
    if( !is_numeric( $subject_id ) || !is_numeric( $object_id ) ) {
        return false;
    }
    
    // Checks on the given relation
    if( !wl_core_check_relation_predicate_is_supported( $predicate ) ) {
        return false;
    }
    
    // Prepare interaction with db
    global $wpdb;

    
    // Checks passed. Add relation if not exists
    // See https://codex.wordpress.org/Class_Reference/wpdb#REPLACE_row
    $wpdb->replace( 
	   wl_core_get_relation_instances_table_name(), 
	   array( 
        'subject_id' => $subject_id,
		'predicate' => $predicate, 
		'object_id' => $object_id 
	   ), 
	   array( '%d', '%s', '%d'	) 
    );
    
    // Return record id
    return $wpdb->insert_id;
}

/**
* Remove a given relation instance 
*
* @param int $subject_id The post ID | The entity post ID.
* @param string $predicate Name of the relation: 'what' | 'where' | 'when' | 'who'
* @param int $object_id The entity post ID.
*
* @return (boolean) False for failure. True for success.
*/
function wl_core_delete_relation_instance( $subject_id, $predicate, $object_id ) {
    
}

/**
* Create multiple relation instances 
* @uses wl_add_relation_instance to create each single instance
* 
* @param int $subject_id The post ID | The entity post ID.
* @param string $predicate Name of the relation: 'what' | 'where' | 'when' | 'who'
* @param array $object_ids The entity post IDs collection.
*
* @return (integer|boolean) Return the relation instances IDs or false
*/
function wl_core_add_relation_instances( $subject_id, $predicate, $object_ids ) {
    
    // Checks on subject and object
    if( !is_numeric( $subject_id ) ) {
        return false;
    }
    
    // Checks on the given relation
    if( !wl_core_check_relation_predicate_is_supported( $predicate ) ) {
        return false;
    }
    
    // Check $object_ids is an array
    if( !is_array( $object_ids ) || empty( $object_ids ) ) {
        return false;
    }
    
    // Call method to check and add each single relation
    $inserted_records_ids = array();
    foreach ( $object_ids as $object_id ) {
        $new_record_id = wl_core_add_relation_instance( $subject_id, $predicate, $object_id );
        $inserted_records_ids[] = $new_record_id;
    }
    
    return $inserted_records_ids;
}

/**
* Remove all relation instances for a given $subject_id and $predicate
* If $predicate is omitted, $predicate filter is not applied
*
* @param int $subject_id The post ID | The entity post ID.
* @param string $predicate Name of the relation: null | 'what' | 'where' | 'when' | 'who'
*
* @return (boolean) False for failure. True for success.
*/
function wl_core_delete_relation_instances( $subject_id, $predicate = null ) {

}

/**
* Find all entities related to a given $subject_id
* If $predicate is omitted, $predicate filter is not applied 
*
* @param int $subject_id The post ID | The entity post ID.
* @param string $predicate Name of the relation: null | 'what' | 'where' | 'when' | 'who'
*
* @return (array) Array of post entity objects.
*/
function wl_core_get_related_entities( $subject_id, $predicate = null ) {

}

/**
* Find all entity ids related to a given $subject_id
* If $predicate is omitted, $predicate filter is not applied 
*
* @param int $subject_id The post ID | The entity post ID.
* @param string $predicate Name of the relation: null | 'what' | 'where' | 'when' | 'who'
*
* @return (array) Array of ids.
*/
function wl_core_get_related_entity_ids( $subject_id, $predicate = null ) {
    
    // Check $subject_id
    if( !is_numeric( $subject_id ) ) {
        return array();
    }
    
    // Check valid $predicate (must be null or one of the 4W)
    if( !is_null( $predicate ) && !wl_core_check_relation_predicate_is_supported( $predicate ) ) {
        return array(); 
    }
    
    // Prepare interaction with db
    global $wpdb;
    
    // Retrieve data
    $query = 'SELECT object_id FROM ' . wl_core_get_relation_instances_table_name() . ' WHERE ';
    if( !is_null( $predicate ) ) {
        $query .= 'predicate=' . $predicate . ' AND ';
    }    
    $query .= 'subject_id=' . $subject_id;
    $results = $wpdb->get_results( $query );
    
    $ids = array();
    foreach( $results as $res ) {
        $ids[] = $res->object_id;
    }
    
    return array_unique( $ids );
}

/**
* Find all posts related to a given $object_id
* If $predicate is omitted, $predicate filter is not applied 
*
* @param int $object_id The entity ID.
* @param string $predicate Name of the relation: null | 'what' | 'where' | 'when' | 'who'
*
* @return (array) Array of objects.
*/
function wl_core_get_related_posts( $object_id, $predicate = null ) {

}

/**
* Find all post ids related to a given $object_id
* If $predicate is omitted, $predicate filter is not applied 
*
* @param int $object_id The entity ID.
* @param string $predicate Name of the relation: null | 'what' | 'where' | 'when' | 'who'
*
* @return (array) Array of post ids.
*/
function wl_core_get_related_post_ids( $object_id, $predicate = null ) {
    
    // Check $subject_id
    if( !is_numeric( $object_id ) ) {
        return array();
    }
    
    // Check valid $predicate (must be null or one of the 4W)
    if( !is_null( $predicate ) && !wl_core_check_relation_predicate_is_supported( $predicate ) ) {
        return array(); 
    }
    
    // Prepare interaction with db
    global $wpdb;
    
    // Retrieve data
    $query = 'SELECT subject_id FROM ' . wl_core_get_relation_instances_table_name() . ' WHERE ';
    if( !is_null( $predicate ) ) {
        $query .= 'predicate=' . $predicate . ' AND ';
    }    
    $query .= 'object_id=' . $object_id;
    $results = $wpdb->get_results( $query );
    
    $ids = array();
    foreach( $results as $res ) {
        $ids[] = $res->suject_id;
    }
    
    return array_unique( $ids );
}

/**
* Find all relation instances for a given $subject_id
* If $predicate is omitted, $predicate filter is not applied 
*
* @param int $subject_id The post ID | The entity post ID.
* @param string $predicate Name of the relation: null | 'what' | 'where' | 'when' | 'who'
*
* @return (array) Array of relation instance objects.
*/
function wl_core_get_relation_instances_for( $subject_id, $predicate = null ) {

}

/**
* Define a sql statement between wp_posts and wp_wl_relation_instances tables  
* It's used by wl_get_posts. Implements a subset of WpQuery object 
* @see https://codex.wordpress.org/Class_Reference/WP_Query
*
* Form the array like this:
* <code>
* $args = array(
*   'id'      => 'foo',          // the id
*   'predicate'   => [ what | where | when | who | null ],
*   'predicate_scope'   => [ subject_id | object_id ],
*   'post_type' => [ posts | entities ] 
*   'numberposts' => n,
*   'fields' => 'ids'   
* );
* </code>
*
* @param array args Arguments to be used in the query builder.
*
* @return string String representing a sql statement 
*/
function wl_core_sql_query_builder( $args ) {

    // Prepare interaction with db
    global $wpdb;

}

/**
* Define a sql statement between wp_posts and wp_wl_relation_instances tables  
* It's used by wl_get_posts. Implements a subset of WpQuery object 
* @uses wl_core_sql_query_builder to compose the sql statement
* @uses WP_Query object to perform the query
*
* @param array args Arguments to be used in the query builder.
*
* @return (array) List of WP_Post objects or list of WP_Post ids. 
*/
function wl_core_get_posts( $args ) {

}

/* OLD METHODS */

/**
 * Get the name of the post meta complementary to the one given as parameter.
 * See *wordlift_core_constants.php* for more details.
 *
 * @param string $meta_name Name of the meta
 *
 * @return string The complementary meta, if any, otherwise null.
 */
function wl_core_get_complementary_relation( $meta_name ) {

    $mapping = unserialize(WL_CORE_POST_ENTITY_RELATIONS_MAPPING);
    if (isset($mapping[$meta_name])) {
        return $mapping[$meta_name];
    }

    return null;
}

/**
 * Reset relations between a post/entity and another ( A --> B ).
 * The complmentary relation is reset too ( B --> A ).
 *
 * @param int $subject_id The post ID.
 * @param string $relation Name of the relation.
 *
 */
function wl_core_reset_relation_between_posts_and_entities( $subject_id, $relation ) {
    
    // Return if parameters are not set
    if( empty( $subject_id ) || empty( $relation ) ) {
        return;
    }
    
    // Get related
    $related = wl_core_get_related_post_and_entities( $subject_id, $relation );
    
    // Reset all related
    delete_post_meta( $subject_id, $relation );
    
    // Get the complementary relation, if exists

    $inverseRelation = wl_core_get_complementary_relation( $relation );
    // Add the complementary relation: B1 --> A, B2 --> A, ... , BN --> A
    if ( !is_null( $inverseRelation ) && !empty( $inverseRelation ) ) {           
        foreach( $related as $rel_id ) {
            wl_write_log("Delete relation $inverseRelation from $rel_id to $subject_id");
            delete_post_meta( (int) $rel_id, $inverseRelation, $subject_id );
        }
    }
}

/**
 * Add a relation between a post/entity and another ( A --> B ).
 * The complmentary relation is also added ( B --> A ).
 *
 * @param int $subject_id The post ID.
 * @param string $relation Name of the relation.
 * @param array $object_ids Ids of the related posts.
 *
 */
function wl_core_add_relation_between_posts_and_entities( $subject_id, $relation, $object_ids ) {
    
    // Return if parameters are not set
    if( empty( $subject_id ) || empty( $object_ids ) || empty( $relation ) ) {
        return;
    }
    
    // Ensure $object_ids is an array.
    if ( !is_array( $object_ids ) ) {
        $object_ids = array( $object_ids );
    }
    
    // Add relation between subject and objects: A --> B1, A-->B2, ... , A -->BN
    wl_core_merge_old_related_with_new( $subject_id, $relation, $object_ids );

    // Get the complementary relation, if exists
    $inverseRelation = wl_core_get_complementary_relation( $relation );

    // Add the complementary relation: B1 --> A, B2 --> A, ... , BN --> A
    if ( !is_null( $inverseRelation ) && !empty( $inverseRelation ) ) {
        foreach ( $object_ids as $object_id ) {
            // Get the existing complementary meta values and merge them together.
            wl_core_merge_old_related_with_new( $object_id, $inverseRelation, $subject_id );
        }
    }
}

/**
 * Update post meta with new related ids.
 * 
 * @used by *wl_core_add_relation_between_posts_and_entities*
 *
 * @param int $subject_id The post ID.
 * @param string $relation Name of the relation.
 * @param array $new_related_ids Ids of the related posts to add.
 * 
 */
function wl_core_merge_old_related_with_new( $subject_id, $relation, $new_related_ids ) {
    
    // Ensure the argument is an array.
    if ( !is_array( $new_related_ids ) ) {
        $new_related_ids = array( $new_related_ids );
    }    

    // Retrieve related already present in db.
    $related = wl_core_get_related_post_and_entities( $subject_id, $relation );

    // Merge old related with new.
    if( empty( $related ) ) {
        $related = $new_related_ids;
    } else {
        $related = array_merge( $related, $new_related_ids );
    }
    
    // Take away duplicates
    $related = array_unique( $related );
    
    // Take away stored meta values
    delete_post_meta( $subject_id, $relation );
    
    // Add new values (combined with old ones)
    foreach( $related as $rel_id ) {
        // Add meta value (convert to int if the id is a string)
        // WARN if a string is given an invalid value 0 is saved
        add_post_meta( $subject_id, $relation, (int) $rel_id );
    }
}

/**
 * Get a post/entity related ids.
 *
 * @param int $subject_id The post ID.
 * @param string $relation Name of the relation.
 * 
 * @return array Ids of the related posts/entities.
 * 
 */
function wl_core_get_related_post_and_entities( $subject_id, $relation ) {
    
    // TODO: add some checks on the arguments, for example the existence of the relation.
    
    $objects_ids = get_post_meta( $subject_id, $relation ); 
    // get_post_meta returns an array in any case, so no need to check
    
    return $objects_ids;
}

/**
 * Get the IDs of the entities related to the specified entity.
 *
 * @param int $entity_id The entity ID.
 * @param string $field_name Name of the meta
 *
 * @return array An array of entity IDs related to the one specified.
 */
function wl_get_related_entities( $entity_id, $field_name = WL_CUSTOM_FIELD_RELATED_ENTITIES ) {

    return wl_core_get_related_post_and_entities( $entity_id, $field_name );
}

/**
 * Add the related entity IDs for the specified entity ID.
 *
 * @param int $entity_id An entity ID.
 * @param int|array $new_entities_ids An array of related entity IDs.
 * @param string $field_name Name of the meta
 */
function wl_add_related_entities($entity_id, $new_entities_ids, $field_name = WL_CUSTOM_FIELD_RELATED_ENTITIES) {
    
    // TODO: check that only entities are passed (no posts)
    
    wl_core_add_relation_between_posts_and_entities( $entity_id, $field_name, $new_entities_ids );
}

/**
 * Set the related entity IDs for the specified entity ID.
 *
 * @param int $entity_id An entity ID.
 * @param int|array $new_entities_ids An array of related entity IDs.
 * @param string $field_name Name of the meta
 */
function wl_set_related_entities( $entity_id, $new_entities_ids, $field_name = WL_CUSTOM_FIELD_RELATED_ENTITIES ) {
    
    wl_core_reset_relation_between_posts_and_entities( $entity_id, $field_name );
    wl_add_related_entities( $entity_id, $new_entities_ids, $field_name );
}

/**
 * Get the post ids that reference the specified entity.
 *
 * @param int $entity_id The entity ID.
 * @param string $field_name Name of the meta (used for the 4W)
 *
 * @return array An array of post IDs.
 */
function wl_get_referencing_posts( $entity_id, $field_name = WL_CUSTOM_FIELD_IS_REFERENCED_BY_POSTS) {
    
    return wl_core_get_related_post_and_entities( $entity_id, $field_name );
}

/**
 * Set the referenced entity IDs for the specified post ID.
 *
 * @param int $post_id A post ID.
 * @param int|array $new_entity_ids An array of referenced entity IDs.
 * @param string $field_name Name of the meta (used for the 4W)
 */
function wl_set_referenced_entities( $post_id, $new_entity_ids, $field_name = WL_CUSTOM_FIELD_REFERENCED_ENTITIES ) {
    
    wl_core_reset_relation_between_posts_and_entities( $post_id, $field_name );
    wl_add_referenced_entities( $post_id, $new_entity_ids, $field_name );
}

/**
 * Add the referenced entity IDs for the specified post ID.
 *
 * @param int $post_id A post ID.
 * @param int|array $new_entity_ids An array of referenced entity IDs.
 * @param string $field_name Name of the meta (used for the 4W)
 */
function wl_add_referenced_entities( $post_id, $new_entity_ids, $field_name = WL_CUSTOM_FIELD_REFERENCED_ENTITIES ) {
    
    wl_core_add_relation_between_posts_and_entities( $post_id, $field_name, $new_entity_ids );
}

/**
 * Get the referenced entity IDs for the specified post ID.
 *
 * @param int $post_id A post ID.
 * @param string $field_name Name of the meta (used for the 4W)
 */
function wl_get_referenced_entities( $post_id, $field_name = WL_CUSTOM_FIELD_REFERENCED_ENTITIES ) {

    return wl_core_get_related_post_and_entities( $post_id, $field_name );
}

/**
 * Get the IDs of 4W related to the specified post.
 *
 * @param int $post_id The post ID.
 *
 * @return array An array containing the 4W entitities ids. In case of non existent post, an empty array is returned.
 */
function wl_get_post_4w_entities($post_id) {

    // Return if post does not exists
    if (get_post_status($post_id) == False) {
        return array();
    }

    return array(
        WL_CUSTOM_FIELD_WHAT_ENTITIES => wl_get_referenced_entities($post_id, WL_CUSTOM_FIELD_WHAT_ENTITIES),
        WL_CUSTOM_FIELD_WHERE_ENTITIES => wl_get_referenced_entities($post_id, WL_CUSTOM_FIELD_WHERE_ENTITIES),
        WL_CUSTOM_FIELD_WHEN_ENTITIES => wl_get_referenced_entities($post_id, WL_CUSTOM_FIELD_WHEN_ENTITIES),
        WL_CUSTOM_FIELD_WHO_ENTITIES => wl_get_referenced_entities($post_id, WL_CUSTOM_FIELD_WHO_ENTITIES)
    );
}

/**
 * Get the IDs of posts for which an entity is a 4W.
 *
 * @param int $entity_id The entity ID.
 *
 * @return array An array containing the posts ids. In case of non existent entity, an empty array is returned.
 */
function wl_get_entity_is_4w_for_posts($entity_id) {

    // Return if entity does not exists
    if (get_post_status($entity_id) == False) {
        return array();
    }

    return array(
        WL_CUSTOM_FIELD_IS_WHAT_FOR_POSTS => wl_get_referencing_posts($entity_id, WL_CUSTOM_FIELD_IS_WHAT_FOR_POSTS),
        WL_CUSTOM_FIELD_IS_WHERE_FOR_POSTS => wl_get_referencing_posts($entity_id, WL_CUSTOM_FIELD_IS_WHERE_FOR_POSTS),
        WL_CUSTOM_FIELD_IS_WHEN_FOR_POSTS => wl_get_referencing_posts($entity_id, WL_CUSTOM_FIELD_IS_WHEN_FOR_POSTS),
        WL_CUSTOM_FIELD_IS_WHO_FOR_POSTS => wl_get_referencing_posts($entity_id, WL_CUSTOM_FIELD_IS_WHO_FOR_POSTS)
    );
}