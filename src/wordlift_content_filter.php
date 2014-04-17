<?php

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

add_filter('wp_insert_post_data', 'wl_remove_text_annotations', '99', 1);

