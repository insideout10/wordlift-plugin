<?php
/**
 * This file contains admin methods related to the Entity Type taxonomy.
 */

/**
 * Entity taxonomy metabox must show exclusive options, no checkboxes.
 */
add_filter('wp_terms_checklist_args', 'wl_change_taxonomy_metabox_checkboxes_into_option');
function wl_change_taxonomy_metabox_checkboxes_into_option( $args ) {
    
        if ( isset( $args['taxonomy'] ) && $args['taxonomy'] == WL_ENTITY_TYPE_TAXONOMY_NAME ) {
            
            // We override the way WP prints the taxonomy metabox HTML
            $args['walker'] = new Wordlift_Taxonomy_Walker; // See class below.
            $args['checked_ontop'] = false;
        }
    return $args;
}

/**
 * This class will help wordpress to print the Entity taxonomy metabox in order to show exclusive options, no checkboxes.
 */
class Wordlift_Taxonomy_Walker extends Walker {
    var $tree_type = 'category';
    var $db_fields = array ('parent' => 'parent', 'id' => 'term_id');

    function start_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent<ul class='children'>\n";
    }

    function end_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }
    
    function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
        extract($args);
        if ( empty($taxonomy) )
            $taxonomy = 'category';

        if ( $taxonomy == 'category' )
            $name = 'post_category';
        else
            $name = 'tax_input['.$taxonomy.']';

        /** @var $popular_cats */
        $class = in_array( $category->term_id, $popular_cats ) ? ' class="popular-category"' : '';
        /** @var $selected_cats */
        $output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" . '<label class="selectit"><input value="' . $category->term_id . '" type="radio" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' . checked( in_array( $category->term_id, $selected_cats ), TRUE, FALSE ) . disabled( empty( $args['disabled'] ), FALSE, FALSE ) . ' /> ' . esc_html( apply_filters('the_category', $category->name )) . '</label>';
    }

    function end_el( &$output, $category, $depth = 0, $args = array() ) {
        $output .= "</li>\n";
    }
}

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

