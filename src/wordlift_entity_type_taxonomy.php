<?php
/**
 * This file contains methods related to the Entity Type taxonomy.
 */

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
 * Add the type taxonomy to the entity (from the *init* hook).
 */
function wl_entity_type_taxonomy_register()
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

