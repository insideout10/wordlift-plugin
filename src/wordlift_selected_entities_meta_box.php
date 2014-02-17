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
        'Currently Selected Entities',
        'wordlift_selected_entities_box_content',
        $post_type
    );
}

function wordlift_selected_entities_box_content($post) {

echo <<<EOF
		<div class="wl-entity-tab-wrapper">
		<ul class="entities">
			<li ng-repeat="(index, entity) in getSelectedEntities()">
				<div wl-meta-box-selected-entity index="index" entity="entity"></div>
			</li>
		</ul>
		</div>
EOF;
}

add_action('add_meta_boxes', 'wordlift_admin_add_selected_entities_meta_box');
