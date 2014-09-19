<?php
/**
 * This file contains methods related to entities.
 */

/**
 * Build the entity URI given the entity's post.
 *
 * @uses wl_sanitize_uri_path to sanitize the post title.
 * @uses wl_config_get_dataset_base_uri to get the dataset base URI.
 *
 * @param int $post_id The post ID
 *
 * @return string The URI of the entity
 */
function wl_build_entity_uri( $post_id )
{

    // Get the post.
    $post = get_post( $post_id );

    if (null === $post) {

        wl_write_log( "wl_build_entity_uri : error [ post ID :: $post_id ][ post :: null ]" );
        return;
    }

    // Create an ID given the title.
    $path = wl_sanitize_uri_path( $post->post_title );

    // If the path is empty, i.e. there's no title, use the post ID as path.
    if ( empty( $path ) ) {
        $path = "id/$post->ID";
    }

    // Create the URL (dataset base URI has a trailing slash).
    $url = sprintf( '%s/%s/%s', wl_config_get_dataset_base_uri(), $post->post_type, $path );

    wl_write_log("wl_build_entity_uri [ post_id :: $post->ID ][ type :: $post->post_type ][ title :: $post->post_title ][ url :: $url ]");

    return $url;
}

/**
 * Get the entity URI of the provided post.
 *
 * @uses wl_build_entity_uri to create a new URI if the entity doesn't have an URI yet.
 * @uses wl_set_entity_uri to set a newly create URI.
 *
 * @param int $post_id The post ID.
 *
 * @return string|null The URI of the entity or null if not configured.
 */
function wl_get_entity_uri( $post_id )
{

    $uri = get_post_meta( $post_id, WL_ENTITY_URL_META_NAME, true );
    $uri = utf8_encode( $uri );

    // Set the URI if it isn't set yet.
    $post_status = get_post_status( $post_id );
    if ( empty( $uri ) && 'auto-draft' !== $post_status && 'revision' !== $post_status ) {
        $uri = wl_build_entity_uri( $post_id ); //  "http://data.redlink.io/$user_id/$dataset_id/post/$post->ID";
        wl_set_entity_uri( $post_id, $uri );
    }

    return $uri;
}

/**
 * Save the entity URI for the provided post ID.
 * @param int $post_id The post ID.
 * @param string $uri The post URI.
 * @return bool True if successful, otherwise false.
 */
function wl_set_entity_uri($post_id, $uri)
{

    wl_write_log("wl_set_entity_uri [ post id :: $post_id ][ uri :: $uri ]");

    $uri = utf8_decode($uri);
    return update_post_meta($post_id, WL_ENTITY_URL_META_NAME, $uri);
}


/**
 * Get the entity type URIs associated to the specified post.
 *
 * @since 3.0.0
 *
 * @param int $post_id The post ID.
 * @return array An array of terms.
 */
function wl_get_entity_types($post_id)
{

    return get_post_meta($post_id, 'wl_entity_type_uri');
}

/**
 * Set the types for the entity with the specified post ID.
 * @param int $post_id The entity post ID.
 * @param array $type_uris An array of type URIs.
 */
function wl_set_entity_types( $post_id, $type_uris = array() ) {

    // Avoid errors because of null values.
    if ( is_null( $type_uris ) ) {
        $type_uris = array();
    }

    wl_write_log( "wl_set_entity_types [ post id :: $post_id ][ type uris :: " . var_export( $type_uris, true ) . " ]");

    // Ensure there are no duplicates.
    $type_uris = array_unique( $type_uris );

    delete_post_meta($post_id, 'wl_entity_type_uri');
    foreach ($type_uris as $type_uri) {
        if (empty($type_uri)) {
            continue;
        }
        add_post_meta($post_id, 'wl_entity_type_uri', $type_uri);
    }
}

/**
 * Save the specified entities to the local storage.
 * @param array $entities An array of entities.
 * @param int $related_post_id A related post ID.
 * @return array An array of posts.
 */
function wl_save_entities($entities, $related_post_id = null)
{

    wl_write_log("wl_save_entities [ entities count :: " . count($entities) . " ][ related post id :: $related_post_id ]");

    // Prepare the return array.
    $posts = array();

    // Save each entity and store the post id.
    foreach ($entities as $entity) {
        $uri = $entity['uri'];
        $label = $entity['label'];

        // This is the main type URI.
        $main_type_uri = $entity['main_type'];

        // the preferred type.
        $type_uris = $entity['type'];

        $description = $entity['description'];
        $images = (isset($entity['image']) ?
            (is_array($entity['image'])
                ? $entity['image']
                : array($entity['image']))
            : array());
        $same_as = (isset($entity['sameas']) ?
            (is_array($entity['sameas'])
                ? $entity['sameas']
                : array($entity['sameas']))
            : array());

        // Save the entity.
        $post = wl_save_entity( $uri, $label, $main_type_uri, $description, $type_uris, $images, $related_post_id, $same_as );

        // Store the post in the return array if successful.
        if (null !== $post) {
            array_push($posts, $post);
        }
    }

    return $posts;
}

