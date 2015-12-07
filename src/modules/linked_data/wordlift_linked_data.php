<?php
/**
 * The Linked Data module provides synchronization of local WordPress data with the remote Linked Data store.
 */

require_once( 'wordlift_linked_data_images.php' );

/**
 * Receive events from post saves, and split them according to the post type.
 *
 * @since 3.0.0
 *
 * @param int $post_id The post id.
 */
function wl_linked_data_save_post( $post_id ) {

	// If it's not numeric exit from here.
	if ( ! is_numeric( $post_id ) || is_numeric( wp_is_post_revision( $post_id ) ) ) {
		return;
	}

	// unhook this function so it doesn't loop infinitely
	remove_action( 'save_post', 'wl_linked_data_save_post' );

	// raise the *wl_linked_data_save_post* event.
	do_action( 'wl_linked_data_save_post', $post_id );

	// re-hook this function
	add_action( 'save_post', 'wl_linked_data_save_post' );
}

add_action( 'save_post', 'wl_linked_data_save_post' );

/**
 * Save the post to the triple store. Also saves the entities locally and on the triple store.
 *
 * @since 3.0.0
 *
 * @param int $post_id The post id being saved.
 */
function wl_linked_data_save_post_and_related_entities( $post_id ) {

    // Ignore auto-saves
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// get the current post.
	$post = get_post( $post_id );

	remove_action( 'wl_linked_data_save_post', 'wl_linked_data_save_post_and_related_entities' );

	// wl_write_log( "[ post id :: $post_id ][ autosave :: false ][ post type :: $post->post_type ]" );
    
	// Store mapping between tmp new entities uris and real new entities uri
	$entities_uri_mapping = array();
	// Store classification box mapping
	$entities_predicates_mapping = null;
    	
    // Save the entities coming with POST data.
	if ( isset( $_POST['wl_entities'] ) &&  isset( $_POST['wl_boxes'] ) ) {
	
            wl_write_log( "[ post id :: $post_id ][ POST(wl_entities) :: " );
            wl_write_log( json_encode( $_POST['wl_entities'] ) );
            wl_write_log( "]" );
            wl_write_log( "[ post id :: $post_id ][ POST(wl_boxes) :: " );
            wl_write_log( json_encode( $_POST['wl_boxes'], true ) );
            wl_write_log( "]" );

            $entities_via_post = $_POST['wl_entities'];
            $boxes_via_post = $_POST['wl_boxes'];

            foreach ( $entities_via_post as $entity_uri => $entity ) {
            	// Local entities have a tmp uri with 'local-entity-'
            	// These uris need to be rewritten here and replaced in the content
                if ( preg_match( '/^local-entity-.+/', $entity_uri ) > 0 ) {
                	// Build the proper uri 
                	$uri = sprintf( '%s/%s/%s', wl_configuration_get_redlink_dataset_uri(), 'entity', wl_sanitize_uri_path( $entity['label'] ) );
                	// Populate the mapping
                	$entities_uri_mapping[ $entity_uri ] = $uri;
                	// Override the entity obj
                	$entities_via_post[ $entity_uri ][ 'uri' ] = $uri;
                }
            }

            // Populate the $entities_predicates_mapping
            // Local Redlink uris need to be used here
            foreach ( $boxes_via_post as $predicate => $entity_uris ) {
                foreach ( $entity_uris as $entity_uri ) {
                	// wl_write_log("Going to map predicates for uri $entity_uri ");
                	// Retrieve the entity label needed to build the uri
                	$label = $entities_via_post[ stripslashes( $entity_uri ) ][ 'label' ];
                    $uri = sprintf( '%s/%s/%s', wl_configuration_get_redlink_dataset_uri(), 'entity', wl_sanitize_uri_path( $label ) );
                    // wl_write_log("Going to map predicate $predicate to uri $uri ");
                    $entities_predicates_mapping[ $uri ][] = $predicate; 
                }	
            }

            // Save entities (properties included) and push them to Redlink
            wl_save_entities( array_values( $entities_via_post ), $post_id );

	}
    
	// Replace tmp uris in content post if needed
	$updated_post_content = $post->post_content;
    // Save each entity and store the post id.
	foreach ( $entities_uri_mapping as $tmp_uri => $uri ) {
		$updated_post_content = str_replace( $tmp_uri, $uri, $updated_post_content );
	}
	// Update the post content
  	wp_update_post( array(
  		'ID'           => $post->ID,
  		'post_content' => $updated_post_content, 
  	) );

	// Extract related/referenced entities from text.
 	$disambiguated_entities = wl_linked_data_content_get_embedded_entities( $updated_post_content );
        
    // Reset previously saved instances
	wl_core_delete_relation_instances( $post_id );    
        
    // Save relation instances
    foreach( array_unique( $disambiguated_entities ) as $referenced_entity_id ) {
        
        // wl_write_log(" Going to manage relation between Post $post_id and $referenced_entity_id");
        
        if( $entities_predicates_mapping ) { 

        	// wl_write_log(" Going to manage relation instances according to the following mapping");
         	
            // Retrieve the entity uri
            $referenced_entity_uri = wl_get_entity_uri( $referenced_entity_id );
            // Retrieve predicates for the current uri
            if ( isset( $entities_predicates_mapping[ $referenced_entity_uri ] ) ) {
           		foreach ( $entities_predicates_mapping[ $referenced_entity_uri ] as $predicate ) {
                	// wl_write_log(" Going to add relation with predicate $predicate");
                	wl_core_add_relation_instance( $post_id, $predicate, $referenced_entity_id );
            	}
            } else {
            	wl_write_log("Entity uri $referenced_entity_uri missing in the mapping");
                wl_write_log( $entities_predicates_mapping );
            }

        } else {
            // Just for unit tests
            wl_core_add_relation_instance( $post_id, 'what', $referenced_entity_id );
        }

        // TODO Check if is needed
        wl_linked_data_push_to_redlink( $referenced_entity_id );
    }
    
	// Push the post to Redlink.
	wl_linked_data_push_to_redlink( $post->ID );

	add_action( 'wl_linked_data_save_post', 'wl_linked_data_save_post_and_related_entities' );
}

