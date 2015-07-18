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

    if ( $posts = wl_core_get_posts( array(
        'get'               =>  'posts',
        'post_type'         =>  'entity',
        'related_to'        =>  $subject_id, 
        'as'                =>  'object',
        'with_predicate'    =>  $predicate,
        ) ) ) {
        return $post_ids;
    }
    // If wl_core_get_posts return false then an empty array is returned
    return array();
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
    
    if ( $post_ids = wl_core_get_posts( array(
        'get'               =>  'post_ids',
        'post_type'         =>  'entity',
        'related_to'        =>  $subject_id, 
        'as'                =>  'object',
        'with_predicate'    =>  $predicate,
        ) ) ) {
        return $post_ids;
    }
    // If wl_core_get_posts return false then an empty array is returned
    return array();
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

    if ( $posts = wl_core_get_posts( array(
        'get'               =>  'posts',
        'post_type'         =>  'post',
        'related_to'        =>  $object_id, 
        'as'                =>  'subject',
        'with_predicate'    =>  $predicate,
        ) ) ) {
        return $post_ids;
    }
    // If wl_core_get_posts return false then an empty array is returned
    return array();

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
    
    if ( $post_ids = wl_core_get_posts( array(
        'get'               =>  'post_ids',
        'post_type'         =>  'post',
        'related_to'        =>  $object_id, 
        'as'                =>  'subject',
        'with_predicate'    =>  $predicate,
        ) ) ) {
        return $post_ids;
    }
    // If wl_core_get_posts return false then an empty array is returned
    return array();
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
* It's used by wl_core_get_posts. Implements a subset of WpQuery object 
* @see https://codex.wordpress.org/Class_Reference/WP_Query
* Arguments validation is delegated to wl_core_get_posts method.
* Form the array like this:
* <code>
* $args = array(
*   'get' => 'posts', // posts, post_ids, relations, relation_ids 
*   'first' => n,
*   'related_to'      => 10,          // the post/s / entity/ies id / ids
*   'as'   => [ subject | object ],
*   'with_predicate'   => [ what | where | when | who ], // null as default value
*   'post_type' => [ post | entity ] 
* );
* </code>
*
* @param array args Arguments to be used in the query builder.
*
* @return string | false String representing a sql statement, or false in case of error 
*/
function wl_core_sql_query_builder( $args ) {

    // Prepare interaction with db
    global $wpdb;
    // Retrieve Wordlift relation instances table name
    $table_name = wl_core_get_relation_instances_table_name();
    // Sql Join with posts table is required only if 'get' is 'posts' or 'post_ids'
    $is_join_required = ( in_array( $args[ 'get' ], array( 'posts', 'post_ids') ) );

    // Sql Action
    $sql = "SELECT ";
    // Determine what has to be returned depending on 'get' argument value
    switch ( $args[ 'get' ] ) {
        case 'posts':
            $sql .= "p.*";
            break;
        case 'post_ids':
            $sql .= "p.id";
            break;
        case 'relations':
            $sql .= "r.*";
            break;
        case 'relation_ids':
            $sql .= "r.id";
            break;
    }

    // Sql Inner Join if needed 
    if ( $is_join_required ) {
        // If we look for posts related as objects the JOIN has to be done with the object_id column and viceversa
        $join_column = $args[ 'as' ] . "_id"; 
        
        $sql .= " FROM $wpdb->posts as p JOIN $table_name as r ON p.id = r.$join_column";
        // Sql add post type filter
        $sql .= $wpdb->prepare( " AND p.post_type = %s AND", $args[ 'post_type' ] );

    } else {
        $sql .= " FROM $table_name as r WHERE";    
    }
    
    // Add filtering condition
    // If we look for posts related as objects this means that 
    // related_to is a reference for a subject: subject_id is the filtering column
    // If we look for posts related as subject this means that 
    // related_to is reference for an object: object_id is the filtering column
    
    // TODO implement also array, not only single integer
    $filtering_column = ( 'object' == $args[ 'as' ] ) ? "subject_id" : "object_id";
    $sql .= $wpdb->prepare( " r.$filtering_column = %d", $args[ 'related_to' ] );

    // Add predicate filter if required
    if ( isset( $args[ 'with_predicate' ] ) ) {
        // Sql Inner Join clausole 
        $sql .= $wpdb->prepare( " AND r.predicate = %s", $args[ 'with_predicate' ] );
    }
    if ( isset( $args[ 'first' ] ) && is_integer( $args[ 'first' ] ) ) {
        // Sql Inner Join clausole 
        $sql .= $wpdb->prepare( " LIMIT %d", $args[ 'first'] );
    }
    // Close sql statement
    $sql .= ";";

    return $sql;

}

/**
* Perform a query on db depending on args
* It's responsible for argument validations  
* @uses wl_core_sql_query_builder to compose the sql statement
* @uses $wpdb instance to perform the query
*
* @param array args Arguments to be used in the query builder.
*
* @return (array) List of WP_Post objects or list of WP_Post ids. False in case of error or invalid params
*/
function wl_core_get_posts( $args ) {

    // Merge given args with defaults args value
    $args = array_merge( array(
        'with_predicate' => null,
        'as' => 'subject',
        'post_type' => 'post',
        'get' => 'posts'
    ), $args);

    // Arguments validation rules
    if ( !isset( $args[ 'related_to' ] ) || !is_integer( $args['related_to'] ) ) {
        return false;
    }
    if ( !in_array( $args[ 'get' ], array( 'posts', 'post_ids', 'relations', 'relation_ids' ) ) )  {
        return false;
    }
    if ( !in_array( $args[ 'as' ], array( 'object', 'subject' ) ) )  {
        return false;
    }
    if ( !in_array( $args[ 'post_type' ], array( 'post', 'entity' ) ) )  {
        return false;
    }
    if ( null != $args[ 'with_predicate' ] )  {
        if ( !wl_core_check_relation_predicate_is_supported( $args[ 'with_predicate' ] ) ) {
            return false;
        } 
    }
    // Prepare interaction with db
    global $wpdb;
    // Build sql statement with given arguments
    $sql_statement = wl_core_sql_query_builder( $args ); 
    wl_write_log( "Going to execute sql statement: $sql_statement " );

    $results = array();
    // If ids are required, returns a one-dimensional array containing ids.
    // Otherwise an array of associative arrays representing the post | relation object
    if ( in_array( $args[ 'get' ], array( 'post_ids', 'relation_ids') ) ) {
        # See https://codex.wordpress.org/Class_Reference/wpdb#SELECT_a_Column
        $results = $wpdb->get_col( $sql_statement );
    } else {
        $results = $wpdb->get_results( $sql_statement, ARRAY_A );
    }
    // If there were an error performing the query then false is returned
    if ( !empty( $wpdb->last_error ) ) {
        return false;
    }
    // Finally
    return $results;
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