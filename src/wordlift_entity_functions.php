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

    // Build the entity URI.
//    $url = sprintf(
//        'http://data.redlink.io/%s/%s/%s/%s',
//        wl_config_get_user_id(),
//        wl_config_get_dataset(),
//        $post->post_type,
//        $path
//    );

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
    if (empty($uri)) {
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

