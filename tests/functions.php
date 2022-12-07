<?php
/**
 * This file contains support functions for the tests.
 */

// Define the JSON-LD contexts.

define( 'WL_ENHANCER_NAMESPACE', 'a' );
define( 'WL_DUBLIN_CORE_NAMESPACE', 'b' );
define(
	'WL_JSON_LD_CONTEXT',
	serialize(
		array(
			WL_ENHANCER_NAMESPACE    => 'http://fise.iks-project.eu/ontology/',
			WL_DUBLIN_CORE_NAMESPACE => 'http://purl.org/dc/terms/',
		)
	)
);

require_once 'jsonld.php';

/**
 * Compatibility for WP 4.4.
 *
 * @since 3.19.4
 */
function _wl_test_set_wp_die_handler() {
	global $wp_filter;
	if ( ! class_exists( 'WPDieException' ) ) {
		class WPDieException extends Exception {
		}
	}

	if ( ! function_exists( '_wl_test_wp_die_handler' ) ) {
		function _wl_test_wp_die_handler( $message ) {
			if ( ! is_scalar( $message ) ) {
				$message = '0';
			}

			throw new WPDieException( $message );
		}
	}

	unset( $wp_filter['wp_die_ajax_handler'] );
	add_filter(
		'wp_die_ajax_handler',
		function () {
			return '_wl_test_wp_die_handler';
		}
	);

	unset( $wp_filter['wp_die_handler'] );
	add_filter(
		'wp_die_handler',
		function () {
			return '_wl_test_wp_die_handler';
		}
	);

}

/**
 * Create a new post.
 *
 * @param string $content The post content.
 * @param string $slug The post slug.
 * @param string $title The post title.
 * @param string $status The post status (e.g. draft, publish, pending, private, ...)
 * @param string $type The post status (e.g. post, page, link, ...)
 *
 * @return int|WP_Error The post ID or a WP_Error instance.
 */
function wl_create_post( $content, $slug, $title, $status = 'draft', $type = 'post' ) {

	$args = array(
		'post_content' => $content,
		'post_name'    => $slug,
		'post_status'  => $status,
		'post_type'    => $type,
		'post_author'  => wl_test_create_user(),
	);

	if ( ! empty( $title ) ) {
		$args['post_title'] = $title;
	}

	$wp_error = null;
	$post_id  = wp_insert_post( $args, $wp_error );

	if ( is_wp_error( $wp_error ) ) {
		return $wp_error;
	}

	return $post_id;
}

/**
 * Delete the attachments related to the specified post.
 *
 * @param $post_id
 */
function wl_delete_post_attachments( $post_id ) {

	// Get all the attachments related to the post.
	$attachments = wl_get_attachments( $post_id );

	// Delete each attachment.
	foreach ( $attachments as $attachment ) {
		if ( false === wp_delete_attachment( $attachment->ID ) ) {
			wl_write_log( "wl_delete_post_attachments : error [ post id :: $post_id ]" );
		}
	}
}

/**
 * Get a types array from an item.
 *
 * @param object|array|string $item An item with a '@type' property (if the property doesn't exist, an empty array is returned).
 *
 * @return array The items array (or an empty array if the '@type' property doesn't exist).
 */
function wl_type_to_types( $item ) {

	if ( is_string( $item ) ) {
		return array( $item );
	}

	if ( is_array( $item ) ) {
		return $item;
	}

	return ! isset( $item->{'@type'} )
		? array() // Set an empty array if type is not set on the item.
		: ( is_array( $item->{'@type'} ) ? $item->{'@type'} : array( $item->{'@type'} ) );
}

/**
 * Parse the string representation of the JSON-LD response from the analysis service.
 *
 * @param string $json A string representation in JSON-LD format.
 *
 * @return array|null Null in case of failure, otherwise an array with Text Annotations, Entity Annotations and
 * Entities.
 */
