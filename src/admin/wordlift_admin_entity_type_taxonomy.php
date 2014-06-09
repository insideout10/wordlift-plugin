<?php
/**
 * This file contains admin methods related to the Entity Type taxonomy.
 */

/**
 * Save extra taxonomy fields callback function.
 */
function wl_entity_type_taxonomy_save_custom_meta($term_id)
{
    if (isset($_POST['term_meta'])) {

        // Get the values for the term.
        $css_class = $_POST['term_meta']['css_class'];
        $uri = $_POST['term_meta']['uri'];
        $same_as = (!empty($_POST['term_meta']['same_as']) ? preg_split( "/\\r\\n|\\r|\\n/", $_POST['term_meta']['same_as'] ) : array());

        // Update the term data.
        wl_entity_type_taxonomy_update_term($term_id, $css_class, $uri, $same_as);

        wl_write_log("wl_entity_type_save_taxonomy_custom_meta [ term id :: $term_id ]");
    }
}

add_action('edited_wl_entity_type', 'wl_entity_type_taxonomy_save_custom_meta', 10, 2);
add_action('create_wl_entity_type', 'wl_entity_type_taxonomy_save_custom_meta', 10, 2);


// Add term page
function wl_entity_type_taxonomy_add_term_fields()
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

add_action('wl_entity_type_add_form_fields', 'wl_entity_type_taxonomy_add_term_fields', 10, 2);


function wl_entity_type_taxonomy_edit_term_fields($term)
{

    // put the term ID into a variable
    $t_id = $term->term_id;
    $entity_type = wl_entity_type_taxonomy_get_term_options($t_id);
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
    <tr class="form-field">
        <th scope="row" valign="top"><label
                for="term_meta[additional_properties]"><?php _e('Additional Properties', 'wordlift'); ?></label></th>
        <td>
            <textarea name="term_meta[additional_properties]" id="term_meta[additional_properties]"><?php echo ''; ?></textarea>

            <p class="description"><?php _e('Enter a value for this field', 'wordlift') ?></p>
        </td>
    </tr>
<?php

}

add_action('wl_entity_type_edit_form_fields', 'wl_entity_type_taxonomy_edit_term_fields', 10, 2);

