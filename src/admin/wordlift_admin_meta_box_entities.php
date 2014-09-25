<?php

/**
 * This file provides methods and functions for the related entities meta-box in the admin UI.
 */

/**
 * Adds the entities meta box (called from *add_meta_boxes* hook).
 *
 * @param string $post_type The type of the current open post.
 */
function wl_admin_add_entities_meta_box($post_type) {
    wl_write_log("wl_admin_add_entities_meta_box [ post type :: $post_type ]");

    add_meta_box(
            'wordlift_entities_box', __('Related Entities', 'wordlift'), 'wl_entities_box_content', $post_type, 'side', 'high'
    );

    // Add meta box for Event and Place entities
    $entity_id = get_the_ID();
    $entity_type = wl_entity_get_type($entity_id);

    if (isset($entity_id) && is_numeric($entity_id) && !is_null($entity_type)) {

        $is_event = strpos($entity_type['uri'], 'Event') !== False;
        $is_place = strpos($entity_type['uri'], 'Place') !== False;

        if ($is_event) {
            add_meta_box(
                    'wordlift_event_entities_box', __('Event duration', 'wordlift'), 'wl_event_entities_box_content', $post_type, 'side', 'default'
            );
            add_meta_box(
                    'wordlift_event_entities_location_box', __('Event location', 'wordlift'), 'wl_event_entities_location_box_content', $post_type, 'side', 'default'
            );
        }

        if ($is_place) {
            add_meta_box(
                    'wordlift_place_entities_box', __('Coordinates', 'wordlift'), 'wl_place_entities_box_content', $post_type, 'side', 'default'
            );
        }
    }
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
function wl_event_entities_box_content($post) {

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
 * Displays the event duration meta box contents (called by *add_meta_box* callback).
 *
 * @param WP_Post $post The current post.
 */
function wl_event_entities_location_box_content( $post ) {

    wp_nonce_field( 'wordlift_event_location_entity_box', 'wordlift_event_location_entity_box_nonce' );
    
    // Get default value, if any
    $defaultPlace = get_post_meta( $post->ID, WL_CUSTOM_FIELD_LOCATION, true );
    if( $defaultPlace !== '' && is_numeric( $defaultPlace ) )
        $defaultPlace = get_post( $defaultPlace );

    // Search entities tagged as Places
    $args = array(
        'posts_per_page'                => -1,
        'orderby'                       => 'RECENCYYYYYYYY',
        'post_type'                     => WL_ENTITY_TYPE_NAME,
        WL_ENTITY_TYPE_TAXONOMY_NAME    => 'Place'
    ); 
    $places = get_posts( $args );
    
    // Write HTML <select>
    if( count( $places ) > 0 ) {
        echo '<label for="' . WL_CUSTOM_FIELD_LOCATION . '">' . __('Location', 'wordlift') . '</label>';
        echo '<select name="' . WL_CUSTOM_FIELD_LOCATION . '" style="width:100%" />';
        
        // Default value
        echo '<option value="' . $defaultPlace->ID . '">' . $defaultPlace->post_title . '</option>';
        foreach( $places as $place ) {
            // Loop over options
            echo '<option value="' . $place->ID . '">' . $place->post_title . '</option>';
        }
        
        echo '</select>';
    }
    
    
    echo "<script type='text/javascript'>
    $ = jQuery;
    $(document).ready(function() {
        console.log('yeah');
    });
    </script>";
}

/**
 * Saves the Event start and end date from entity editor page
 */
function wl_event_entity_type_save_location($post_id) {
    // Check if our nonce is set.
    if ( !isset( $_POST['wordlift_event_location_entity_box_nonce'] ) )
        return $post_id;
    $nonce = $_POST['wordlift_event_location_entity_box_nonce'];

    // Verify that the nonce is valid.
    if ( !wp_verify_nonce( $nonce, 'wordlift_event_location_entity_box' ) )
        return $post_id;

    // save the Event start and end date
    if ( isset( $_POST[WL_CUSTOM_FIELD_LOCATION] ) ) {
        $location = $_POST[WL_CUSTOM_FIELD_LOCATION];
    }
    if ( isset( $location ) && is_numeric( $location ) ) {
        update_post_meta( $post_id, WL_CUSTOM_FIELD_LOCATION, $location );
    }
}

add_action( 'wordlift_save_post', 'wl_event_entity_type_save_location' );


























/**
 * Displays the place meta box contents (called by *add_meta_box* callback).
 *
 * @param WP_Post $post The current post.
 */
function wl_place_entities_box_content($post) {

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
