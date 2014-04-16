<?php

/**
 * Registers the entity custom post type (from the *init* hook).
 */
function wordlift_register_custom_type_entity()
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

    register_post_type('entity', $args);
}

/**
 * Add the type taxonomy to the entity (from the *init* hook).
 */
function wordlift_taxonomies_entity()
{

    $labels = array(
        'name' => _x('Entity Types', 'taxonomy general name'),
        'singular_name' => _x('Entity Type', 'taxonomy singular name'),
        'search_items' => __('Search Entity Types'),
        'all_items' => __('All Entity Types'),
        'parent_item' => __('Parent Entity Type'),
        'parent_item_colon' => __('Parent Entity Type:'),
        'edit_item' => __('Edit Entity Type'),
        'update_item' => __('Update Entity Type'),
        'add_new_item' => __('Add New Entity Type'),
        'new_item_name' => __('New Entity Type'),
        'menu_name' => __('Entity Types'),
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => false
    );

    register_taxonomy('wl_entity_type', 'entity', $args);
}

/**
 * Adds the Entity URL box and the Entity SameAs box (from the hook *add_meta_boxes*).
 */
function wordlift_entity_url_box()
{
    add_meta_box(
        'wordlift_entity_box',
        __('Entity URL', 'wordlift'),
        'wordlift_entity_box_content',
        'entity',
        'normal',
        'high'
    );
}

/**
 * Displays the content of the entity URL box (called from the *entity_url* method).
 * @param WP_Post $post The post.
 */
function wordlift_entity_box_content($post)
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
function wordlift_save_entity_custom_fields($post_id)
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

/**
 * Get the entity type URIs associated to the specified post.
 *
 * @since 3.0.0
 *
 * @param int $post_id The post ID.
 * @return array An array of terms.
 */
function wl_get_entity_types($post_id)
{

//    $types = wp_get_post_terms($post_id, 'wl_entity_type');
//
//    if (!empty($prefix)) {
//        array_walk($types, function (&$item) use ($prefix) {
//            $item = $prefix . $item->name;
//        });
//    }
//
//    return $types;

    return get_post_meta($post_id, 'wl_entity_type_uri');
}

/**
 * Set the types for the entity with the specified post ID.
 * @param int $post_id The entity post ID.
 * @param array $type_uris An array of type URIs.
 */
function wl_set_entity_types($post_id, $type_uris = array())
{

    write_log( "wl_set_entity_types [ post id :: $post_id ][ type uris :: " . var_export( $type_uris, true ) . " ]");

    // Ensure there are no duplicates.
    $type_uris = array_unique( $type_uris );

    delete_post_meta($post_id, 'wl_entity_type_uri');
    foreach ($type_uris as $type_uri) {
        if (empty($type_uri)) {
            continue;
        }
        add_post_meta($post_id, 'wl_entity_type_uri', $type_uri);
    }
}

/**
 * Get the entity main type for the specified post ID.
 * @param int $post_id The post ID.
 * @return array|null An array of type properties or null if no term is associated.
 */
function wl_get_entity_main_type( $post_id ) {
    $terms = wp_get_object_terms( $post_id, 'wl_entity_type', array(
        'fields' => 'ids'
    ) );

    if ( is_wp_error($terms) ) {
        // TODO: handle error
        return null;
    }

    // If there are not terms associated, return null.
    if ( 0 === count( $terms ) ) {
        return null;
    }

    // Return the entity type with the specified id.
    return wl_load_entity_type($terms[0]);
}

// Add term page
function wl_entity_type_add_term_fields()
{
    // this will add the custom meta field to the add new term page
    ?>
    <div class="form-field">
        <th scope="row" valign="top"><label
                for="term_meta[css_class]"><?php _e('Entity Type CSS Class', 'wordlift'); ?></label></th>
        <td>
            <input type="text" name="term_meta[css_class]" id="term_meta[css_class]" value="">

            <p class="description"><?php _e('Enter a value for this field', 'wordlift'); ?></p>
        </td>
    </div>
    <div class="form-field">
        <th scope="row" valign="top"><label
                for="term_meta[color]"><?php _e('Entity Type Color', 'wordlift'); ?></label></th>
        <td>
            <input type="text" name="term_meta[color]" id="term_meta[color]" value="">

            <p class="description"><?php _e('Enter a value for this field', 'wordlift'); ?></p>
        </td>
    </div>
    <div class="form-field">
        <th scope="row" valign="top"><label for="term_meta[uri]"><?php _e('Entity Type URI', 'wordlift'); ?></label>
        </th>
        <td>
            <input type="text" name="term_meta[uri]" id="term_meta[uri]" value="">

            <p class="description"><?php _e('Enter a value for this field', 'wordlift'); ?></p>
        </td>
    </div>
    <div class="form-field">
        <th scope="row" valign="top"><label
                for="term_meta[same_as]"><?php _e('Entity Type Alternative URIs', 'wordlift'); ?></label></th>
        <td>
            <textarea name="term_meta[same_as]" id="term_meta[same_as]"></textarea>

            <p class="description"><?php _e('Enter a value for this field', 'wordlift') ?></p>
        </td>
    </div>
<?php
}

add_action('wl_entity_type_add_form_fields', 'wl_entity_type_add_term_fields', 10, 2);