function wl_parse_response( $json ) {

	// Check that the provided param is an object.
	if ( ! is_object( $json ) ) {
		return null;
	}

	// Define the context for compacting.
	$context = (object) unserialize( WL_JSON_LD_CONTEXT );

	// Compact the JSON-LD.
	$jsonld = jsonld_compact( $json, $context );

	// Get the entity annotations indexed by the textannotation reference.
	$entity_annotations = array();
	// Text Annotations are index by their ID.
	$text_annotations = array();
	// Entities are indexed by their ID.
	$entities = array();
	foreach ( $jsonld->{'@graph'} as $item ) {
		$types = wl_type_to_types( $item );
		// Entity Annotation.
		if ( in_array( WL_ENHANCER_NAMESPACE . ':EntityAnnotation', $types ) ) {
			array_push( $entity_annotations, $item );
		} // Text Annotation.
		elseif ( in_array( WL_ENHANCER_NAMESPACE . ':TextAnnotation', $types ) ) {

			// Skip Text Annotations that do not have the selection-prefix, -suffix and selected-text.
			if ( isset( $item->{WL_ENHANCER_NAMESPACE . ':selection-prefix'}->{'@value'} )
				 && isset( $item->{WL_ENHANCER_NAMESPACE . ':selection-suffix'}->{'@value'} )
				 && isset( $item->{WL_ENHANCER_NAMESPACE . ':selected-text'}->{'@value'} )
			) {

				$text_annotations[ $item->{'@id'} ] = array(
					'_'          => $item,
					'id'         => $item->{'@id'},
					'sel_prefix' => $item->{WL_ENHANCER_NAMESPACE . ':selection-prefix'}->{'@value'},
					'sel_suffix' => $item->{WL_ENHANCER_NAMESPACE . ':selection-suffix'}->{'@value'},
					'sel_text'   => $item->{WL_ENHANCER_NAMESPACE . ':selected-text'}->{'@value'},
					'entities'   => array(),
					// will hold the entities referenced by this text-annotation.
				);
			}
		} // Entity
		else {
			$entities[ $item->{'@id'} ] = $item;
		}
	}

	// echo '[ $entity_annotations :: ' . count( $entity_annotations ) . ' ]';
	// echo '[ $text_annotations :: ' . count( $text_annotations ) . ' ]';
	// echo '[ $entities :: ' . count( $entities ) . ' ]';

	// Bind the entities to each text annotation via the entity annotation.
	foreach ( $entity_annotations as $item ) {
		// The relation to a Text Annotation.
		$relation = (string) $item->{WL_DUBLIN_CORE_NAMESPACE . ':relation'}->{'@id'};
		// The reference to an entity.
		$entity_reference = (string) $item->{WL_ENHANCER_NAMESPACE . ':entity-reference'}->{'@id'};
		// Get the confidence for the match.
		$confidence = $item->{WL_ENHANCER_NAMESPACE . ':confidence'}->{'@value'};

		// echo "[ relation :: $relation ][ reference :: $entity_reference ]\n";

		// Get the Text Annotation (by ref).
		$text_annotation = &$text_annotations[ $relation ];
		// Get the Entity (by ref)
		$entity = &$entities[ $entity_reference ];

		// echo "[ entity null :: " . is_null( $entity ) . " ]\n";

		// Add the entity to the text annotation entities array.
		array_push(
			$text_annotation['entities'],
			array(
				'entity'     => $entity,
				'confidence' => $confidence,
			)
		);
	}

	return array(
		'text_annotations'   => $text_annotations,
		'entity_annotations' => $entity_annotations,
		'entities'           => $entities,
	);
}

/**
 * Get the attachments for the specified post ID.
 *
 * @param int $post_id The post ID.
 *
 * @return array An array of attachments.
 */
function wl_get_attachments( $post_id ) {

	return get_posts(
		array(
			'post_type'      => 'attachment',
			'posts_per_page' => - 1,
			'post_status'    => 'any',
			'post_parent'    => $post_id,
		)
	);
}

function _wl_mock_http_request( $response, $request, $url ) {

	if ( $response || preg_match( '@/wl-api$@', $url ) ) {
		return $response;
	}

	if ( $response || preg_match( '@^https://woocommerce.com/@', $url ) ) {
		return $response;
	}

	$method = $request['method'];

	if ( 'PUT' === $method && preg_match( '@/accounts\?key=key123&url=http%3A%2F%2Fexample.org&country=us&language=en$@', $url ) ) {

		$response = array(
			'body'     => '{ "datasetURI": "https://data.localdomain.localhost/dataset", "packageType": "unknown" }',
			'response' => array( 'code' => 200 ),
		);

		// If dataset-ng is enable for tests, populate the features response header.
		if ( wp_validate_boolean( getenv( 'WL_FEATURES__DATASET_NG' ) ) ) {
			$response['headers'] = array( Wordlift\Features\Response_Adapter::WL_1 => base64_encode( '{ "features": { "dataset-ng": true, "analysis-ng": true } }' ) );
		}

		return $response;
	}

	if ( preg_match( '@/accounts/me/include-excludes$@', $url ) ) {
		return array( 'response' => array( 'code' => 200 ) );
	}

	$request_data = is_string( $request['body'] ) ? json_decode( $request['body'], true ) : null;

	if ( is_string( $request['body'] ) && 'POST' === $method && '430c6e5d6b51fa56c4e1a240ad4fdd8d' === md5( $request_data['content'] ) ) {
		return array(
			'body'     => file_get_contents( __DIR__ . '/assets/content-analysis-response-3.json' ),
			'headers'  => array( 'content-type' => 'application/json' ),
			'response' => array( 'code' => 200 ),
		);
	}

	/** ACF pass-through. */
	if ( 'GET' === $method && preg_match( '@^https://connect.advancedcustomfields.com/@', $url ) ) {
		return $response;
	}

	if ( preg_match( '@^http://example.org/wp-admin/admin-ajax.php\?action=wp_wl_dataset__sync@', $url ) ) {
		return $response;
	}

	if ( preg_match( '@^https://downloads\.wordpress\.org/@', $url ) ) {
		return $response;
	}

	remove_filter( 'pre_http_request', '_wl_mock_http_request', PHP_INT_MAX );

	echo "An unknown request to $url has been caught:\n";
	$md5 = md5( $request['body'] );
	echo( "Request Details (Body MD5 $md5): \n" . var_export( $request, true ) );
	echo( "Response Details: \n" . var_export( wp_remote_request( $url, $request ), true ) );

	echo "Request Stack Trace: \n";
	debug_print_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 30 );
	die( 1 );

	return $response;
}