/**
 * Save the specified data as an entity in WordPress. This method only create new entities. When an existing entity is
 * found (by its URI), then the original post is returned.
 *
 * @param string $uri The entity URI.
 * @param string $label The entity label.
 * @param string $type_uri The entity type URI.
 * @param string $description The entity description.
 * @param array $entity_types An array of entity type URIs.
 * @param array $images An array of image URLs.
 * @param int $related_post_id A related post ID.
 * @param array $same_as An array of sameAs URLs.
 *
 * @return null|WP_Post A post instance or null in case of failure.
 */
function wl_save_entity( $uri, $label, $type_uri, $description, $entity_types = array(), $images = array(), $related_post_id = null, $same_as = array() )
{
    // Avoid errors due to null.
    if ( is_null( $entity_types ) ) {
        $entity_types = array();
    }

    wl_write_log( "wl_save_entity [ uri :: $uri ][ label :: $label ][ type uri :: $type_uri ][ related post id :: $related_post_id ]" );

    // Check whether an entity already exists with the provided URI.
    $post = wl_get_entity_post_by_uri( $uri );

    // Return the found post, do not overwrite data.
    if (null !== $post) {
        wl_write_log( "wl_save_entity : post exists [ post id :: $post->ID ][ uri :: $uri ][ label :: $label ][ related post id :: $related_post_id ]" );
        return $post;
    }

    // No post found, create a new one.
    $params = array(
        'post_status'  => ( is_numeric( $related_post_id ) ? get_post_status( $related_post_id ) : 'draft' ),
        'post_type'    => 'entity',
        'post_title'   => $label,
        'post_content' => $description,
        'post_excerpt' => ''
    );

    // create or update the post.
    $post_id = wp_insert_post( $params, true );

    // TODO: handle errors.
    if ( is_wp_error( $post_id ) ) {
        wl_write_log( 'wl_save_entity : error occurred' );
        // inform an error occurred.
        return null;
    }

    wl_set_entity_main_type( $post_id, $type_uri );

    // Save the entity types.
    wl_set_entity_types( $post_id, $entity_types );

    // Get a dataset URI for the entity.
    $wl_uri = wl_build_entity_uri( $post_id );

    // Save the entity URI.
    wl_set_entity_uri( $post_id, $wl_uri );

    // Add the uri to the sameAs data if it's not a local URI.
    if ( $wl_uri !== $uri ) {
        array_push( $same_as, $uri );
    }
    // Save the sameAs data for the entity.
    wl_set_same_as( $post_id, $same_as );

    // Call hooks.
    do_action( 'wl_save_entity', $post_id );

    // If the coordinates are provided, then set them.
//    if (is_array($coordinates) && isset($coordinates['latitude']) && isset($coordinates['longitude'])) {
//        wl_set_coordinates($post_id, $coordinates['latitude'], $coordinates['longitude']);
//    }

    wl_write_log( "wl_save_entity [ post id :: $post_id ][ uri :: $uri ][ label :: $label ][ wl uri :: $wl_uri ][ types :: " . implode(',', $entity_types) . " ][ images count :: " . count($images) . " ][ same_as count :: " . count($same_as) . " ]" );

    foreach ( $images as $image_remote_url ) {

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
            'post_title'     => $label, // Set the title to the post title.
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

    // Add the related post ID if provided.
    if (null !== $related_post_id) {
        // Add related entities or related posts according to the post type.
        wl_add_related( $post_id, $related_post_id );
        // And vice-versa (be aware that relations are pushed to Redlink with wl_push_to_redlink).
        wl_add_related( $related_post_id, $post_id );
    }

    // The entity is pushed to Redlink on save by the function hooked to save_post.
    // save the entity in the triple store.
    wl_push_to_redlink( $post_id );

    // finally return the entity post.
    return get_post( $post_id );
}

/**
 * Retrieve entity property (post meta) starting from the schema.org's property name 
 * This function will be used mostly in theme development and entity editing as a way to
 *  achieve dynamic semantic publishing
 * @param $property_name as defined by schema.org
 * @param (optional) $entity_id, the function will try to retrieve it automatically
 * @return array containing value(s) or null (in case of error or no values).
 */
function wl_get_meta_value( $property_name, $entity_id=null ) {
    
    // Property name must be defined.
    if( !isset( $property_name ) || is_null( $property_name ) ){
        return null;
    }
    
    // Establish entity id.
    if( is_null( $entity_id ) ) {
        $entity_id = get_the_ID();
        if( is_null( $entity_id ) || !is_numeric( $entity_id ) ) {
            return null;
        }
    }
    
    // Get info on the entity relatively to the WL taxonomy
    $terms = wp_get_object_terms( $entity_id, WL_ENTITY_TYPE_TAXONOMY_NAME );
    if( count($terms) == 0 ) {
        return null;
    }
    
    // Get mapping between WL constants and schema.org properties
    $term_info = wl_entity_type_taxonomy_get_term_options( $terms[0]->term_id );
    $term_mapping =  $term_info['custom_fields'];
    
    foreach( $term_mapping as $wl_constant => $schema_name) {
        if( $schema_name == $property_name ) {
            return get_post_meta( $entity_id, $wl_constant );
        }
    }
}