add_action( 'wl_linked_data_save_post', 'wl_linked_data_save_post_and_related_entities' );

/**
 * Adds default schema type "Thing" as soon as an entity is created.
 */
function wordlift_save_post_add_default_schema_type( $entity_id ) {
    
    $entity = get_post( $entity_id );
    $entity_type = wl_schema_get_types( $entity_id );
    
    // Assign type 'Thing' if we are dealing with an entity without type
    if( $entity->post_type == Wordlift_Entity_Service::TYPE_NAME && is_null( $entity_type ) ) {
        
        wl_schema_set_types( $entity_id, 'Thing' );
    }
}
// Priority 1 (default is 10) because we want the default type to be set as soon as possible
// Attatched to save_post because *wl_linked_data_save_post* does not always fire
add_action( 'save_post', 'wordlift_save_post_add_default_schema_type', 1);

/**
 * Save the specified entities to the local storage.
 *
 * @param array $entities An array of entities.
 * @param int $related_post_id A related post ID.
 *
 * @return array An array of posts.
 */
function wl_save_entities( $entities, $related_post_id = null ) {

	// wl_write_log( "[ entities count :: " . count( $entities ) . " ][ related post id :: $related_post_id ]" );

	// Prepare the return array.
	$posts = array();

	// Save each entity and store the post id.
	foreach ( $entities as $entity ) {

		$uri   = $entity['uri'];
		$label = $entity['label'];

		// This is the main type URI.
		$main_type_uri = $entity['main_type'];

		// the preferred type.
		$type_uris = ( isset( $entity['type'] ) ?
                                $entity['type'] :
                                array() );

		$description = $entity['description'];
		$images      = ( isset( $entity['image'] ) ?
			( is_array( $entity['image'] )
				? $entity['image']
				: array( $entity['image'] ) )
			: array() );
		$same_as     = ( isset( $entity['sameas'] ) ?
			( is_array( $entity['sameas'] )
				? $entity['sameas']
				: array( $entity['sameas'] ) )
			: array() );
		
		$other_properties = ( isset( $entity['properties'] ) ?
			$entity['properties'] :
			array() );
                
                // Bring properties inside an associative array
                $entity_properties = array(
                    'uri'             => $uri,
                    'label'           => $label,
                    'main_type_uri'   => $main_type_uri,
                    'description'     => $description,
                    'type_uris'       => $type_uris,
                    'images'          => $images,
                    'related_post_id' => $related_post_id,
                    'same_as'         => $same_as,
					'properties'      => $other_properties
                );
                
		// Save the entity.
		$entity_post = wl_save_entity( $entity_properties );
                
		// Store the post in the return array if successful.
		if ( null !== $entity_post ) {
			array_push( $posts, $entity_post );
		}
	}

	return $posts;
}

