<?php
/**
 * This file provides methods for the shortcode *wl_geomap*.
 */

/**
 * Retrieve geomap places. If $post_id is null the return places blog wide
 *
 * @uses wl_get_referenced_entities to retrieve the entities referenced by the specified post.
 *
 * @param int $post_id The post ID.
 * @return array An array of place posts.
 */
function wl_shortcode_geomap_get_places( $post_id = null ) {

    // If $post_id is null or is not numeric it means this is a global geomap	
	$is_global = ( is_null( $post_id ) ? true : false );
    $is_global = ( !is_numeric($post_id) ? true : $is_global );

    // If the current one is not a global geomap, retrieve related post / place ids
    $place_ids = $is_global ? array() : wl_get_referenced_entities( $post_id );
    
    // If is not a global geomap, an empty $place_ids means that no place is related to the post
    // An empty array can be returned in this case
    if( !$is_global && empty($place_ids) ) {
        return array();
    }

	// Retrieve all 'published' places with geo coordinates defined 
    // If $place_ids is not empty, it's used to limit query results to the current post related places
    // Please note that when $place_ids is an empty array, the 'post__in' parameter is not considered in the query
    $places = get_posts( array(
        'post__in' => $place_ids,
        'post_type' => WL_ENTITY_TYPE_NAME,
        'nopaging' => true,
        'post_status' => 'published',
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
/**
 * Encode places array in geojson compliant format 
 * (refer to http://leafletjs.com/examples/geojson.html)
 * Define geomap boundaries according to $places
 * Default boundaries are defined using PHP_INT_MAX value
 *
 * @param array $places An array of place posts.
 * @return array An array of place posts.
 */
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

        // TODO Map html rendering should be delegated to the wordlift js ui layer
        // This function should be focused on returning pure data instead

        // Get the title, URL and thumb of the entity.
        $title   = esc_attr( $entity->post_title );
        $link    = esc_attr( get_permalink( $entity->ID ) );
		if ( '' !== ( $thumbnail_id = get_post_thumbnail_id( $entity->ID ) ) &&
            false !== ( $attachment = wp_get_attachment_image_src( $thumbnail_id ) ) ) {
			$img_src = esc_attr( $attachment[0] );
		}
		
		// Build HTML popup. TODO: move thumb width in css
        $content = "<a href=$link>
        				<h6>$title</h6>";
		if( isset( $img_src ) ) {
        	$content = $content . "<img src=$img_src style='width:100%'/>";
		}
        $content = $content . "</a><ul>";
		// Get the related posts (published) and print them in the popup.
    	$related_posts = wl_get_referencing_posts( $entity->ID );
      	foreach ( $related_posts as $rp_id ) {
                $rp = get_post( $rp_id );
        	$title   = esc_attr( $rp->post_title );
        	$link    = esc_attr( get_permalink( $rp->ID ) );
			$content = $content . "<li><a href=$link>$title</a></li>";
		}
		$content = $content . "</ul>";
		
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

    // TODO Baundaries management could be delegated to the wordlift js ui layer
        
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
/**
 * Print both global or post related places in json. It's executed via Ajax
 *
 * @uses wl_shortcode_geomap_get_places in order to retrieve places
 * @uses wl_shortcode_geomap_to_json in order to encode retireved places as json object
 *
 * @param array $places An array of place posts.
 * @return array An array of place posts.
 */
function wl_shortcode_geomap_ajax()
{
	// Get the post Id.
    $post_id = ( isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : null );

    ob_clean();
    header( "Content-Type: application/json" );

    $places = wl_shortcode_geomap_get_places( $post_id );
    echo wl_shortcode_geomap_to_json( $places );
	
    wp_die();
}

add_action( 'wp_ajax_wl_geomap', 'wl_shortcode_geomap_ajax' );
add_action( 'wp_ajax_nopriv_wl_geomap', 'wl_shortcode_geomap_ajax' );

/**
 * Print geomap shortcode
 *
 * @param array $atts An array of shortcode attributes.
 * @return string A dom element represneting a geomap.
 */
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

add_shortcode('wl_geomap', 'wl_shortcode_geomap');