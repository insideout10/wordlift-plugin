<?php

/**
 * Registers the entity custom post type (from the *init* hook).
 */
function wl_entity_type_register()
{

    $labels = array(
        'name' => _x('Entities', 'post type general name', 'wordlift'),
        'singular_name' => _x('Entity', 'post type singular name', 'wordlift'),
        'add_new' => _x('Add New', 'entity', 'wordlift'),
        'add_new_item' => __('Add New Entity', 'wordlift'),
        'edit_item' => __('Edit Entity', 'wordlift'),
        'new_item' => __('New Entity', 'wordlift'),
        'all_items' => __('All Entities', 'wordlift'),
        'view_item' => __('View Entity', 'wordlift'),
        'search_items' => __('Search Entities', 'wordlift'),
        'not_found' => __('No entities found', 'wordlift'),
        'not_found_in_trash' => __('No entities found in the Trash', 'wordlift'),
        'parent_item_colon' => '',
        'menu_name' => 'Entities'
    );

    $args = array(
        'labels' => $labels,
        'description' => 'Holds our entities and entity specific data',
        'public' => true,
        'menu_position' => 20, // after the pages menu.
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
        'has_archive' => true,
        'taxonomies' => array('category')
    );

    register_post_type( WL_ENTITY_TYPE_NAME, $args);
}
add_action('init', 'wl_entity_type_register');


/**
 * Adds the Entity URL box and the Entity SameAs box (from the hook *add_meta_boxes*).
 */
