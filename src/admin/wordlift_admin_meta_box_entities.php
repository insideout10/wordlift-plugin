<?php

/**
 * This file provides methods and functions to generate entities meta-boxes in the admin UI.
 */

/**
 * Adds the entities meta box (called from *add_meta_boxes* hook).
 *
 * @param string $post_type The type of the current open post.
 */
function wl_admin_add_entities_meta_box( $post_type ) {
    wl_write_log("wl_admin_add_entities_meta_box [ post type :: $post_type ]");
    
    // Add meta box for related entities (separated from the others for historical reasons)
    add_meta_box(
            'wordlift_entities_box', __('Related Entities', 'wordlift'), 'wl_entities_box_content', $post_type, 'side', 'high'
    );

    // Add meta box for specific type of entities
    $entity_id = get_the_ID();
    $entity_type = wl_entity_get_type($entity_id);

    if ( isset($entity_id) && is_numeric($entity_id) && isset( $entity_type['custom_fields'] ) ) {
        
        // In some special case, properties must be grouped in one metabox (e.g. coordinates)
        $metaboxes = wl_entities_metaboxes_group_properties_by_input_field( $entity_type['custom_fields'] );
        $simple_metaboxes = $metaboxes[0];
        $grouped_metaboxes = $metaboxes[1];
        
        // Loop over simple entity properties
        foreach( $simple_metaboxes as $key => $property ) {

            // Metabox title
            $title = __( 'Edit', 'wordlift' ) . ' ' . __( $property['predicate'], 'wordlift' );
            
            // Info passed to the metabox
            $info = array();
            $info[ $key ] = $property;

            switch( $property['type'] ) {
                case WL_DATA_TYPE_URI:
                    add_meta_box(
                        'wordlift_uri_entities_box', $title, 'wl_entities_uri_box_content', $post_type, 'side', 'high', $info
                    );
                    break;
                case WL_DATA_TYPE_DATE:
                    add_meta_box(
                        'wordlift_date_entities_box', $title, 'wl_entities_date_box_content', $post_type, 'side', 'high'
                    );
                    break;
                case WL_DATA_TYPE_INTEGER:
                    add_meta_box(
                        'wordlift_int_entities_box', $title, 'wl_entities_int_box_content', $post_type, 'side', 'high'
                    );
                    break;
                case WL_DATA_TYPE_DOUBLE:
                    add_meta_box(
                        'wordlift_double_entities_box', $title, 'wl_entities_double_box_content', $post_type, 'side', 'high'
                    );
                    break;
                case WL_DATA_TYPE_BOOLEAN:
                    add_meta_box(
                        'wordlift_bool_entities_box', $title, 'wl_entities_bool_box_content', $post_type, 'side', 'high'
                    );
                    break;
                case WL_DATA_TYPE_STRING:
                    add_meta_box(
                        'wordlift_string_entities_box', $title, 'wl_entities_string_box_content', $post_type, 'side', 'high'
                    );
                    break;
            }
        }
        
        // Loop over grouped properties
        foreach( $grouped_metaboxes as $key => $property ) {
            
            // Metabox title
            $title = __( 'Edit', 'wordlift' ) . ' ' . __( $key, 'wordlift' );

            switch( $key ) {
                case 'coordinates':
                    add_meta_box(
                        'wordlift_coordinates_entities_box', $title, 'wl_entities_coordinates_box_content', $post_type, 'side', 'high'
                    );
                    break;
            }
        }
    }
}

function wl_entities_metaboxes_group_properties_by_input_field( $custom_fields ) {
    
    $simple_properties = array();
    $grouped_properties = array();
    
    // Loop over possible entity properties
    foreach( $custom_fields as $key => $property ) {
        
        // Check presence of predicate and type
        if( isset( $property['predicate'] ) && isset( $property['type'] ) ) {
            
            // Check if input_field is defined
            if( isset( $property['input_field'] ) && $property['input_field'] !== '' ) {
                
                $grouped_key = $property['input_field'];
                
                // Update list of grouped properties
                $grouped_properties[$grouped_key][$key] = $property;
       
            } else {
                
                // input_field not defined, add simple metabox
                $simple_properties[$key] = $property;
            }
        }
    }
    
    return array( $simple_properties, $grouped_properties );
}

add_action('add_meta_boxes', 'wl_admin_add_entities_meta_box');

/**
 * Displays the meta box contents (called by *add_meta_box* callback).
 *
 * @param WP_Post $post The current post.
 */
