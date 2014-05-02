<?php

/**
 * Adds the entities meta box (called from *add_meta_boxes* hook).
 */
function wl_admin_add_entities_meta_box($post_type)
{

    write_log("wl_admin_add_entities_meta_box [ post type :: $post_type ]");

    add_meta_box(
        'wordlift_entitities_box',
        __('Related Entities', 'wordlift'),
        'wl_entities_box_content',
        $post_type,
        'side',
        'high'
    );
}

/**
 * Displays the meta box contents (called by *add_meta_box* callback).
 * @param WP_Post $post The current post.
 */
function wl_entities_box_content($post)
{

    write_log("wl_entities_box_content [ post id :: $post->ID ]");

    // get the related entities IDs.
    $related_entities_ids = get_post_meta($post->ID, 'wordlift_related_entities', true);

    if (!is_array($related_entities_ids)) {
        write_log("related_entities_ids is not of the right type.");

        // print an empty entities array.
        wl_entities_box_js(array());
        return;
    }

    // check if there are related entities.
    if (!is_array($related_entities_ids) || 0 === count($related_entities_ids)) {
        _e('No related entities', 'wordlift');

        // print an empty entities array.
        wl_entities_box_js(array());
        return;
    }

    // The Query
    $args = array(
        'post_status' => 'any',
        'post__in' => $related_entities_ids,
        'post_type' => 'entity'
    );
    $query = new WP_Query($args);
    $related_entities = $query->get_posts();

    // Print out each entity.
    foreach ($related_entities as $related_entity) {
        echo('<a href="' . get_edit_post_link($related_entity->ID) . '">' . $related_entity->post_title . '</a><br>');
    }

    // Print the JavaScript representation of the entities.
    wl_entities_box_js($related_entities);
}

/**
 * Print out a javascript representation of the provided entities collection.
 * @param array $entities An array of entities.
 */
function wl_entities_box_js( $entities ) {

    echo <<<EOF
    <script type="text/javascript">
        jQuery( function() {
            var e = {};

EOF;

    foreach ($entities as $entity) {
        // uri
        $uri = json_encode( wl_get_entity_uri( $entity->ID ) );
        // entity object
        $obj = json_encode( wl_serialize_entity( $entity ) );
        
        echo "e[$uri] = $obj;";        
    }

    echo <<<EOF
        if ('undefined' == typeof window.wordlift) {
            window.wordlift = {}
        }
        window.wordlift.entities = e;

        } );
    </script>
EOF;

}

add_action('add_meta_boxes', 'wl_admin_add_entities_meta_box');
