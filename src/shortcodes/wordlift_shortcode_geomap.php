<?php

function wl_shortcode_geomap_get_places( $post_id = null ) {
		
	// Are we in a post?
    if ( is_null( $post_id ) ) {
        // Global GeoMap
        // We fill the array $entity_ids with (blog wide) interesting places
        $post_ids = get_posts( array(
	        'numberposts' => 20,
	        'fields'      => 'ids', //only get post IDs
	        'orderby'     => 'post_date',
	        'order'       => 'DESC'
	    ) );
		
		if( empty( $post_ids ) ){
			return array();
		}
		
		// Retrieve referenced entities
	    $entity_ids = array();
	    foreach ( $post_ids as $id ) {
	        $entity_ids = array_merge( $entity_ids, wl_get_referenced_entity_ids( $id ) );
	    }
		
    } else {
    	// Post-specific GeoMap
     	$entity_ids = wl_get_referenced_entity_ids( $post_id );
    }

    // If there are no entity IDs, we don't show the map.
    if (0 === count($entity_ids)) {
        return array();
    }
	
	// Get entities that have coordinates.
    $places = get_posts( array(
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

	return $places;
}

function wl_shortcode_geomap_to_json( $places ) {
		
	// Prepare for min/max lat/long in case we need to define a view boundary for the client JavaScript.
    $min_latitude  = PHP_INT_MAX;
    $min_longitude = PHP_INT_MAX;
    $max_latitude  = ~PHP_INT_MAX;
    $max_longitude = ~PHP_INT_MAX;

    // Prepare an empty array of POIs.
    $pois = array();

    // Add a POI for each entity that has coordinates.
    foreach ($places as $entity) {

        // Get the coordinates.
        $coordinates = wl_get_coordinates( $entity->ID );

        // Don't show the widget if the coordinates aren't set.
        if (!is_array($coordinates) || !is_numeric($coordinates['latitude']) || !is_numeric($coordinates['longitude'])) {
            continue;
        }

        // Ignore entities that are not published.
        if ('publish' !== $entity->post_status) {
            continue;
        }

        // Get the title of the entity.
        $title   = esc_attr( $entity->post_title );
        $link    = esc_attr( get_permalink( $entity->ID ) );
        $content = "<a href=$link>$title</a>";
		
		// Formatting POI in geoJSON.
		// http://leafletjs.com/examples/geojson.html
		$poi = array(
			'type'			=> 'Feature',
			'properties'	=> array( 'popupContent' => $content ),
			'geometry'		=> array(
										'type' => 'Point',
										'coordinates' => array(
															// Leaflet geoJSON wants them swapped
															$coordinates['longitude'],
															$coordinates['latitude']
														)
									)
		);
		
		$pois[] = $poi;

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

	// Formatting boundaries in a Leaflet-like format (see LatLngBounds).
	// http://leafletjs.com/reference.html#latlngbounds
	$boundaries = array(
						array( $min_latitude, $min_longitude ),
						array( $max_latitude, $max_longitude )
					);

	$jsondata = array();
	$jsondata['features'] = $pois;
	$jsondata['boundaries'] = $boundaries;
    	
	return json_encode( $jsondata );
}

function wl_shortcode_geomap_ajax()
{
	// Get the ID of the post who requested the timeline.
    $post_id = ( isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : null );

    ob_clean();
    header( "Content-Type: application/json" );

    $result = wl_shortcode_geomap_get_places( $post_id );
    $result = wl_shortcode_geomap_to_json( $result );
	
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
		$post_id = null;
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

	// Escaping atts.
    $esc_class  = esc_attr('wl-geomap');
    $esc_id     = esc_attr($geomap_id);
	$esc_width  = esc_attr($geomap_atts['width']);
	$esc_height = esc_attr($geomap_atts['height']);
    $esc_post_id 	= esc_attr($post_id);
	
	// Return HTML template.
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

}

function wl_register_shortcode_geomap() {
    add_shortcode('wl-geomap', 'wl_shortcode_geomap');
}

add_action( 'init', 'wl_register_shortcode_geomap');