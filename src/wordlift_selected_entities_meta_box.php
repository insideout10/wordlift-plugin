<?php

/*
Plugin Name: WordLift
Plugin URI: http://wordlift.it
Description: Supercharge your WordPress Site with Smart Tagging and #Schemaorg support - a brand new way to write, organise and publish your contents to the Linked Data Cloud.
Version: 3.0.0-SNAPSHOT
Author: InSideOut10
Author URI: http://www.insideout.io
License: APL
*/

function wordlift_admin_add_selected_entities_meta_box($post_type) {
    add_meta_box(
        'wordlift_selected_entitities_box',
        'Selected Entities',
        'wordlift_selected_entities_box_content',
        $post_type
    );
}

function wordlift_selected_entities_box_content($post) {
	echo('<div class="wordlift-entity-tab"></div>');
}

add_action('add_meta_boxes', 'wordlift_admin_add_selected_entities_meta_box');
