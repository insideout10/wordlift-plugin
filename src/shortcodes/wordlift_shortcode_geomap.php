<?php

function wl_shortcode_geomap_get_places( $post_id = null ) {
		
	// Are we in a post?
    if ( is_null( $post_id ) ) {
        // Global GeoMap
        // TODO: Instead of returning it empty, we have to fill the array $entity_ids
        //		 with (blog wide) interesting places
        return array();
    } else {
    	// Post-specific GeoMap
     	$entity_ids = wl_get_referenced_entity_ids( $post_id );
    }

    // If there are no entity IDs, we don't show the map.
    if (0 === count($entity_ids)) {
        return;
    }
	
	// Get all the entities that have a meta key with date start and end information.
    $res = get_posts( array(
        'post__in' => $entity_ids,
        'post_type' => WL_ENTITY_TYPE_NAME,
        'posts_per_page' => -1,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => WL_CUSTOM_FIELD_GEO_LATITUDE,
                'value' => null,
                'compare' => '!=',
            ),
            array(
                'key' => WL_CUSTOM_FIELD_GEO_LONGITUDE,
                'value' => null,
                'compare' => '!=',
            )
        )
    ) );
	
	print_r( $res );

    // Prepare for min/max lat/long in case we need to define a view boundary for the client JavaScript.
    $min_latitude  = PHP_INT_MAX;
    $min_longitude = PHP_INT_MAX;
    $max_latitude  = ~PHP_INT_MAX;
    $max_longitude = ~PHP_INT_MAX;

    // Prepare an empty array of POIs.
    $pois = array();

    // Add a POI for each entity that has coordinates.
    foreach ($entity_ids as $entity_id) {

        // Get the coordinates.
        $coordinates = wl_get_coordinates($entity_id);

        // Don't show the widget if the coordinates aren't set.
        if (!is_array($coordinates) || !is_numeric($coordinates['latitude']) || !is_numeric($coordinates['longitude'])) {
            continue;
        }

        $entity = get_post($entity_id);

        // Ignore entities that are not published.
        if ('publish' !== $entity->post_status) {
            continue;
        }

        // Get the title of the entity.
        $title   = htmlentities( $entity->post_title );
        $link    = htmlentities( get_permalink( $entity->ID ) );
        $content = json_encode( "<a href=\"$link\">$title</a>" );

        array_push( $pois, array(
            'latitude'     => $coordinates['latitude'],
            'longitude'    => $coordinates['longitude'],
            'popupContent' => $content
        ) );

        // TODO: calculate the type to choose a marker of the appropriate color.

        // Set a reference to the coordinates.
        $latitude =  & $coordinates['latitude'];
        if ( $latitude < $min_latitude ) {
            $min_latitude = $latitude;
        }
        if ( $latitude > $max_latitude ) {
            $max_latitude = $latitude;
        }
        $longitude =  & $coordinates['longitude'];
        if ( $longitude < $min_longitude ) {
            $min_longitude = $longitude;
        }
        if ( $longitude > $max_longitude ) {
            $max_longitude = $longitude;
        }
    }

    return $pois;
}

function wl_shortcode_geomap_to_json() {
	$arr = array( 1,2,3,4,5 );
	return json_encode( $arr );
}

function wl_shortcode_geomap_ajax()
{
	// Get the ID of the post who requested the timeline.
    $post_id = ( isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : null );

    ob_clean();
    header( "Content-Type: application/json" );

    //$result = wl_shortcode_geomap_get_places( $post_id );
    $result = wl_shortcode_geomap_to_json( $result );
	
	write_log( var_export($result, true) );
    echo $result;
    wp_die();
}
add_action( 'wp_ajax_wl_geomap', 'wl_shortcode_geomap_ajax' );
add_action( 'wp_ajax_nopriv_wl_geomap', 'wl_shortcode_geomap_ajax' );


