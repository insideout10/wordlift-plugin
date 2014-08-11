<?php
/**
 * This file provides methods and functions for the related entities meta-box in the admin UI.
 */

/**
 * Adds the entities meta box (called from *add_meta_boxes* hook).
 *
 * @param string $post_type The type of the current open post.
 */
function wl_admin_add_entities_meta_box( $post_type )
{
    wl_write_log("wl_admin_add_entities_meta_box [ post type :: $post_type ]");

    add_meta_box(
        'wordlift_entities_box',
        __('Related Entities', 'wordlift'),
        'wl_entities_box_content',
        $post_type,
        'side',
        'high'
    );
    
    // Add meta box for Event entities
    $entity_id = get_the_ID();
    $entity_type = wl_entity_get_type( $entity_id );
    
    if( isset( $entity_id ) && is_numeric( $entity_id ) && !is_null( $entity_type ) ) {
        //$is_event = ( $entity_type['uri'] == 'wl-event' );
        $is_event = strpos( $entity_type['uri'], 'Event' ) !== False;

        if( $is_event ) {
            add_meta_box(
                'wordlift_event_entities_box',
                __('Event duration', 'wordlift'),
                'wl_event_entities_box_content',
                $post_type,
                'side',
                'default'
            );
        }
    }
}

/**
 * Displays the meta box contents (called by *add_meta_box* callback).
 *
 * @param WP_Post $post The current post.
 */
function wl_entities_box_content($post)
{

    wl_write_log("wl_entities_box_content [ post id :: $post->ID ]");

    // get the related entities IDs.
    $related_entities_ids = wl_get_referenced_entity_ids( $post->ID );

    if (!is_array($related_entities_ids)) {
        wl_write_log("related_entities_ids is not of the right type.");

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

/**
 * Displays the event meta box contents (called by *add_meta_box* callback).
 *
 * @param WP_Post $post The current post.
 */
function wl_event_entities_box_content( $post ) {
    
    wp_enqueue_script('jquery-ui-datepicker');
    
    wp_nonce_field('wordlift_event_entity_box', 'wordlift_event_entity_box_nonce');

    $start_date = get_post_meta( $post->ID, WL_CUSTOM_FIELD_CAL_DATE_START, true );
    $start_date = esc_attr( $start_date );
    
    echo '<label for="wl_event_start">' . __('Start date', 'wordlift') . '</label>';
    echo '<input type="text" id="wl_event_start" class="wl_datepicker" name="wl_event_start" value="' . $start_date . '" style="width:100%" />';

    $end_date = get_post_meta( $post->ID, WL_CUSTOM_FIELD_CAL_DATE_END, true );
    $end_date = esc_attr( $end_date );
    echo '<label for="wl_event_end">' . __('End date', 'wordlift') . '</label>';
    echo '<input type="text" id="wl_event_end" class="wl_datepicker" name="wl_event_end" value="' . $end_date . '" style="width:100%" />';
    
    echo "<script type='text/javascript'>
    $ = jQuery;
    $(document).ready(function() {
        $('.wl_datepicker').each( function() {
            $(this).datepicker({
                dateFormat: 'yy-mm-dd',
                defaultDate: $(this).val()
            });
        });
    });
    </script>";
}

add_action('add_meta_boxes', 'wl_admin_add_entities_meta_box');

/**
 * Saves the Event start and end date from entity editor page
 */
function wl_event_entity_type_save_start_and_end_date($post_id)
{
    // Check if our nonce is set.
    if ( !isset($_POST['wordlift_event_entity_box_nonce']) )
        return $post_id;
    $nonce = $_POST['wordlift_event_entity_box_nonce'];

    // Verify that the nonce is valid.
    if ( !wp_verify_nonce($nonce, 'wordlift_event_entity_box') )
        return $post_id;

    // save the Event start and end date
    if( isset( $_POST['wl_event_start'] ) && isset( $_POST['wl_event_end'] ) ) {
        $start = $_POST['wl_event_start'];
        $end = $_POST['wl_event_end'];
    }
    if( isset( $start ) && strtotime( $start ) ) {
        update_post_meta($post_id, WL_CUSTOM_FIELD_CAL_DATE_START, $start);
    }
    if( isset( $end ) && strtotime( $end ) ) {
        update_post_meta($post_id, WL_CUSTOM_FIELD_CAL_DATE_END, $end);
    }
}
add_action('wordlift_save_post', 'wl_event_entity_type_save_start_and_end_date');