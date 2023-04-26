<?php
/**
 * The Linked Data module provides synchronization of local WordPress data with the remote Linked Data store.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/modules/linked_data
 */

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;
use Wordlift\No_Editor_Analysis\No_Editor_Analysis_Feature;
use Wordlift\Object_Type_Enum;
use Wordlift\Relation\Relation;
use Wordlift\Relation\Relation_Service;

/**
 * Receive events from post saves, and split them according to the post type.
 *
 * @param int $post_id The post id.
 *
 * @since 3.0.0
 */
function wl_linked_data_save_post( $post_id ) {

	$log = Wordlift_Log_Service::get_logger( 'wl_linked_data_save_post' );

	$log->trace( "Saving post $post_id to Linked Data..." );

	// If it's not numeric exit from here.
	if ( ! is_numeric( $post_id ) || is_numeric( wp_is_post_revision( $post_id ) ) ) {
		$log->debug( "Skipping $post_id, because id is not numeric or is a post revision." );

		return;
	}

	// Get the post type and check whether it supports the editor.
	//
	// @see https://github.com/insideout10/wordlift-plugin/issues/659.
	$post_type = get_post_type( $post_id );
	/**
	 * Use `wl_post_type_supports_editor` which calls a filter to allow 3rd parties to change the setting.
	 *
	 * @since 3.19.4
	 *
	 * @see https://github.com/insideout10/wordlift-plugin/issues/847.
	 */
	$is_editor_supported = wl_post_type_supports_editor( $post_type );

	$is_no_editor_analysis_enabled = Wordlift\No_Editor_Analysis\No_Editor_Analysis_Feature::can_no_editor_analysis_be_used( $post_id );
	// Bail out if it's not an entity.
	if ( ! $is_editor_supported
		 && ! $is_no_editor_analysis_enabled ) {
		$log->debug( "Skipping $post_id, because $post_type doesn't support the editor (content)." );

		return;
	}

	/**
	 * Only process valid post types
	 *
	 * @since 3.25.6
	 */
	$supported_types = Wordlift_Entity_Service::valid_entity_post_types();

	// Bail out if it's not a valid entity.
	if ( ! in_array( $post_type, $supported_types, true ) && ! $is_no_editor_analysis_enabled ) {
		$log->debug( "Skipping $post_id, because $post_type is not a valid entity." );

		return;
	}

	// Unhook this function so it doesn't loop infinitely.
	remove_action( 'save_post', 'wl_linked_data_save_post' );

	// raise the *wl_linked_data_save_post* event.
	do_action( 'wl_linked_data_save_post', $post_id );

	// Re-hook this function.
	add_action( 'save_post', 'wl_linked_data_save_post' );
}

add_action( 'save_post', 'wl_linked_data_save_post' );

/**
 * Save the post to the triple store. Also saves the entities locally and on the triple store.
 *
 * This function is called by the 'save_post' hook, so we expect the nonce to be valid.
 *
 * @param int $post_id The post id being saved.
 *
 * @since 3.0.0
 */