/**
 * Save the specified data as an entity in WordPress. This method only create new entities. When an existing entity is
 * found (by its URI), then the original post is returned.
 *
 * @param array $entity_properties, associative array containing: 
 * string 'uri' The entity URI.
 * string 'label' The entity label.
 * string 'main_type_uri' The entity type URI.
 * string 'description' The entity description.
 * array 'type_uris' An array of entity type URIs.
 * array 'images' An array of image URLs.
 * int 'related_post_id' A related post ID.
 * array 'same_as' An array of sameAs URLs.
 *
 * @return null|WP_Post A post instance or null in case of failure.
 */
function wl_save_entity( $entity_properties ) {
    
        $uri              = $entity_properties['uri'];
        $label            = $entity_properties['label'];
        $type_uri         = $entity_properties['main_type_uri'];
        $description      = $entity_properties['description'];
        $entity_types     = $entity_properties['type_uris'];
        $images           = $entity_properties['images'];
        $related_post_id  = $entity_properties['related_post_id'];
        $same_as          = $entity_properties['same_as'];
		$other_properties = $entity_properties['properties'];

	// Avoid errors due to null.
	if ( is_null( $entity_types ) ) {
		$entity_types = array();
	}

	// wl_write_log( "[ uri :: $uri ][ label :: $label ][ type uri :: $type_uri ]" );
        
        // Prepare properties of the new entity.
	$params = array(
		'post_status'  => ( is_numeric( $related_post_id ) ? get_post_status( $related_post_id ) : 'draft' ),
		'post_type'    => Wordlift_Entity_Service::TYPE_NAME,
		'post_title'   => $label,
		'post_content' => $description,
		'post_excerpt' => ''
	);

	// Check whether an entity already exists with the provided URI.
	$post = wl_get_entity_post_by_uri( $uri );
	        
	if ( null !== $post ) {
            // We insert into the params the entity ID, so it will be updated and not inserted.
            $params[ 'ID' ] = $post->ID;
            // Preserve the current entity status
            if ( 'public' == $post->post_status ) {
            	$params[ 'post_status' ] = $post->post_status;
            }
			// Preserve the current entity content. Hotfix for issue #152
            $params[ 'post_content' ] = $post->post_content;       
	}

	// If Yoast is installed and active, we temporary remove the save_postdata hook which causes Yoast to "pass over"
	// the local SEO form values to the created entity (https://github.com/insideout10/wordlift-plugin/issues/156)
	// Same thing applies to SEO Ultimate (https://github.com/insideout10/wordlift-plugin/issues/148)
	// This does NOT affect saving an entity from the entity admin page since this function is called when an entity
	// is created when saving a post.
	global $wpseo_metabox, $seo_ultimate;
	if ( isset( $wpseo_metabox ) ) {
		remove_action( 'wp_insert_post', array( $wpseo_metabox, 'save_postdata' ) );
	}

	if (isset($seo_ultimate)) {
		remove_action( 'save_post', array( $seo_ultimate, 'save_postmeta_box' ) );
	}

	// create or update the post.
	$post_id = wp_insert_post( $params, true );

	// If Yoast is installed and active, we restore the Yoast save_postdata hook (https://github.com/insideout10/wordlift-plugin/issues/156)
	if ( isset( $wpseo_metabox ) ) {
		add_action( 'wp_insert_post', array( $wpseo_metabox, 'save_postdata' ) );
	}

	// If SEO Ultimate is installed, add back the hook we removed a few lines above.
	if (isset($seo_ultimate)) {
		add_action( 'save_post',  array( $seo_ultimate, 'save_postmeta_box' ), 10, 2 );
	}

	// TODO: handle errors.
	if ( is_wp_error( $post_id ) ) {
		wl_write_log( ': error occurred' );

		// inform an error occurred.
		return null;
	}

	wl_set_entity_main_type( $post_id, $type_uri );

	// Save the entity types.
	wl_set_entity_rdf_types( $post_id, $entity_types );

	// Get a dataset URI for the entity.
	$wl_uri = wl_build_entity_uri( $post_id );

	// Save the entity URI.
	wl_set_entity_uri( $post_id, $wl_uri );

	// Add the uri to the sameAs data if it's not a local URI.
	if ( $wl_uri !== $uri ) {
		array_push( $same_as, $uri );
	}

	$new_uri = wl_get_entity_uri( $post_id );
	
	// Save the sameAs data for the entity.
	wl_schema_set_value( $post_id, 'sameAs', $same_as );
	
	// Save the other properties (latitude, langitude, startDate, endDate, etc.)
	foreach ( $other_properties as $property_name => $property_value ) {
		wl_schema_set_value( $post_id, $property_name, $property_value);
	}

	// Call hooks.
	do_action( 'wl_save_entity', $post_id );

	wl_write_log( "[ post id :: $post_id ][ uri :: $uri ][ label :: $label ][ wl uri :: $wl_uri ][ types :: " . implode( ',', $entity_types ) . " ][ images count :: " . count( $images ) . " ][ same_as count :: " . count( $same_as ) . " ]" );
        
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
		$image = wl_save_image( $image_remote_url );

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
			'post_mime_type' => $content_type
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
        
	// The entity is pushed to Redlink on save by the function hooked to save_post.
	// save the entity in the triple store.
	wl_linked_data_push_to_redlink( $post_id );

	// finally return the entity post.
	return get_post( $post_id );
}