function wl_entities_box_content($post) {

    wl_write_log("wl_entities_box_content [ post id :: $post->ID ]");

    // get the related entities IDs.
    $related_entities_ids = wl_get_referenced_entity_ids($post->ID);

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
function wl_entities_box_js($entities) {

    echo <<<EOF
    <script type="text/javascript">
        jQuery( function() {
            var e = {};

EOF;

    foreach ($entities as $entity) {
        // uri
        $uri = json_encode(wl_get_entity_uri($entity->ID));
        // entity object
        $obj = json_encode(wl_serialize_entity($entity));

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
function wl_entities_date_box_content($post) {

    wp_enqueue_script('jquery-ui-datepicker');

    wp_nonce_field('wordlift_event_entity_box', 'wordlift_event_entity_box_nonce');

    $start_date = get_post_meta($post->ID, WL_CUSTOM_FIELD_CAL_DATE_START, true);
    $start_date = esc_attr($start_date);

    echo '<label for="wl_event_start">' . __('Start date', 'wordlift') . '</label>';
    echo '<input type="text" id="wl_event_start" class="wl_datepicker" name="wl_event_start" value="' . $start_date . '" style="width:100%" />';

    $end_date = get_post_meta($post->ID, WL_CUSTOM_FIELD_CAL_DATE_END, true);
    $end_date = esc_attr($end_date);
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

/**
 * Saves the Event start and end date from entity editor page
 */
function wl_event_entity_type_save_start_and_end_date($post_id) {
    // Check if our nonce is set.
    if (!isset($_POST['wordlift_event_entity_box_nonce']))
        return $post_id;
    $nonce = $_POST['wordlift_event_entity_box_nonce'];

    // Verify that the nonce is valid.
    if (!wp_verify_nonce($nonce, 'wordlift_event_entity_box'))
        return $post_id;

    // save the Event start and end date
    if (isset($_POST['wl_event_start']) && isset($_POST['wl_event_end'])) {
        $start = $_POST['wl_event_start'];
        $end = $_POST['wl_event_end'];
    }
    if (isset($start) && strtotime($start)) {
        update_post_meta($post_id, WL_CUSTOM_FIELD_CAL_DATE_START, $start);
    }
    if (isset($end) && strtotime($end)) {
        update_post_meta($post_id, WL_CUSTOM_FIELD_CAL_DATE_END, $end);
    }
}

add_action('wordlift_save_post', 'wl_event_entity_type_save_start_and_end_date');












/**
 * Displays jQuery autocomplete in a meta box, to assign an entity as property value (e.g. location of an Event).
 *
 * @param WP_Post $post The current post.
 * @param $custom_fields Array The custom field the method must manage.
 */
function wl_entities_uri_box_content( $post, $args ) {
    
    // Which meta/custom_field are we managing?
    $custom_field = $args['args'];
    $meta_name = ( array_keys( $custom_field ) );
    $meta_name = $meta_name[0];
    
    // Which type of entity is object?
    $expected_type = $custom_field[$meta_name]['constraints'];
    
    // Set Nonce
    wp_nonce_field( 'wordlift_uri_entity_box', 'wordlift_uri_entity_box_nonce' );
    
    // Get default value, if any
    $defaultEntity = get_post_meta( $post->ID, $meta_name, true );
    if( $defaultEntity !== '' && is_numeric( $defaultEntity ) ) {
        $defaultEntity = get_post( $defaultEntity );
    }

    // Search entities of the expected type
    $args = array(
        'posts_per_page'                => -1,
        'orderby'                       => 'RECENCYYYYYYYY',
        'post_type'                     => WL_ENTITY_TYPE_NAME,
        WL_ENTITY_TYPE_TAXONOMY_NAME    => $expected_type
    ); 
    $candidates = get_posts( $args );
    
    // Write HTML
    if( count( $candidates ) > 0 ) {
        // Input to show the options
        echo '<input id="autocompleteEntity" style="width:100%" >';
        // Input to store the actual chosen values ( autocomplete quirks... )
        echo '<input type="hidden" id="autocompleteEntityHidden" name="' . $meta_name . '">';

        // Add jQuery Autocomplete
        wp_enqueue_script( 'jquery-ui-autocomplete' );
 
        // Filter $candidates to only contain id and name
        $simpleCandidates = array_map(function($p) {
            return array( 'value' => $p->ID, 'label' => $p->post_title ); 
        }, $candidates);
        
        // Add null value (to delete location)
        $nullCandidate = array( 'value' => '', 'label' => __('<no location>', 'wordlift') );
        array_unshift( $simpleCandidates, $nullCandidate );
        
        // Add to Autocomplete available place
        wp_localize_script( 'jquery-ui-autocomplete', 'availableEntities',
            array(
                'list'      => $simpleCandidates,
                'default'   => $defaultEntity
            )
        );
        
        var_dump('TODO: - insert uri insted of id in the postmeta. - adjust saving method');

        echo "<script type='text/javascript'>
        $ = jQuery;
        $(document).ready(function() {
            var selector = '#autocompleteEntity';
            var hiddenSelector = '#autocompleteEntityHidden';
            
            // Default label and value
            if( availableEntities.default.hasOwnProperty( 'ID' ) ){
                $(selector).val( availableEntities.default.post_title );
                $(hiddenSelector).val( availableEntities.default.ID );
            }
            
            // Init autocomplete
            $(selector).autocomplete({
                minLength: 0,
                source: availableEntities.list,
                select: function( event, ui ){
                    // Display label but store value in the hidden <input>
                    event.preventDefault();
                    $(selector).val( ui.item.label );
                    $(hiddenSelector).val( ui.item.value );
                },
                focus: function( event, ui ) {
                    // Do not show values instead of the label
                    event.preventDefault();
                    $(selector).val(ui.item.label);
                }
            });
        });
        </script>";
    } else {
        echo __('No entities of the right type found.', 'wordlift');
    }
}

/**
 * Saves the entity chosen from the entity metabox in the entity editor page
 */
function wl_entity_uri_metabox_save($post_id) {
    // Check if our nonce is set.
    if ( !isset( $_POST['wordlift_uri_entity_box_nonce'] ) )
        return $post_id;
    $nonce = $_POST['wordlift_uri_entity_box_nonce'];

    // Verify that the nonce is valid.
    if ( !wp_verify_nonce( $nonce, 'wordlift_uri_entity_box' ) )
        return $post_id;
    
    // Save the property value for this entity
    if ( isset( $_POST[WL_CUSTOM_FIELD_LOCATION] ) ) {
        $location = $_POST[WL_CUSTOM_FIELD_LOCATION];
    }
    if ( isset( $location ) && is_numeric( $location ) ) {
        update_post_meta( $post_id, WL_CUSTOM_FIELD_LOCATION, $location );
    } else {
        delete_post_meta( $post_id, WL_CUSTOM_FIELD_LOCATION );
    }
}
add_action( 'wordlift_save_post', 'wl_entity_uri_metabox_save' );








///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
/////////////////// General metaboxes /////////////////////////
///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////










/**
 * Displays the place meta box contents (called by *add_meta_box* callback).
 *
 * @param WP_Post $post The current post.
 */
function wl_entities_coordinates_box_content($post) {
    
    // Add leaflet css and library.
    wp_enqueue_style(
            'leaflet_css', plugins_url('bower_components/leaflet/dist/leaflet.css', __FILE__)
    );
    wp_enqueue_script(
            'leaflet_js', plugins_url('bower_components/leaflet/dist/leaflet.js', __FILE__)
    );
    
    // Security.
    wp_nonce_field('wordlift_place_entity_box', 'wordlift_place_entity_box_nonce');
    
    // Get coordinates
    $coords = wl_get_coordinates($post->ID);
    $latitude = $coords['latitude'];
    $longitude = $coords['longitude'];
    
    // Default coords values [0, 0]
    if( !isset( $longitude ) || !is_numeric( $longitude ))
        $longitude = 0.0;
    if( !isset( $latitude ) || !is_numeric( $latitude ))
        $latitude = 0.0;
    
    // Default zoom value
    if( $latitude==0.0 || $longitude==0.0 ) {
        $zoom = 1;  // Choose from a world panoramic
    } else {
        $zoom = 9;  // Close up view
    }
    
    // Print input fields
    echo '<label for="wl_place_lat">' . __('Latitude', 'wordlift') . '</label>';
    echo '<input type="text" id="wl_place_lat" name="wl_place_lat" value="' . $latitude . '" style="width:100%" />';

    echo '<label for="wl_place_lon">' . __('Longitude', 'wordlift') . '</label>';
    echo '<input type="text" id="wl_place_lon" name="wl_place_lon" value="' . $longitude . '" style="width:100%" />';

    // Show Leaflet map to pick coordinates
    echo "<div id='wl_place_coords_map'></div>";
    echo "<script type='text/javascript'>
    $ = jQuery;
    $(document).ready(function(){
        $('#wl_place_coords_map').width('100%').height('200px');
        var wlMap = L.map('wl_place_coords_map').setView([$latitude, $longitude], $zoom);
    
        L.tileLayer( 'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
            { attribution: '&copy; <a href=http://osm.org/copyright>OpenStreetMap</a> contributors'}
        ).addTo( wlMap );
        
        var marker = L.marker([$latitude, $longitude]).addTo( wlMap );
    
        function refreshCoords(e) {
            $('#wl_place_lat').val( e.latlng.lat );
            $('#wl_place_lon').val( e.latlng.lng );
            marker.setLatLng( e.latlng )
        }

        wlMap.on('click', refreshCoords);
    });
    </script>";
}

/**
 * Saves the Place coordinates from entity editor page
 */
function wl_place_entity_type_save_coordinates($post_id) {
    // Check if our nonce is set.
    if ( !isset($_POST['wordlift_place_entity_box_nonce']) )
        return $post_id;
    $nonce = $_POST['wordlift_place_entity_box_nonce'];

    // Verify that the nonce is valid.
    if ( !wp_verify_nonce($nonce, 'wordlift_place_entity_box') )
        return $post_id;

    // save the Place start and end date
    if( isset( $_POST['wl_place_lat'] ) && isset( $_POST['wl_place_lon'] ) ) {
        $latitude = $_POST['wl_place_lat'];
        $longitude = $_POST['wl_place_lon'];
    }
    if( isset( $latitude ) && is_numeric( $latitude ) ) {
        update_post_meta($post_id, WL_CUSTOM_FIELD_GEO_LATITUDE, $latitude);
    }
    if( isset( $longitude ) && is_numeric( $longitude ) ) {
        update_post_meta($post_id, WL_CUSTOM_FIELD_GEO_LONGITUDE, $longitude);
    }
}
add_action('wordlift_save_post', 'wl_place_entity_type_save_coordinates');
