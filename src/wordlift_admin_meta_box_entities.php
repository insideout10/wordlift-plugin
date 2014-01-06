<?php

/**
 * Adds the entities meta box (called from *add_meta_boxes* hook).
 */
function wordlift_admin_add_entities_meta_box($post_type) {
    add_meta_box(
        'wordlift_entitities_box',
        __( 'Entities', 'wordlift' ),
        'wordlift_entities_box_content',
        $post_type,
        'side',
        'high'
    );
}

function wordlift_entities_box_content($post) {

//    <span class="textannotation person disambiguated" id="urn:enhancement-b5a082da-3301-cb56-1e5c-1b8c3a838a52" itemid="http://dbpedia.org/resource/Mohamed_Morsi" itemprop="name" itemscope="itemscope" itemtype="http://schema.org/Person"
    $pattern = '/<span class=\"textannotation[^\"]*\" id=\"[^\"]+\" itemid=\"([^\"]+)\"[^>]*>/i';

    $matches = array();
    $count   = preg_match_all ($pattern , $post->post_content, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        echo $match[1] . '<br>';
    }
}

add_action('add_meta_boxes', 'wordlift_admin_add_entities_meta_box');