function wl_shortcode_geomap( $atts ) {
	
    // Extract attributes and set default values.
    $geomap_atts = shortcode_atts( array(
        'width'      => '100%',
        'height'     => '300px',
        'global'     => false
    ), $atts );
	
	// Get id of the post
	$post_id = get_the_ID();
	
	if ( $geomap_atts['global'] || is_null( $post_id ) ) {
		// Global geomap
        $geomap_id = 'wl_geomap_global';
    } else {
    	// Post-specific geomap
        $geomap_id = 'wl_geomap_' . $post_id;
    }	

    // Add leaflet css and library.
    wp_enqueue_style(
    	'leaflet_css',
    	plugins_url( 'bower_components/leaflet/dist/leaflet.css', __FILE__ )
	);
	wp_enqueue_script(
        'leaflet_js',
        plugins_url( 'bower_components/leaflet/dist/leaflet.js', __FILE__ )
    );

	// Add wordlift-ui css and library.
	wp_enqueue_style( 'wordlift-ui-css', plugins_url( 'css/wordlift.ui.min.css', __FILE__ ) );
	wp_enqueue_script( 'wordlift-ui', plugins_url( 'js/wordlift.ui.min.js', __FILE__ ), array( 'jquery' ) );
	wp_localize_script( 'wordlift-ui', 'wl_geomap_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),	// Global param
        'action'   => 'wl_geomap'					// Global param
    ) );

	/*
	
    // Print out the header.
    echo <<<EOF
    <script type="text/javascript">
        jQuery( function() {

            // Initialize the features array.
            var features = [], bounds = [];

EOF;

    // Print out each POI.
    foreach ($pois as $poi) {
        $popupContent = & $poi['popupContent'];
        $latitude     = & $poi['latitude'];
        $longitude    = & $poi['longitude'];

        // Print each feature.
        echo <<<EOF
                features.push({
                    "type": "Feature",
                    "properties": {
                        "popupContent": $popupContent
                    },
                    "geometry": {
                        "type": "Point",
                        "coordinates": [$longitude, $latitude]
                    }
                });

EOF;

    }

    // The element id for the map.
    $element_id = uniqid('map-');

    // Print the remainder of the JavaScript including the initialization stuff.
    echo <<<EOF

                    // create a map in the "map" div, set the view to a given place and zoom
                    var map = L.map('$element_id');

                    // Set the bounds of the map or the center, according on how many features we have on the map.
                    if (1 === features.length) {
                        map.setView([$latitude, $longitude], 13);
                    } else {
                        map.fitBounds([
                            [$min_latitude, $min_longitude],
                            [$max_latitude, $max_longitude]
                        ]);
                    }

                    // add an OpenStreetMap tile layer
                    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    L.geoJson(features, {
                        pointToLayer: function (feature, latlng) {
                            return L.marker(latlng, {});
                        },
                        onEachFeature: function onEachFeature(feature, layer) {
                            // does this feature have a property named popupContent?
                            if (feature.properties && feature.properties.popupContent) {
                                layer.bindPopup(feature.properties.popupContent);
                            }
                        }
                    }).addTo(map);

                } );
            </script>
EOF;

    // Get the widget's title.
    $title = apply_filters('widget_title', $instance['title']);

    // Print the HTML output.
    echo $args['before_widget'];
    if (!empty($title)) {
        echo $args['before_title'] . $title . $args['after_title'];
    }
*/
	
	
	
	// Escaping atts.
    $esc_class  = esc_attr('wl-geomap');
    $esc_id     = esc_attr($geomap_id);
	$esc_width  = esc_attr($geomap_atts['width']);
	$esc_height = esc_attr($geomap_atts['height']);

    $esc_post_id 	= esc_attr($post_id);
	
    return <<<EOF
<div class="$esc_class" 
	id="$esc_id"
	data-post-id="$esc_post_id"
	style="width:$esc_width;
        height:$esc_height;
        background-color:gray
        ">
</div>
EOF;

    //echo $args['after_widget'];

}

function wl_register_shortcode_geomap() {
    add_shortcode('wl-geomap', 'wl_shortcode_geomap');
}

add_action( 'init', 'wl_register_shortcode_geomap');