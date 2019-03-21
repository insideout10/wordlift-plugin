<?php
/**
 * This file provides methods and functions to generate entities meta-boxes in the admin UI.
 *
 * @package Wordlift
 */

/**
 * Build WL_Metabox and the contained WL_Metabox_Field(s)
 */
function wl_register_metaboxes() {

	// Load metabox classes.
	require_once( 'WL_Metabox/class-wl-metabox.php' );

	$wl_metabox = new WL_Metabox();     // Everything is done inside here with the correct timing.
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

	// Bail out if the post type doesn't support a TinyMCE editor.
	if ( ! wl_post_type_supports_editor( $post_type ) ) {
		return;
	}

	if (!Wordlift_Admin::is_gutenberg()) {
		// Add main meta box for related entities and 4W only if not Gutenberg
		add_meta_box(
			'wordlift_entities_box', __( 'WordLift', 'wordlift' ), 'wl_entities_box_content', $post_type, 'side', 'high'
		);
	} else {
		// Call wl_entities_box_content for the other things that it does.
		wl_entities_box_content(get_post(), false);
	}
}

add_action( 'add_meta_boxes', 'wl_admin_add_entities_meta_box' );

/**
 * Whether the post type supports the editor UI.
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/847
 *
 * @param string $post_type The post type.
 *
 * @return bool True if the editor UI is supported otherwise false.
 */
function wl_post_type_supports_editor( $post_type ) {

	$default = post_type_supports( $post_type, 'editor' );

	/**
	 * Allow 3rd parties to force the classification to load.
	 *
	 * @since 3.19.4
	 *
	 * @see https://github.com/insideout10/wordlift-plugin/issues/847.
	 *
	 * @param bool $default The preset value as gathered by the `post_type_supports` call.
	 */
	return apply_filters( 'wl_post_type_supports_editor', $default, $post_type );
}

/**
 * Displays the meta box contents (called by *add_meta_box* callback).
 *
 * @param WP_Post $post The current post.
 */
function wl_entities_box_content( $post, $wrapper = true ) {

	// Angularjs edit-post widget wrapper.
	if($wrapper) echo '<div id="wordlift-edit-post-outer-wrapper"></div>';

	// Angularjs edit-post widget classification boxes configuration.
	$classification_boxes = unserialize( WL_CORE_POST_CLASSIFICATION_BOXES );

	// Array to store all related entities ids.
	$all_referenced_entities_ids = array();

	// Add selected entities to classification_boxes.
	foreach ( $classification_boxes as $i => $box ) {
		// Build the proper relation name.
		$relation_name = $box['id'];

		// Get the entity referenced from the post content.
		/*
		 * Allow 3rd parties to provide another post content.
		 *
		 * @since 3.20.0
		 */
		$post_content = apply_filters( 'wl_post_content', $post->post_content, $post );
		$entity_uris  = Wordlift_Content_Filter_Service::get_instance()->get_entity_uris( $post_content );

		// Enhance current box selected entities.
		$classification_boxes[ $i ]['selectedEntities'] = $entity_uris;

		// Maps the URIs to entity posts.
		$entity_service = Wordlift_Entity_Service::get_instance();

		// Replace all entity URI's with post ID's if found or null if there is no related post.
		$entity_ids = array_map( function ( $item ) use ( $entity_service ) {
			// Return entity post by the entity URI or null.
			$post = $entity_service->get_entity_post_by_uri( $item );

			// Check that the post object is not null.
			if ( ! empty( $post ) ) {
				return $post->ID;
			}
		}, $entity_uris );
		// Store the entity ids for all the 4W.
		$all_referenced_entities_ids = array_merge( $all_referenced_entities_ids, $entity_ids );

	}
	// Json encoding for classification boxes structure.
	$classification_boxes = wp_json_encode( $classification_boxes );

	// Ensure there are no repetitions of the referenced entities.
	$all_referenced_entities_ids = array_unique( $all_referenced_entities_ids );

	// Remove all null, false and empty strings.
	// NULL is being returned in some cases, when there is not related post, so we need to remove it.
	$all_referenced_entities_ids = array_filter( $all_referenced_entities_ids );

	// Build the entity storage object.
	$referenced_entities_obj = array();
	foreach ( $all_referenced_entities_ids as $referenced_entity ) {
		$entity = wl_serialize_entity( $referenced_entity );
		// Set a default confidence of `PHP_INT_MAX` for already annotated entities.
		$referenced_entities_obj[ $entity['id'] ] = $entity
		                                            + array( 'confidence' => PHP_INT_MAX );
	}

	$referenced_entities_obj = empty( $referenced_entities_obj ) ?
		'{}' : wp_json_encode( $referenced_entities_obj );

	$published_place_id  = get_post_meta(
		$post->ID, Wordlift_Schema_Service::FIELD_LOCATION_CREATED, true
	);
	$published_place_obj = ( $published_place_id ) ?
		wp_json_encode( wl_serialize_entity( $published_place_id ) ) :
		'undefined';

	$topic_id  = get_post_meta(
		$post->ID, Wordlift_Schema_Service::FIELD_TOPIC, true
	);
	$topic_obj = ( $topic_id ) ?
		wp_json_encode( wl_serialize_entity( $topic_id ) ) :
		'undefined';

	$configuration_service = Wordlift_Configuration_Service::get_instance();

	$default_thumbnail_path = WL_DEFAULT_THUMBNAIL_PATH;
	$default_path           = WL_DEFAULT_PATH;
	$dataset_uri            = $configuration_service->get_dataset_uri();
	$current_post_uri       = wl_get_entity_uri( $post->ID );

	// Retrieve the current post author.
	$post_author = get_userdata( $post->post_author )->display_name;
	// Retrive the published date.
	$published_date = get_the_time( 'Y-m-d', $post->ID );
	// Current language.
	$current_language = $configuration_service->get_language_code();

	$js_code =  <<<JS
		if ('undefined' == typeof window.wordlift) {
			window.wordlift = {};
			window.wordlift.entities = {};  		
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
JS;

	if($wrapper) echo '<script type="text/javascript">'.PHP_EOL.$js_code.PHP_EOL.'</script>';
	wp_add_inline_script('wl-entity-metabox-utility', $js_code);
}