function wl_linked_data_save_post_and_related_entities( $post_id ) {

	$log = Wordlift_Log_Service::get_logger( 'wl_linked_data_save_post_and_related_entities' );

	$log->trace( "Saving $post_id to Linked Data along with related entities..." );

	// Ignore auto-saves
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		$log->trace( 'Doing autosave, skipping...' );

		return;
	}

	// get the current post.
	$post = get_post( $post_id );

	remove_action( 'wl_linked_data_save_post', 'wl_linked_data_save_post_and_related_entities' );

	// wl_write_log( "[ post id :: $post_id ][ autosave :: false ][ post type :: $post->post_type ]" );

	// Get the entity service instance.
	$entity_service  = Wordlift_Entity_Service::get_instance();
	$uri_service     = Wordlift_Entity_Uri_Service::get_instance();
	$content_service = Wordpress_Content_Service::get_instance();

	// Store mapping between tmp new entities uris and real new entities uri
	$entities_uri_mapping = array();

	// Save all the selected internal entity uris to this variable.
	$internal_entity_uris = array();

	// Save the entities coming with POST data.
	if ( isset( $_POST['wl_entities'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$data              = filter_var_array( $_POST, array( 'wl_entities' => array( 'flags' => FILTER_REQUIRE_ARRAY ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$entities_via_post = $data['wl_entities'];
		wl_write_log( "[ post id :: $post_id ][ POST(wl_entities) :: " );
		wl_write_log( wp_json_encode( $entities_via_post ) );
		wl_write_log( ']' );

		foreach ( $entities_via_post as $entity_uri => $entity ) {

			if ( preg_match( '/^local-entity-.+/', $entity_uri ) ) {
				$existing_entity = get_page_by_title( $entity['label'], OBJECT, Wordlift_Entity_Service::valid_entity_post_types() );
				if ( isset( $existing_entity ) ) {
					$existing_entity_type = Wordlift_Entity_Type_Service::get_instance()->get( $existing_entity->ID );
					// Type doesn't match, continue to create a new entity.
					if ( ! isset( $existing_entity_type ) || $existing_entity_type['css_class'] !== $entity['main_type'] ) {
						$existing_entity = null;
					}
				}
			} else {
				// Look if current entity uri matches an internal existing entity, meaning:
				// 1. when $entity_uri is an internal uri
				// 2. when $entity_uri is an external uri used as sameAs of an internal entity
				$existing_entity = $entity_service->get_entity_post_by_uri( $entity_uri );
			}

			// Don't save the entities which are not found, but also local.
			if ( ! isset( $existing_entity ) && $uri_service->is_internal( $entity_uri ) ) {
				$internal_entity_uris[] = $entity_uri;
				continue;
			}

			if ( ! isset( $existing_entity ) ) {
				// Update entity data with related post
				$entity['related_post_id'] = $post_id;
				// New entity, save it.
				$existing_entity = wl_save_entity( $entity );
			} else {
				// Existing entity, update post status.
				if ( $existing_entity instanceof WP_Post && 'publish' !== $existing_entity->post_status ) {
					// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
					$post_status = apply_filters( 'wl_feature__enable__entity-auto-publish', true )
						? $post->post_status : 'draft';
					wl_update_post_status( $existing_entity->ID, $post_status );
				}
			}

			$uri = $content_service->get_entity_id( Wordpress_Content_Id::create_post( $existing_entity->ID ) );

			$internal_entity_uris[] = $uri;
			wl_write_log( "Map $entity_uri on $uri" );
			$entities_uri_mapping[ $entity_uri ] = $uri;

		}
	}

	// Replace tmp uris in content post if needed
	$updated_post_content = $post->post_content;

	// Update the post content if we found mappings of new entities.
	if ( ! empty( $entities_uri_mapping ) ) {
		// Save each entity and store the post id.
		foreach ( $entities_uri_mapping as $tmp_uri => $uri ) {
			if ( 1 !== preg_match( '@^(https?://|local-entity-)@', $tmp_uri ) ) {
				continue;
			}

			$updated_post_content = str_replace( $tmp_uri, $uri, $updated_post_content );
		}

		// Update the post content.
		/**
		 * Note: wp_update_post do stripslashes before saving the
		 * content, so add the slashes to prevent back slash getting
		 * removed.
		 */
		wp_update_post(
			array(
				'ID'           => $post->ID,
				'post_content' => addslashes( $updated_post_content ),
			)
		);
	}

	// Reset previously saved instances.
	wl_core_delete_relation_instances( $post_id );

	$content_id = Wordpress_Content_Id::create_post( $post->ID );
	$relations  = Relation_Service::get_instance()->get_relations( $content_id );

	if ( No_Editor_Analysis_Feature::can_no_editor_analysis_be_used( $post_id ) ) {
		$relations->add( ...Relation_Service::get_relations_from_uris( $content_id, $internal_entity_uris ) );
	}
	/**
	 * Filter the relations, we dont want to create a relation
	 * to uncategorized term, we are already filtering this on jsonld,
	 *
	 * @todo: do i need to move this to post-term-relation-service ?
	 */
	$filtered_relations = array_filter(
		$relations->toArray(),
		function ( $item ) {
			/**
			 * @var $item Relation
			 */
			$object = $item->get_object();

			return ! ( $object->get_type() === Object_Type_Enum::TERM
			&& $object->get_id() === 1 );
		}
	);

	// Save relation instances
	/** @var Relation $relation */
	foreach ( $filtered_relations  as $relation ) {
		$subject = $relation->get_subject();
		$object  = $relation->get_object();

		wl_core_add_relation_instance(
		// subject id.
			$subject->get_id(),
			// what, where, when, who
			$relation->get_predicate(),
			// object id.
			$object->get_id(),
			// Subject type.
			$subject->get_type(),
			// Object type.
			$object->get_type()
		);

	}

	if ( isset( $_POST['wl_entities'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		// Save post metadata if available
		$metadata_via_post = ( isset( $_POST['wl_metadata'] ) ) ? filter_input_array( INPUT_POST, array( 'wl_metadata' => FILTER_DEFAULT ) ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Missing

		$fields = array(
			Wordlift_Schema_Service::FIELD_LOCATION_CREATED,
			Wordlift_Schema_Service::FIELD_TOPIC,
		);

		// Unlink topic taxonomy terms
		Wordlift_Topic_Taxonomy_Service::get_instance()->unlink_topic_for( $post->ID );

		foreach ( $fields as $field ) {

			// Delete current values
			delete_post_meta( $post->ID, $field );
			// Retrieve the entity uri
			$uri = ( isset( $metadata_via_post[ $field ] ) ) ?
				stripslashes( $metadata_via_post[ $field ] ) : '';

			if ( empty( $uri ) ) {
				continue;
			}
			$entity = $entity_service->get_entity_post_by_uri( $uri );

			if ( $entity ) {
				add_post_meta( $post->ID, $field, $entity->ID, true );
				// Set also the topic taxonomy
				if ( Wordlift_Schema_Service::FIELD_TOPIC === $field ) {
					Wordlift_Topic_Taxonomy_Service::get_instance()->set_topic_for( $post->ID, $entity );
				}
			}
		}
	}

	add_action( 'wl_linked_data_save_post', 'wl_linked_data_save_post_and_related_entities' );
}

add_action( 'wl_linked_data_save_post', 'wl_linked_data_save_post_and_related_entities' );

/**
 * Save the specified data as an entity in WordPress. This method only create new entities. When an existing entity is
 * found (by its URI), then the original post is returned.
 *
 * @param array $entity_data , associative array containing:
 *                           string 'uri'             The entity URI.
 *                           string 'label'           The entity label.
 *                           string 'main_type'       The entity type URI.
 *                           array  'type'            An array of entity type URIs.
 *                           string 'description'     The entity description.
 *                           array  'images'          An array of image URLs.
 *                           int    'related_post_id' A related post ID.
 *                           array  'same_as'         An array of sameAs URLs.
 *
 * @return null|WP_Post A post instance or null in case of failure.
 */
function wl_save_entity( $entity_data ) {

	// Required for REST API calls
	if ( ! function_exists( 'wp_crop_image' ) ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
	}

	$log = Wordlift_Log_Service::get_logger( 'wl_save_entity' );

	/*
	 * Data is coming from a $_POST, sanitize it.
	 *
	 * @since 3.19.4
	 *
	 * @see https://github.com/insideout10/wordlift-plugin/issues/841
	 */
	$label            = preg_replace( '/\xEF\xBB\xBF/', '', sanitize_text_field( $entity_data['label'] ) );
	$type_uri         = $entity_data['main_type'];
	$entity_types     = isset( $entity_data['type'] ) ? $entity_data['type'] : array();
	$description      = $entity_data['description'];
	$images           = isset( $entity_data['image'] ) ? (array) $entity_data['image'] : array();
	$same_as          = isset( $entity_data['sameas'] ) ? (array) $entity_data['sameas'] : array();
	$related_post_id  = isset( $entity_data['related_post_id'] ) ? $entity_data['related_post_id'] : null;
	$other_properties = isset( $entity_data['properties'] ) ? $entity_data['properties'] : array();
	// Get the synonyms.
	$synonyms = isset( $entity_data['synonym'] ) ? $entity_data['synonym'] : array();

	// Check whether an entity already exists with the provided URI.
	$uri = $entity_data['uri'];
	if ( isset( $uri ) ) {
		$post = Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( $uri );
		if ( isset( $post ) ) {
			$log->debug( "Post already exists for URI $uri." );

			return $post;
		}
	}

	// Prepare properties of the new entity.
	// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
	$post_status = apply_filters( 'wl_feature__enable__entity-auto-publish', true ) && is_numeric( $related_post_id )
		? get_post_status( $related_post_id ) : 'draft';

	$params = array(
		// @@todo: we don't want an entity to be automatically published.
		'post_status'  => $post_status,
		'post_type'    => Wordlift_Entity_Service::TYPE_NAME,
		'post_title'   => $label,
		'post_content' => $description,
		'post_excerpt' => '',
		// Ensure we're using a valid slug. We're not overwriting an existing
		// entity with a post_name already set, since this call is made only for
		// new entities.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/282
		'post_name'    => sanitize_title( $label ),
	);

	// If Yoast is installed and active, we temporary remove the save_postdata hook which causes Yoast to "pass over"
	// the local SEO form values to the created entity (https://github.com/insideout10/wordlift-plugin/issues/156)
	// Same thing applies to SEO Ultimate (https://github.com/insideout10/wordlift-plugin/issues/148)
	// This does NOT affect saving an entity from the entity admin page since this function is called when an entity
	// is created when saving a post.
	global $wpseo_metabox, $seo_ultimate;
	if ( isset( $wpseo_metabox ) ) {
		remove_action(
			'wp_insert_post',
			array(
				$wpseo_metabox,
				'save_postdata',
			)
		);
	}

	if ( isset( $seo_ultimate ) ) {
		remove_action(
			'save_post',
			array(
				$seo_ultimate,
				'save_postmeta_box',
			)
		);
	}

	// The fact that we're calling here wp_insert_post is causing issues with plugins (and ourselves too) that hook to
	// save_post in order to save additional inputs from the edit page. In order to avoid issues, we pop all the hooks
	// to the save_post and restore them after we saved the entity.
	// see https://github.com/insideout10/wordlift-plugin/issues/203
	// see https://github.com/insideout10/wordlift-plugin/issues/156
	// see https://github.com/insideout10/wordlift-plugin/issues/148
	global $wp_filter;
	$save_post_filters = is_array( $wp_filter['save_post'] ) ? $wp_filter['save_post'] : $wp_filter['save_post']->callbacks;
	is_array( $wp_filter['save_post'] ) ? $wp_filter['save_post'] = array() : $wp_filter['save_post']->remove_all_filters(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

	$log->trace( 'Going to insert new post...' );

	// create or update the post.
	$post_id = wp_insert_post( $params, true );

	// Setting the alternative labels for this entity.
	Wordlift_Entity_Service::get_instance()
						   ->set_alternative_labels( $post_id, $synonyms );

	// Restore all the existing filters.
	is_array( $wp_filter['save_post'] ) ? $wp_filter['save_post'] = $save_post_filters : $wp_filter['save_post']->callbacks = $save_post_filters; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

	// If Yoast is installed and active, we restore the Yoast save_postdata hook (https://github.com/insideout10/wordlift-plugin/issues/156)
	if ( isset( $wpseo_metabox ) ) {
		add_action(
			'wp_insert_post',
			array(
				$wpseo_metabox,
				'save_postdata',
			)
		);
	}

	// If SEO Ultimate is installed, add back the hook we removed a few lines above.
	if ( isset( $seo_ultimate ) ) {
		add_action(
			'save_post',
			array(
				$seo_ultimate,
				'save_postmeta_box',
			),
			10,
			2
		);
	}

	// TODO: handle errors.
	if ( is_wp_error( $post_id ) ) {
		$log->error( 'An error occurred: ' . $post_id->get_error_message() );

		// inform an error occurred.
		return null;
	}

	wl_set_entity_main_type( $post_id, $type_uri );

	// Save the entity types.
	wl_set_entity_rdf_types( $post_id, $entity_types );

	// Get a dataset URI for the entity.
	$wl_uri = Wordlift_Entity_Service::get_instance()->get_uri( $post_id );

	// Add the uri to the sameAs data if it's not a local URI.
	if ( isset( $uri ) && preg_match( '@https?://.*@', $uri ) &&
		 $wl_uri !== $uri &&
		 // Only remote entities
		 0 !== strpos( $uri, Wordlift_Configuration_Service::get_instance()->get_dataset_uri() )
	) {
		array_push( $same_as, $uri );
	}

	// Save the sameAs data for the entity.
	wl_schema_set_value( $post_id, 'sameAs', $same_as );

	// Save the other properties (latitude, langitude, startDate, endDate, etc.)
	foreach ( $other_properties as $property_name => $property_value ) {
		wl_schema_set_value( $post_id, $property_name, $property_value );
	}

	// Call hooks.
	do_action( 'wl_save_entity', $post_id );

	foreach ( $images as $image_remote_url ) {

		// Check if image is already present in local DB
		if ( strpos( $image_remote_url, site_url() ) !== false ) {
			// Do nothing.
			continue;
		}

		// Check if there is an existing attachment for this post ID and source URL.
		$existing_image = wl_get_attachment_for_source_url( $post_id, $image_remote_url );

		// Skip if an existing image is found.
		if ( null !== $existing_image ) {
			continue;
		}

		// Save the image and get the local path.
		$image = Wordlift_Remote_Image_Service::save_from_url( $image_remote_url );

		if ( false === $image || is_wp_error( $image ) ) {
			continue;
		}

		// Get the local URL.
		$filename     = $image['path'];
		$url          = $image['url'];
		$content_type = $image['content_type'];

		$attachment = array(
			'guid'           => $url,
			// post_title, post_content (the value for this key should be the empty string), post_status and post_mime_type
			'post_title'     => $label,
			// Set the title to the post title.
			'post_content'   => '',
			'post_status'    => 'inherit',
			'post_mime_type' => $content_type,
		);

		// Create the attachment in WordPress and generate the related metadata.
		$attachment_id = wp_insert_attachment( $attachment, $filename, $post_id );

		// Set the source URL for the image.
		wl_set_source_url( $attachment_id, $image_remote_url );

		$attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
		wp_update_attachment_metadata( $attachment_id, $attachment_data );

		// Set it as the featured image.
		set_post_thumbnail( $post_id, $attachment_id );
	}

	// finally return the entity post.
	return get_post( $post_id );
}