add_filter( 'pre_http_request', '_wl_mock_http_request', PHP_INT_MAX, 3 );

// add_option( 'wl_advanced_settings', array(
// "redlink_dataset_uri" => "https://data.localdomain.localhost/dataset",
// "package_type"        => "unknown"
// ) );

/**
 * Configure WordPress with the test settings (may vary according to the local PHP and WordPress versions).
 */
function wl_configure_wordpress_test() {

	$configuration_service = Wordlift_Configuration_Service::get_instance();

	// Simulate WordLift activation.
	activate_wordlift();

	// When setting the WordLift Key, the Redlink dataset URI is provisioned by WordLift Server.
	$configuration_service->set_key( '' );
	$configuration_service->set_key( getenv( 'WORDLIFT_KEY' ) );
	$dataset_uri = $configuration_service->get_dataset_uri();

	/*
	 * We want to run tests even if we're unable to set a dataset URI.
	 *
	 * @since 3.24.2
	 */
	// if ( empty( $dataset_uri ) ) {
	// echo( 'The dataset URI is not set (maybe the WordLift key is not valid?).' );
	// die( 2 );
	// }
}

/**
 * Create a test user.
 *
 * @return int|WP_Error
 */
function wl_test_create_user() {

	return wp_insert_user(
		array(
			'user_login' => uniqid( 'user-' ),
			'user_pass'  => 'tmppass',
			'first_name' => 'Mario',
			'last_name'  => 'Rossi',
		)
	);
}

/**
 * Get relations for a given $subject_id as an associative array.
 *
 * @param int    $post_id
 * @param string $predicate
 *
 * @return array in the following format:
 *              Array (
 *                  [0] => stdClass Object ( [id] => 140 [subject_id] => 17 [predicate] => what [object_id] => 47 ),
 *                  [1] => stdClass Object ( [id] => 141 [subject_id] => 17 [predicate] => what [object_id] => 14 ),
 *                  [2] => stdClass Object ( [id] => 142 [subject_id] => 17 [predicate] => where [object_id] => 16 ),
 *                  ...
 * @global WP_Query $wpdb
 */
function wl_tests_get_relation_instances_for( $post_id, $predicate = null ) {

	// Prepare interaction with db
	global $wpdb;
	// Retrieve Wordlift relation instances table name
	$table_name = wl_core_get_relation_instances_table_name();
	// Sql Action
	$sql_statement = $wpdb->prepare( "SELECT * FROM $table_name WHERE subject_id = %d", $post_id );
	if ( null != $predicate ) {
		$sql_statement .= $wpdb->prepare( ' AND predicate = %s', $predicate );
	}
	$results = $wpdb->get_results( $sql_statement );

	return $results;

}

/**
 * Retrieve entity property type, starting from the schema.org's property name
 * or from the WL_CUSTOM_FIELD_xxx name.
 *
 * @param string $property_name as defined by schema.org or WL internal constants
 *
 * @return array containing type(s) or null (in case of error or no types).
 */
function wl_get_meta_type( $property_name ) {

	// Property name must be defined.
	if ( ! isset( $property_name ) || $property_name === null ) {
		return null;
	}

	// store eventual schema name in  different variable
	$property_schema_name = wl_build_full_schema_uri_from_schema_slug( $property_name );

	// Loop over custom_fields
	$entity_terms = wl_entity_taxonomy_get_custom_fields();

	foreach ( $entity_terms as $term ) {

		foreach ( $term as $wl_constant => $field ) {

			// Is this the predicate we are searching for?
			if ( isset( $field['type'] ) ) {
				$found_predicate = isset( $field['predicate'] ) && ( $field['predicate'] == $property_schema_name );
				$found_constant  = ( $wl_constant == $property_name );
				if ( $found_predicate || $found_constant ) {
					return $field['type'];
				}
			}
		}
	}

	return null;
}

/**
 * Remove a given relation instance
 *
 * @param int    $subject_id The post ID | The entity post ID.
 * @param string $predicate Name of the relation: 'what' | 'where' | 'when' | 'who'
 * @param int    $object_id The entity post ID.
 *
 * @return boolean False for failure. True for success.
 * @uses   $wpdb->delete() to perform the query
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
 *
 * @param int    $subject_id The post ID | The entity post ID.
 * @param string $predicate Name of the relation: 'what' | 'where' | 'when' | 'who'
 * @param array  $object_ids The entity post IDs collection.
 *
 * @return integer|boolean Return the relation instances IDs or false
 * @uses   wl_add_relation_instance() to create each single instance
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
