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
		WL_WHAT_RELATION,
		WL_WHEN_RELATION,
		WL_WHERE_RELATION,
		WL_WHO_RELATION,
	) );
}

/**
 * Return an array of validation rules used by wl_core_get_posts
 *
 * @return array in the format field => (array) accepeted_values
 */
function wl_core_get_validation_rules() {

	return unserialize( WL_CORE_GET_POSTS_VALIDATION_RULES );
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
 * @param int    $subject_id The post ID | The entity post ID.
 * @param string $predicate  Name of the relation: 'what' | 'where' | 'when' | 'who'
 * @param int    $object_id  The entity post ID.
 *
 * @uses   $wpdb->replace() to perform the query
 * @return (integer|boolean) Return then relation instance ID or false
 */
function wl_core_add_relation_instance( $subject_id, $predicate, $object_id ) {

	// Checks on subject and object
	if ( ! is_numeric( $subject_id ) || ! is_numeric( $object_id ) ) {
		return false;
	}

	// Checks on the given relation
	if ( ! wl_core_check_relation_predicate_is_supported( $predicate ) ) {
		return false;
	}

	// Prepare interaction with db
	global $wpdb;

	// Checks passed. Add relation if not exists:
	// See https://codex.wordpress.org/Class_Reference/wpdb#REPLACE_row
	$wpdb->replace(
		wl_core_get_relation_instances_table_name(),
		array(
			'subject_id' => $subject_id,
			'predicate'  => $predicate,
			'object_id'  => $object_id,
		),
		array( '%d', '%s', '%d' )
	);

	// Return record id
	return $wpdb->insert_id;
}

/**
 * Remove a given relation instance
 * @uses   $wpdb->delete() to perform the query
 *
 * @param int    $subject_id The post ID | The entity post ID.
 * @param string $predicate  Name of the relation: 'what' | 'where' | 'when' | 'who'
 * @param int    $object_id  The entity post ID.
 *
 * @return (boolean) False for failure. True for success.
 */
function wl_core_delete_relation_instance( $subject_id, $predicate, $object_id ) {

	// Checks on subject and object
	if ( ! is_numeric( $subject_id ) || ! is_numeric( $object_id ) ) {
		return false;
	}

	// Checks on the given relation
	if ( ! wl_core_check_relation_predicate_is_supported( $predicate ) ) {
		return false;
	}

	// Prepare interaction with db
	global $wpdb;

	wl_write_log( "Going to delete relation instace [ subject_id :: $subject_id ] [ object_id :: $object_id ] [ predicate :: $predicate ]" );

	// @see ttps://codex.wordpress.org/it:Riferimento_classi/wpdb#DELETE_di_righe
	$wpdb->delete(
		wl_core_get_relation_instances_table_name(),
		array(
			'subject_id' => $subject_id,
			'predicate'  => $predicate,
			'object_id'  => $object_id,
		),
		array( '%d', '%s', '%d' )
	);

	return true;
}

/**
 * Create multiple relation instances
 * @uses   wl_add_relation_instance() to create each single instance
 *
 * @param int    $subject_id The post ID | The entity post ID.
 * @param string $predicate  Name of the relation: 'what' | 'where' | 'when' | 'who'
 * @param array  $object_ids The entity post IDs collection.
 *
 * @return (integer|boolean) Return the relation instances IDs or false
 */
function wl_core_add_relation_instances( $subject_id, $predicate, $object_ids ) {

	// Checks on subject and object
	if ( ! is_numeric( $subject_id ) ) {
		return false;
	}

	// Checks on the given relation
	if ( ! wl_core_check_relation_predicate_is_supported( $predicate ) ) {
		return false;
	}

	// Check $object_ids is an array
	if ( ! is_array( $object_ids ) || empty( $object_ids ) ) {
		return false;
	}

	// Call method to check and add each single relation
	$inserted_records_ids = array();
	foreach ( $object_ids as $object_id ) {
		$new_record_id          = wl_core_add_relation_instance( $subject_id, $predicate, $object_id );
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
	if ( ! is_numeric( $subject_id ) ) {
		return false;
	}

	// Prepare interaction with db
	global $wpdb;

	// wl_write_log( "Going to delete relation instances [ subject_id :: $subject_id ]");

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
 * Validate filters given as parameters to any *wl_core_get_related_...*
 *
 * @param array $filters Associative array containing required predicate and post status
 *
 * @return array Corrected $filters, default is:
 *  array(
 *      'predicate' => null,
 *      'status'    => null
 *  );
 */
function wl_core_validate_filters_for_related( $filters ) {

	if ( ! is_array( $filters ) ) {
		$filters = array();
	}

	if ( ! isset( $filters['predicate'] ) ) {
		$filters['predicate'] = null;
	}
	if ( ! isset( $filters['status'] ) ) {
		$filters['status'] = null;
	}

	return $filters;
}

/**
 * Find all entities related to a given $subject_id
 * If $predicate is omitted, $predicate filter is not applied
 * @uses   wl_core_inner_get_related_entities() to perform the action
 *
 * @param int $subject_id The post ID | The entity post ID.
 * @param     $filters    Associative array formed like this:
 *                        <code>
 *                        $filters = array(
 *                        'predicate' => Name of the relation: [ null | 'what' | 'where' | 'when' | 'who' ], default is null (meaning *any* post status)
 *                        'status' => [ null | 'publish' | 'draft' | 'pending' | 'trash' ], default is null (meaning *any* post status)
 *                        );
 *                        </code>
 *
 * @return array Array of post entity objects.
 */
function wl_core_get_related_entities( $subject_id, $filters = array() ) {

	$filters = wl_core_validate_filters_for_related( $filters );

	return wl_core_inner_get_related_entities( "posts", $subject_id, $filters['predicate'], $filters['status'] );
}

/**
 * Find all entity ids related to a given $subject_id
 * If $predicate is omitted, $predicate filter is not applied
 * @uses   wl_core_inner_get_related_entities() to perform the action
 *
 * @param int    $subject_id The post ID | The entity post ID.
 * @param arøray $filters    Associative array formed like this:
 *                           <code>
 *                           $filters = array(
 *                           'predicate' => Name of the relation: [ null | 'what' | 'where' | 'when' | 'who' ], default is null (meaning *any* post status)
 *                           'status' => [ null | 'publish' | 'draft' | 'pending' | 'trash' ], default is null (meaning *any* post status)
 *                           );
 *                           </code>
 *
 * @return array Array of post entity objects.
 */
function wl_core_get_related_entity_ids( $subject_id, $filters = array() ) {

	$filters = wl_core_validate_filters_for_related( $filters );

	return wl_core_inner_get_related_entities( "post_ids", $subject_id, $filters['predicate'], $filters['status'] );
}

/**
 * Find all entity ids related to a given $subject_id
 * If $predicate is omitted, $predicate filter is not applied
 * Do not use it directly. Use wl_core_get_related_entities or wl_core_get_related_entity_ids instead.
 *
 * @param        $get
 * @param        $item_id
 * @param string $predicate   Name of the relation: null | 'what' | 'where' | 'when' | 'who'
 * @param string $post_status Filter by post status null | 'publish' | 'draft' | 'pending' | 'trash'. null means *any* post status
 *
 * @return array Array of ids.
 */
function wl_core_inner_get_related_entities( $get, $item_id, $predicate = null, $post_status = null ) {

	if ( $results = wl_core_get_posts( array(
		'get'            => $get,
		'post_type'      => 'entity',
		'post_status'    => $post_status,
		'related_to'     => $item_id,
		'as'             => 'object',
		'with_predicate' => $predicate,
	) )
	) {
		return $results;
	}

	// If wl_core_get_posts return false then an empty array is returned
	return array();
}

/**
 * Find all posts related to a given $object_id
 * If $predicate is omitted, $predicate filter is not applied
 * @uses   wl_core_get_related_posts() to perform the action
 *
 * @param int $object_id The entity ID or the post ID.
 * @param     $filters   Associative array formed like this:
 *                       <code>
 *                       $filters = array(
 *                       'predicate' => Name of the relation: [ null | 'what' | 'where' | 'when' | 'who' ], default is null (meaning *any* post status)
 *                       'status' => [ null | 'publish' | 'draft' | 'pending' | 'trash' ], default is null (meaning *any* post status)
 *                       );
 *                       </code>
 *
 * @return array Array of objects.
 */
function wl_core_get_related_posts( $object_id, $filters = array() ) {

	$filters = wl_core_validate_filters_for_related( $filters );

	return wl_core_inner_get_related_posts( "posts", $object_id, $filters['predicate'], $filters['status'] );
}

/**
 * Find all post ids related to a given $object_id
 * If $predicate is omitted, $predicate filter is not applied
 * @uses   wl_core_get_related_posts() to perform the action
 *
 * @param int $object_id The entity ID or the post ID.
 * @param     $filters   Associative array formed like this:
 *                       <code>
 *                       $filters = array(
 *                       'predicate' => Name of the relation: [ null | 'what' | 'where' | 'when' | 'who' ], default is null (meaning *any* post status)
 *                       'status' => [ null | 'publish' | 'draft' | 'pending' | 'trash' ], default is null (meaning *any* post status)
 *                       );
 *                       </code>
 *
 * @return array Array of objects.
 */
function wl_core_get_related_post_ids( $object_id, $filters = array() ) {

	$filters = wl_core_validate_filters_for_related( $filters );

	return wl_core_inner_get_related_posts( "post_ids", $object_id, $filters['predicate'], $filters['status'] );
}

/**
 * Find all posts related to a given $object_id
 * If $predicate is omitted, $predicate filter is not applied
 * Not use it directly. Use wl_core_get_related_posts or wl_core_get_related_posts_ids instead.
 *
 * @param int    $object_id   The entity ID or the post ID.
 * @param string $predicate   Name of the relation: null | 'what' | 'where' | 'when' | 'who'
 * @param string $post_status Filter by post status null | 'publish' | 'draft' | 'pending' | 'trash'. null means *any* post status
 *
 * @return array Array of objects.
 */
function wl_core_inner_get_related_posts( $get, $item_id, $predicate = null, $post_status = null ) {

	// Retrieve the post object
	$post = get_post( $item_id );
	if ( null === $post ) {
		return array();
	}

	if ( "entity" === $post->post_type ) {
		if ( $results = wl_core_get_posts( array(
			'get'            => $get,
			'post_type'      => 'post',
			'post_status'    => $post_status,
			'related_to'     => $item_id,
			'as'             => 'subject',
			'with_predicate' => $predicate,
		) )
		) {
			return $results;
		}
	} else {
		if ( $results = wl_core_get_posts( array(
			'get'            => $get,
			'post_type'      => 'post',
			'post_status'    => $post_status,
			'post__not_in'   => array( $item_id ),
			'related_to__in' => wl_core_get_related_entity_ids( $post->ID ),
			'as'             => 'subject',
			'with_predicate' => $predicate,
		) )
		) {
			return $results;
		}
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
 *   'related_to__in' => array(10,20,30)
 *   'post__in'      => array(10,20,30),          // the post/s / entity/ies id / ids
 *   'post__not_in'      => array(10,20,30),          // the post/s / entity/ies id / ids
 *   'as'   => [ subject | object ],
 *   'with_predicate'   => [ what | where | when | who ], // null as default value
 *   'post_type' => [ post | entity ],
 *   'post_status' => [ publish | draft | pending | trash ], default is null (meaning *any* post status)
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

	// Sql Action
	$sql = "SELECT ";
	// Determine what has to be returned depending on 'get' argument value
	switch ( $args['get'] ) {
		case 'posts':
			$sql .= "p.*";
			break;
		case 'post_ids':
			$sql .= "p.id";
			break;
	}

	// If we look for posts related as objects the JOIN has to be done with the object_id column and viceversa
	$join_column = $args['as'] . "_id";

	$sql .= " FROM $wpdb->posts as p JOIN $table_name as r ON p.id = r.$join_column";

	// Sql add post type filter
	$sql .= $wpdb->prepare( " AND p.post_type = %s AND", $args['post_type'] );

	// Sql add post status filter
	if ( isset( $args['post_status'] ) && ! is_null( $args['post_status'] ) ) {
		$sql .= $wpdb->prepare( " p.post_status = %s AND", $args['post_status'] );
	}

	// Add filtering conditions
	// If we look for posts related as objects this means that
	// related_to is a reference for a subject: subject_id is the filtering column
	// If we look for posts related as subject this means that
	// related_to is reference for an object: object_id is the filtering column

	$filtering_column = ( 'object' == $args['as'] ) ? "subject_id" : "object_id";

	if ( isset( $args['related_to'] ) ) {
		$sql .= $wpdb->prepare( " r.$filtering_column = %d", $args['related_to'] );
	}
	if ( isset( $args['related_to'] ) && isset( $args['related_to__in'] ) ) {
		$sql .= " AND";
	}
	if ( isset( $args['related_to__in'] ) ) {
		$sql .= " r.$filtering_column IN (" . implode( ",", $args['related_to__in'] ) . ")";
	}
	if ( isset( $args['post__not_in'] ) ) {
		$sql .= " AND r." . $args['as'] . "_id NOT IN (" . implode( ",", $args['post__not_in'] ) . ")";
	}
	if ( isset( $args['post__in'] ) ) {
		$sql .= " AND r." . $args['as'] . "_id IN (" . implode( ",", $args['post__in'] ) . ")";
	}
	// Add predicate filter if required
	if ( isset( $args['with_predicate'] ) ) {
		// Sql Inner Join clausole
		$sql .= $wpdb->prepare( " AND r.predicate = %s", $args['with_predicate'] );
	}
	// Add a group by clause to avoid duplicated rows
	// @todo: isn't a distinct a better choice?
	$sql .= " GROUP BY p.id";

	// @todo: how does `first` represent the limit?
	if ( isset( $args['first'] ) && is_numeric( $args['first'] ) ) {
		// Sql Inner Join clause.
		$sql .= $wpdb->prepare( " LIMIT %d", $args['first'] );
	}
	// Close sql statement
	$sql .= ";";

	return $sql;

}

/**
 * Perform a query on db depending on args
 * It's responsible for argument validations
 * @uses   wl_core_sql_query_builder() to compose the sql statement
 * @uses   wpdb() instance to perform the query
 *
 * @param array $args Arguments to be used in the query builder.
 *
 * @return array|false List of WP_Post objects or list of WP_Post ids. False in case of error or invalid params
 */
function wl_core_get_posts( $args, $returned_type = OBJECT ) {

	// Merge given args with defaults args value
	$args = array_merge( array(
		'with_predicate' => null,
		'as'             => 'subject',
		'post_type'      => 'post',
		'get'            => 'posts',
		'post_status'    => null,
	), $args );

	// Arguments validation rules
	// At least one between related_to and related_to__in has to be set
	if ( ! isset( $args['related_to'] ) && ! isset( $args['related_to__in'] ) ) {
		return false;
	}
	if ( isset( $args['related_to'] ) && ! is_numeric( $args['related_to'] ) ) {
		return false;
	}

	// The same check is applied to post_in, post__not_in and related_to__in options
	// Only arrays with at least one numeric value are considerad valid
	// The argument value is further sanitized in order to clean up not numeric values
	foreach (
		array(
			'post__in',
			'post__not_in',
			'related_to__in',
		) as $option_name
	) {
		if ( isset( $args[ $option_name ] ) ) {
			if ( ! is_array( $args[ $option_name ] ) || 0 == count( array_filter( $args[ $option_name ], "is_numeric" ) ) ) {
				return false;
			}
			// Sanitize value removing non numeric values from the array
			$args[ $option_name ] = array_filter( $args[ $option_name ], "is_numeric" );
		}
	}
	// Performing validation rules
	foreach ( wl_core_get_validation_rules() as $option_name => $accepeted_values ) {
		if ( isset( $args[ $option_name ] ) && ! is_null( $args[ $option_name ] ) ) {
			if ( ! in_array( $args[ $option_name ], $accepeted_values ) ) {
				return false;
			}
		}
	}

	// Prepare interaction with db
	global $wpdb;

	// Build sql statement with given arguments
	$sql_statement = wl_core_sql_query_builder( $args );

	// wl_write_log( "Going to execute sql statement: $sql_statement " );
	// $results = array();

	// If ids are required, returns a one-dimensional array containing ids.
	// Otherwise an array of associative arrays representing the post | relation object
	if ( 'post_ids' == $args['get'] ) {
		# See https://codex.wordpress.org/Class_Reference/wpdb#SELECT_a_Column
		$results = $wpdb->get_col( $sql_statement );
	} else {
		$results = $wpdb->get_results( $sql_statement, $returned_type );
	}
	// If there were an error performing the query then false is returned
	if ( ! empty( $wpdb->last_error ) ) {
		return false;
	}

	// Finally
	return $results;
}