function wl_entity_type_edit_term_fields($term)
{

    // put the term ID into a variable
    $t_id = $term->term_id;
    $entity_type = wl_load_entity_type($t_id);
    $css_class = esc_attr($entity_type['css_class']);
    $uri = esc_attr($entity_type['uri']);
    $same_as = (is_array($entity_type['same_as']) ? esc_attr(implode("\n", $entity_type['same_as'])) : '');

    // retrieve the existing value(s) for this meta field. This returns an array
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label
                for="term_meta[css_class]"><?php _e('Entity Type CSS Class', 'wordlift'); ?></label></th>
        <td>
            <input type="text" name="term_meta[css_class]" id="term_meta[css_class]" value="<?php echo $css_class; ?>">

            <p class="description"><?php _e('Enter a value for this field', 'wordlift'); ?></p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="term_meta[uri]"><?php _e('Entity Type URI', 'wordlift'); ?></label>
        </th>
        <td>
            <input type="text" name="term_meta[uri]" id="term_meta[uri]" value="<?php echo $uri; ?>">

            <p class="description"><?php _e('Enter a value for this field', 'wordlift'); ?></p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label
                for="term_meta[same_as]"><?php _e('Entity Type Alternative URIs', 'wordlift'); ?></label></th>
        <td>
            <textarea name="term_meta[same_as]" id="term_meta[same_as]"><?php echo $same_as; ?></textarea>

            <p class="description"><?php _e('Enter a value for this field', 'wordlift') ?></p>
        </td>
    </tr>
<?php

}

add_action('wl_entity_type_edit_form_fields', 'wl_entity_type_edit_term_fields', 10, 2);

// Save extra taxonomy fields callback function.
function wl_entity_type_save_taxonomy_custom_meta($term_id)
{
    if (isset($_POST['term_meta'])) {

        // Get the values for the term.
        $css_class = $_POST['term_meta']['css_class'];
//        $color = $_POST['term_meta']['color'];
        $uri = $_POST['term_meta']['uri'];
        $same_as = (!empty($_POST['term_meta']['same_as']) ? explode("\n", $_POST['term_meta']['same_as']) : array());

        // Update the term data.
        wl_update_entity_type($term_id, $css_class, $uri, $same_as);

        write_log("wl_entity_type_save_taxonomy_custom_meta [ term id :: $term_id ]");
    }
}

add_action('edited_wl_entity_type', 'wl_entity_type_save_taxonomy_custom_meta', 10, 2);
add_action('create_wl_entity_type', 'wl_entity_type_save_taxonomy_custom_meta', 10, 2);

/**
 * Update an entity type with the provided data.
 * @param int $term_id The numeric term ID.
 * @param string $css_class The stylesheet class.
 * @param string $uri The URI.
 * @param array $same_as An array of sameAs URIs.
 * @return True if option value has changed, false if not or if update failed.
 */
function wl_update_entity_type($term_id, $css_class, $uri, $same_as = array())
{

    write_log("wl_update_entity_type [ term id :: $term_id ][ css class :: $css_class ][ uri :: $uri ][ same as :: " . implode(',', $same_as) . " ]");

    return update_option("wl_entity_type_${term_id}", array(
        'css_class' => $css_class,
        'uri' => $uri,
        'same_as' => $same_as
    ));
}

/**
 * Set the main type for the entity using the related taxonomy.
 * @param int $post_id The numeric post ID.
 * @param string $type_uri A type URI.
 */
function wl_set_entity_main_type( $post_id, $type_uri ) {

    write_log( "wl_set_entity_main_type [ post id :: $post_id ][ type uri :: $type_uri ]" );

    // If the type URI is empty we remove the type.
    if ( empty($type_uri) ) {
        wp_set_object_terms( $post_id, null, 'wl_entity_type');
        return;
    }

    // Get all the terms bound to the wl_entity_type taxonomy.
    $terms = get_terms('wl_entity_type', array(
        'hide_empty' => false,
        'fields' => 'ids'
    ));

    // Check which term matches the specified URI.
    foreach ( $terms as $term_id ) {
        // Load the type data.
        $type = wl_load_entity_type($term_id);

        // Set the related term ID.
        if ($type_uri === $type['uri']) {
            wp_set_object_terms( $post_id, (int)$term_id, 'wl_entity_type');
            return;
        }
    }
}

/**
 * Get the data for the specified entity type (term id).
 * @param int $term_id A numeric term ID.
 * @return mixed|void The entity type data.
 */
function wl_load_entity_type($term_id)
{

    return get_option("wl_entity_type_${term_id}");
}

/**
 * Prints inline JavaScript with the entity types configuration.
 */
function wl_print_entity_type_inline_js()
{

    $terms = get_terms('wl_entity_type', array(
        'hide_empty' => false,
        'fields' => 'ids'
    ));

    echo <<<EOF
    <script type="text/javascript">
        (function() {
        var t = [];

EOF;

    // Cycle in terms and print them out to the JS.
    foreach ($terms as $term_id) {
        // Load the type data.
        $type = wl_load_entity_type($term_id);

        // Skip types that are not defined.
        if (null === $type['uri']) {
            continue;
        }

        // Assign the data to vars for printing to the JS.
        $uri = json_encode($type['uri']);
        $css_class = json_encode($type['css_class']);
        $same_as = json_encode($type['same_as']);

        echo <<<EOF
            t.push({
                css: $css_class,
                uri: $uri,
                sameAs: $same_as
            });

EOF;
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

add_action('init', 'wordlift_register_custom_type_entity');
add_action('init', 'wordlift_taxonomies_entity', 0);
add_action('add_meta_boxes', 'wordlift_entity_url_box');
add_action('save_post', 'wordlift_save_entity_custom_fields');
