<?php
/**
 * This file contains miscellaneous admin-functions.
 */

/**
 * Removes empty text annotations from the post content.
 * @param array $data The post data.
 * @return array mixed The post data array.
 */
function wl_remove_text_annotations($data) {

    write_log("wl_remove_text_annotations [ data content :: ${data['post_content']} ]");

    //    <span class="textannotation" id="urn:enhancement-777cbed4-b131-00fb-54a4-ed9b26ae57ea">
    //    $pattern = '/<span class=\\\"textannotation\\\" id=\\\"[^\"]+\\\">([^<]+)<\/span>/i';
    $pattern = '/<(\w+)[^>]*\sclass=\\\"textannotation\\\"[^>]*>([^<]+)<\/\1>/im';

    // Remove the pattern while it is found (match nested annotations).
    while (1 === preg_match($pattern, $data['post_content'])) {
        $data['post_content'] = preg_replace($pattern, '$2', $data['post_content'], -1, $count);
    }

    return $data;
}
add_filter('wp_insert_post_data', 'wl_remove_text_annotations', '98', 1);

/**
 * Get an array of entities from the *itemid* attributes embedded in the provided content.
 * @param string $content The content with itemid attributes.
 * @return array An array of entity posts.
 */
function wl_content_get_embedded_entities($content)
{

    // Remove quote escapes.
    $content = str_replace('\\"', '"', $content);

    // Match all itemid attributes.
    $pattern = '/<\w+[^>]*\sitemid="([^"]+)"[^>]*>/im';

    // Remove the pattern while it is found (match nested annotations).
    $matches = array();

    // In case of errors, return an empty array.
    if (false === preg_match_all($pattern, $content, $matches)) {
        return array();
    }

//    write_log("wl_update_related_entities [ content :: $content ][ data :: " . var_export($data, true). " ][ matches :: " . var_export($matches, true) . " ]");

    // Collect the entities.
    $entities = array();
    foreach ($matches[1] as $uri) {
        $entity = wl_get_entity_post_by_uri($uri);
        if (null !== $entity) {
            array_push($entities, $entity->ID);
        }
    }

    return $entities;
}