function wl_entity_type_meta_boxes()
{
    add_meta_box(
        'wordlift_entity_box',
        __('Entity URL', 'wordlift'),
        'wl_entity_type_meta_boxes_content',
        'entity',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'wl_entity_type_meta_boxes');

/**
 * Displays the content of the entity URL box (called from the *entity_url* method).
 * @param WP_Post $post The post.
 */
function wl_entity_type_meta_boxes_content($post)
{
    wp_nonce_field('wordlift_entity_box', 'wordlift_entity_box_nonce');

    $value = wl_get_entity_uri($post->ID);

    echo '<label for="entity_url">' . __('entity-url-label', 'wordlift') . '</label>';
    echo '<input type="text" id="entity_url" name="entity_url" placeholder="enter a URL" value="' . esc_attr($value) . '" style="width: 100%;" />';

    $same_as = implode("\n", wl_get_same_as($post->ID));

    echo '<label for="entity_same_as">' . __('entity-same-as-label', 'wordlift') . '</label>';
    echo '<textarea style="width: 100%;" id="entity_same_as" name="entity_same_as" placeholder="Same As URL">' . esc_attr($same_as) . '</textarea>';

    $entity_types = implode("\n", wl_get_entity_types($post->ID));

    echo '<label for="entity_types">' . __('entity-types-label', 'wordlift') . '</label>';
    echo '<textarea style="width: 100%;" id="entity_types" name="entity_types" placeholder="Entity Types URIs">' . esc_attr($entity_types) . '</textarea>';
}

/**
 * Saves the entity URL for the specified post ID (set via the *save_post* hook).
 * @param int $post_id The post ID.
 * @return int|null
 */
function wl_entity_type_save_custom_fields($post_id)
{

    // Check if our nonce is set.
    if (!isset($_POST['wordlift_entity_box_nonce']))
        return $post_id;

    $nonce = $_POST['wordlift_entity_box_nonce'];

    // Verify that the nonce is valid.
    if (!wp_verify_nonce($nonce, 'wordlift_entity_box'))
        return $post_id;

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;

    // Check the user's permissions.
    if ('page' == $_POST['post_type']) {

        if (!current_user_can('edit_page', $post_id))
            return $post_id;

    } else {

        if (!current_user_can('edit_post', $post_id))
            return $post_id;
    }

    // save the entity URL.
    wl_set_entity_uri(
        $post_id,
        $_POST['entity_url']
    );

    // save the same as values.
    wl_set_same_as(
        $post_id,
        explode("\r\n", $_POST['entity_same_as'])
    );

    // save the same as values.
    wl_set_entity_types(
        $post_id,
        explode("\r\n", $_POST['entity_types'])
    );

}
add_action('save_post', 'wl_entity_type_save_custom_fields');

/**
 * Set the main type for the entity using the related taxonomy.
 * @param int $post_id The numeric post ID.
 * @param string $type_uri A type URI.
 */
function wl_set_entity_main_type( $post_id, $type_uri ) {

    write_log( "wl_set_entity_main_type [ post id :: $post_id ][ type uri :: $type_uri ]" );

    // If the type URI is empty we remove the type.
    if ( empty($type_uri) ) {
        wp_set_object_terms( $post_id, null, WL_ENTITY_TYPE_TAXONOMY_NAME);
        return;
    }

    // Get all the terms bound to the wl_entity_type taxonomy.
    $terms = get_terms(WL_ENTITY_TYPE_TAXONOMY_NAME, array(
        'hide_empty' => false,
        'fields' => 'ids'
    ));

    // Check which term matches the specified URI.
    foreach ( $terms as $term_id ) {
        // Load the type data.
        $type = wl_entity_type_taxonomy_get_term_options($term_id);

        // Set the related term ID.
        if ($type_uri === $type['uri']) {
            wp_set_object_terms( $post_id, (int)$term_id, WL_ENTITY_TYPE_TAXONOMY_NAME);
            return;
        }
    }
}

/**
 * Prints inline JavaScript with the entity types configuration removing duplicates.
 */
function wl_print_entity_type_inline_js()
{

    $terms = get_terms(WL_ENTITY_TYPE_TAXONOMY_NAME, array(
        'hide_empty' => false,
        'fields' => 'id=>name'
    ));

    echo <<<EOF
    <script type="text/javascript">
        (function() {
        var t = [];

EOF;

    array_walk_recursive ( $terms, function( &$name, $term_id ) {

        // Load the type data.
        $type = wl_entity_type_taxonomy_get_term_options($term_id);

        // Skip types that are not defined.
        if (null !== $type['uri']) {

            $name = json_encode(array(
                'label'     =>  $name,
                'uri'       =>  $type['uri'],
                'css'       =>  $type['css_class'],
                'sameAs'    =>  $type['same_as'],
                'templates' =>  ( isset( $type['templates'] ) ? $type['templates'] : array() ),
            ));

        }
    });

    // Remove duplicates
    $terms = array_unique($terms);
    // Cycle in terms and print them out to the JS.
    foreach ($terms as $type) {
        echo "t.push($type);";
    }

    echo <<<EOF
            if ('undefined' == typeof window.wordlift) {
                window.wordlift = {}
            }
            window.wordlift.types = t;

        })();
    </script>
    <style>

EOF;

    // Stylesheets are defined in the wordlift.css file.
//    // Cycle in terms and print them out to the JS.
//    foreach ($terms as $term_id) {
//        // Load the type data.
//        $type = wl_load_entity_type( $term_id );
//
//        // Skip types that are not defined.
//        if ( null === $type['css_class'] ) {
//            continue;
//        }
//
//        // Assign the data to vars for printing to the JS.
//        $css_class = esc_attr( $type['css_class'] );
//        $color = esc_attr( $type['color'] );
//        $contrast_color = 'black';
//
//        echo <<<EOF
//            .$css_class { border-color: $color; }
//            .$css_class .type { color: $color; }
//            .$css_class .type:before { content: ''; }
//            .$css_class:hover, .$css_class.selected { background-color: $color; }
//            .$css_class:hover div, .$css_class.selected div { color: $contrast_color; }
//            .$css_class:hover .type, .$css_class.selected .type { color: $contrast_color; }
//            .$css_class .thumbnail { background-color: $color; }
//
//EOF;
//    }

    echo '</style>';

}

add_action('admin_print_scripts', 'wl_print_entity_type_inline_js');

add_action('init', 'wl_entity_type_taxonomy_register', 0);
