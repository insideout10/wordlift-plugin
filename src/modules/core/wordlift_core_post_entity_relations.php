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
* @uses $wpdb->replace() to perform the query
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
* @uses $wpdb->delete() to perform the query
*
* @param int $subject_id The post ID | The entity post ID.
* @param string $predicate Name of the relation: 'what' | 'where' | 'when' | 'who'
* @param int $object_id The entity post ID.
*
* @return (boolean) False for failure. True for success.
*/
function wl_core_delete_relation_instance( $subject_id, $predicate, $object_id ) {

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

    wl_write_log( "Going to delete relation instace [ subject_id :: $subject_id ] [ object_id :: $object_id ] [ predicate :: $predicate ]"); 
    
    // @see ttps://codex.wordpress.org/it:Riferimento_classi/wpdb#DELETE_di_righe
    $wpdb->delete(
       wl_core_get_relation_instances_table_name(), 
       array( 
        'subject_id' => $subject_id,
        'predicate' => $predicate, 
        'object_id' => $object_id 
       ), 
       array( '%d', '%s', '%d'  ) 
    );

    return true;    
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
*
* @return (boolean) False for failure. True for success.
*/
function wl_core_delete_relation_instances( $subject_id ) {

    // Checks on subject and object
    if( !is_numeric( $subject_id ) ) {
        return false;
    }
    
    // Prepare interaction with db
    global $wpdb;

    wl_write_log( "Going to delete relation instances [ subject_id :: $subject_id ]"); 
    
    // @see ttps://codex.wordpress.org/it:Riferimento_classi/wpdb#DELETE_di_righe
    $wpdb->delete(
       wl_core_get_relation_instances_table_name(), 
       array( 
        'subject_id' => $subject_id,
       ), 
       array( '%d' ) 
    );

    return true;

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
    wl_write_log("debug Abbiamo un problema");
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

    if ( $relation_instances = wl_core_get_posts( array(
        'get'               =>  'relations',
        'related_to'        =>  $subject_id, 
        'as'                =>  'subject',
        'with_predicate'    =>  $predicate,
        ) ) ) {
        return $relation_instances;
    }
    // If wl_core_get_posts return false then an empty array is returned
    return array();
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
    $is_looking_for_posts = ( in_array( $args[ 'get' ], array( 'posts', 'post_ids') ) );

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
    if ( $is_looking_for_posts ) {
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
    if ( $is_looking_for_posts ) {  
        $filtering_column = ( 'object' == $args[ 'as' ] ) ? "subject_id" : "object_id";
        $sql .= $wpdb->prepare( " r.$filtering_column = %d", $args[ 'related_to' ] );
    } else {
        $sql .= $wpdb->prepare( " r." . $args[ 'as' ] . "_id = %d", $args[ 'related_to' ] );
    }

    // Add predicate filter if required
    if ( isset( $args[ 'with_predicate' ] ) ) {
        // Sql Inner Join clausole 
        $sql .= $wpdb->prepare( " AND r.predicate = %s", $args[ 'with_predicate' ] );
    }
    // Add a group by clousole to avoid duplicated rows
    if ( $is_looking_for_posts ) {
        $sql .= " GROUP BY p.id";
    }
    if ( isset( $args[ 'first' ] ) && is_numeric( $args[ 'first' ] ) ) {
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
    if ( !isset( $args[ 'related_to' ] ) || !is_numeric( $args['related_to'] ) ) {
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