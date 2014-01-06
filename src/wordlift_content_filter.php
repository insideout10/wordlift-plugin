<?php

function wordlift_remove_text_annotations($data) {
    $data['post_content'] .= ' (test) ';

    return $data;
}

add_filter('wp_insert_post_data', 'wordlift_remove_text_annotations', '99', 1);

