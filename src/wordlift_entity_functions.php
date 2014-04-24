<?php
/**
 * This file contains methods related to entities.
 */

/**
 * Build the entity URI given the entity's post.
 * @param int $post_id The post ID.
 * @return string The URI of the entity.
 */
function wl_build_entity_uri($post_id)
{

    // Get the post.
    $post = get_post($post_id);
    if (null === $post) {
        write_log("wl_build_entity_uri : error [ post id :: $post_id ][ post :: null ]");
        return;
    }

    // Create an ID given the title.
    $path = wl_sanitize_uri_path($post->post_title);

    // Create the URL (dataset base URI has a trailing slash).
    $url = sprintf( '%s/%s/%s', wl_config_get_dataset_base_uri(), $post->post_type, $path );

    write_log("wl_build_entity_uri [ post_id :: $post->ID ][ type :: $post->post_type ][ title :: $post->post_title ][ url :: $url ]");

    return $url;
}

/**
 * Get the entity URI of the provided post.
 * @param int $post_id The post ID.
 * @return string|null The URI of the entity or null if not configured.
 */
function wl_get_entity_uri($post_id)
{

    $uri = get_post_meta($post_id, WL_ENTITY_URL_META_NAME, true);
    $uri = utf8_encode($uri);

    // Set the URI if it isn't set yet.
    if (empty($uri) && 'auto-draft' !== get_post_status($post_id)) {
        $uri = wl_build_entity_uri($post_id); //  "http://data.redlink.io/$user_id/$dataset_id/post/$post->ID";
        wl_set_entity_uri($post_id, $uri);
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

    write_log("wl_set_entity_uri [ post id :: $post_id ][ uri :: $uri ]");

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
function wl_set_entity_types($post_id, $type_uris = array())
{

    write_log( "wl_set_entity_types [ post id :: $post_id ][ type uris :: " . var_export( $type_uris, true ) . " ]");

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