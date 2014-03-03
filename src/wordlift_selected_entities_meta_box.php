<?php

function wordlift_admin_add_selected_entities_meta_box( $post_type ) {

    add_meta_box(
        'wordlift_selected_entitities_box',
        'Currently Selected Entities',
        'wordlift_selected_entities_box_content',
        $post_type
    );
}

function wordlift_selected_entities_box_content( $post ) {

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