/**
 * Get an array of entities from the *itemid* attributes embedded in the provided content.
 *
 * @since 3.0.0
 *
 * @param string $content The content with itemid attributes.
 *
 * @return array An array of entity posts.
 */
function wl_linked_data_content_get_embedded_entities( $content ) {

	// Remove quote escapes.
	$content = str_replace( '\\"', '"', $content );

	// Match all itemid attributes.
	$pattern = '/<\w+[^>]*\sitemid="([^"]+)"[^>]*>/im';

	//	wl_write_log( "Getting entities embedded into content [ pattern :: $pattern ]" );

	// Remove the pattern while it is found (match nested annotations).
	$matches = array();

	// In case of errors, return an empty array.
	if ( false === preg_match_all( $pattern, $content, $matches ) ) {
		wl_write_log( "Found no entities embedded in content" );

		return array();
	}

//    wl_write_log("wl_update_related_entities [ content :: $content ][ data :: " . var_export($data, true). " ][ matches :: " . var_export($matches, true) . " ]");

	// Collect the entities.
	$entities = array();
	foreach ( $matches[1] as $uri ) {
		$uri_d = html_entity_decode( $uri );
		$entity = wl_get_entity_post_by_uri( $uri_d );
		
		if ( null !== $entity ) {
			array_push( $entities, $entity->ID );
		}
	}

	// $count = sizeof( $entities );
	// wl_write_log( "Found $count entities embedded in content" );

	return $entities;
}

/**
 * Push the post with the specified ID to Redlink.
 *
 * @since 3.0.0
 *
 * @param int $post_id The post ID.
 */
function wl_linked_data_push_to_redlink( $post_id ) {

	// Get the post.
	$post = get_post( $post_id );

	// wl_write_log( "wl_linked_data_push_to_redlink [ post id :: $post_id ][ post type :: $post->post_type ]" );

	// Call the method on behalf of the post type.
	switch ( $post->post_type ) {
		case 'entity':
			wl_push_entity_post_to_redlink( $post );
			break;
		default:
			wl_push_post_to_redlink( $post );
	}

	// Reindex the triple store if buffering is turned off.
	if ( false === WL_ENABLE_SPARQL_UPDATE_QUERIES_BUFFERING ) {
		wordlift_reindex_triple_store();
	}
}
