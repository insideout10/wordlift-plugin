<?php

/**
 * This file provides methods and functions to generate entities meta-boxes in the admin UI.
 */


/**
 * Build WL_Metabox and the contained WL_Metabox_Field(s)
 */
function wl_register_metaboxes() {

	// Load metabox classes
	require_once( 'WL_Metabox/WL_Metabox.php' );

	$wl_metabox = new WL_Metabox();     // Everything is done inside here with the correct timing
}

if ( is_admin() ) {
	add_action( 'load-post.php', 'wl_register_metaboxes' );
	add_action( 'load-post-new.php', 'wl_register_metaboxes' );
}


/**
 * Adds the entities meta box (called from *add_meta_boxes* hook).
 *
 * @param string $post_type The type of the current open post.
 */
function wl_admin_add_entities_meta_box( $post_type ) {

	// wl_write_log( "wl_admin_add_entities_meta_box [ post type :: $post_type ]" );

	// Add main meta box for related entities and 4W
	add_meta_box(
		'wordlift_entities_box', __( 'WordLift', 'wordlift' ), 'wl_entities_box_content', $post_type, 'side', 'high'
	);
}

add_action( 'add_meta_boxes', 'wl_admin_add_entities_meta_box' );

/**
 * Displays the meta box contents (called by *add_meta_box* callback).
 *
 * @param WP_Post $post The current post.
 */
function wl_entities_box_content( $post ) {

	// wl_write_log( "wl_entities_box_content [ post id :: $post->ID ]" );

	// Angularjs edit-post widget wrapper
	echo '<div id="wordlift-edit-post-outer-wrapper"></div>';

	// Angularjs edit-post widget classification boxes configuration
	$classification_boxes = unserialize( WL_CORE_POST_CLASSIFICATION_BOXES );

	// Array to store all related entities ids
	$all_referenced_entities_ids = array();

	// Add selected entities to classification_boxes
	foreach ( $classification_boxes as $i => $box ) {
		// Build the proper relation name
		$relation_name = $box['id'];

		// wl_write_log( "Going to related of $relation_name" );

		// Get entity ids related to the current post for the given relation name (both draft and published entities)
		$draft_entity_ids   = wl_core_get_related_entity_ids( $post->ID, array(
			'predicate' => $relation_name,
			'status'    => 'draft',
		) );
		$publish_entity_ids = wl_core_get_related_entity_ids( $post->ID, array(
			'predicate' => $relation_name,
			'status'    => 'publish',
		) );
		$entity_ids         = array_unique( array_merge( $draft_entity_ids, $publish_entity_ids ) );

		// Store the entity ids for all the 4W
		$all_referenced_entities_ids = array_merge( $all_referenced_entities_ids, $entity_ids );

		// Transform entity ids array in entity uris array
		array_walk( $entity_ids, function ( &$entity_id ) {
			// Retrieve the entity uri for the given entity id
			$entity_id = wl_get_entity_uri( $entity_id );
		} );

		// Enhance current box selected entities
		$classification_boxes[ $i ]['selectedEntities'] = $entity_ids;
	}
	// Json encoding for classification boxes structure
	$classification_boxes = json_encode( $classification_boxes );

	// Ensure there are no repetitions of the referenced entities
	$all_referenced_entities_ids = array_unique( $all_referenced_entities_ids );

	// Build the entity storage object
	$referenced_entities_obj = array();
	foreach ( $all_referenced_entities_ids as $referenced_entity ) {
		$entity                                   = wl_serialize_entity( $referenced_entity );
		$referenced_entities_obj[ $entity['id'] ] = $entity;
	}

	$referenced_entities_obj = empty( $referenced_entities_obj ) ?
		'{}' : json_encode( $referenced_entities_obj );

	$published_place_id  = get_post_meta(
		$post->ID, Wordlift_Schema_Service::FIELD_LOCATION_CREATED, true
	);
	$published_place_obj = ( $published_place_id ) ?
		json_encode( wl_serialize_entity( $published_place_id ) ) :
		'undefined';

	$topic_id  = get_post_meta(
		$post->ID, Wordlift_Schema_Service::FIELD_TOPIC, true
	);
	$topic_obj = ( $topic_id ) ?
		json_encode( wl_serialize_entity( $topic_id ) ) :
		'undefined';

	$default_thumbnail_path = WL_DEFAULT_THUMBNAIL_PATH;
	$default_path           = WL_DEFAULT_PATH;
	$dataset_uri            = wl_configuration_get_redlink_dataset_uri();
	$current_post_uri       = wl_get_entity_uri( $post->ID );

	// Retrieve the current post author
	$post_author = get_userdata( $post->post_author )->display_name;
	// Retrive the published date
	$published_date = get_the_time( 'Y-m-d', $post->ID );
	// Current language
	$current_language = wl_configuration_get_site_language();

	echo <<<EOF
    <script type="text/javascript">
        jQuery( function() {

        	if ('undefined' == typeof window.wordlift) {
            	window.wordlift = {}
            	window.wordlift.entities = {}  		
        	}

        	window.wordlift.classificationBoxes = $classification_boxes;
        	window.wordlift.entities = $referenced_entities_obj;
        	window.wordlift.currentPostId = $post->ID;
			window.wordlift.currentPostUri = '$current_post_uri';
            window.wordlift.currentPostType = '$post->post_type';
            window.wordlift.defaultThumbnailPath = '$default_thumbnail_path';
			window.wordlift.defaultWordLiftPath = '$default_path';
            window.wordlift.datasetUri = '$dataset_uri';
            window.wordlift.currentUser = '$post_author';
            window.wordlift.publishedDate = '$published_date';
            window.wordlift.publishedPlace = $published_place_obj;
            window.wordlift.topic = $topic_obj;
            window.wordlift.currentLanguage = '$current_language';

        });
    </script>
EOF;
}

