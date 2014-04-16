<?php

/**
 * Removes empty text annotations from the post content.
 * @param array $data The post data.
 * @return array mixed The post data array.
 */
function wordlift_remove_text_annotations($data) {

    //    <span class="textannotation" id="urn:enhancement-777cbed4-b131-00fb-54a4-ed9b26ae57ea">
    $pattern = '/<span class=\\\"textannotation\\\" id=\\\"[^\"]+\\\">([^<]+)<\/span>/i';
    $data['post_content'] = preg_replace($pattern, '$1', $data['post_content'], -1, $count);

    return $data;
}

add_filter('wp_insert_post_data', 'wordlift_remove_text_annotations', '99', 1);

