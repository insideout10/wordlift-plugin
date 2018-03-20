<?php
/**
 * This file provides methods for the shortcode *wl_geomap*.
 */

/**
 * Retrieve geomap places. If $post_id is null the return places blog wide
 *
 * @uses wl_core_get_related_entities() to retrieve the entities referenced by the specified post.
 *
 * @param int $post_id The post ID.
 *
 * @return array An array of place posts.
 */
function wl_shortcode_geomap_get_places( $post_id = null ) {

	// If $post_id is null or is not numeric it means this is a global geomap
	$is_global = is_null( $post_id ) || ! is_numeric( $post_id );

	// If the current one is not a global geomap, retrieve related entities ids
	if ( $is_global ) {
		$related_ids = array();
	} else {
		$related_ids = wl_core_get_related_entity_ids( $post_id, array(
			'status' => 'publish'
		) );
		
		// Also include current entity
		if ( Wordlift_Entity_Service::get_instance()->is_entity( $post_id ) ) {
			$related_ids[] = $post_id;
		}
	}

	// If is not a global geomap, an empty $related_ids means that no entities are related to the post
	// An empty array can be returned in this case
	if ( ! $is_global && empty( $related_ids ) ) {
		return array();
	}

	// Retrieve all 'published' places with geo coordinates defined
	// If $related_ids is not empty, it's used to limit query results to the current post related places
	// Please note that when $place_ids is an empty array, the 'post__in' parameter is not considered in the query
	return get_posts( array(
		'post__in'    => $related_ids,
		'post_type'   => Wordlift_Entity_Service::TYPE_NAME,
		'nopaging'    => true,
		'post_status' => 'publish',
		'meta_query'  => array(
			'relation' => 'AND',
			array(
				'key'     => Wordlift_Schema_Service::FIELD_GEO_LATITUDE,
				'value'   => null,
				'compare' => '!=',
			),
			array(
				'key'     => Wordlift_Schema_Service::FIELD_GEO_LONGITUDE,
				'value'   => null,
				'compare' => '!=',
			)
		),
		'tax_query'      => array(
			'taxonomy' => Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME,
			'field'    => 'slug',
			'terms'    => 'place'
		)
	) );
}

/**
 * Encode places array in geojson compliant format
 * (refer to http://leafletjs.com/examples/geojson.html)
 * Define geomap boundaries according to $places
 * Default boundaries are defined using PHP_INT_MAX value
 *
 * @param array $places An array of place posts.
 *
 * @return array An array of markers and boundaries for Leaflet.
 */
function wl_shortcode_geomap_prepare_map( $places ) {

	// Prepare for min/max lat/long in case we need to define a view boundary for the client JavaScript.
	$min_latitude  = PHP_INT_MAX;
	$min_longitude = PHP_INT_MAX;
	$max_latitude  = ~PHP_INT_MAX;
	$max_longitude = ~PHP_INT_MAX;

	// Prepare an empty array of POIs in geoJSON format.
	$pois = array();
	// And store list of points to allow Leaflet compute the optimal bounding box.
	// The main reason for this is that geoJSON has swapped coordinates (lon. lat)
	$boundaries = array();

	// Add a POI for each entity that has coordinates.
	foreach ( $places as $entity ) {

		// Get the coordinates.
		$coordinates = wl_get_coordinates( $entity->ID );

		// Don't show the widget if the coordinates aren't set.
		if ( $coordinates['latitude'] == 0 || $coordinates['longitude'] == 0 ) {
			continue;
		}

		// TODO Map html rendering should be delegated to the wordlift js ui layer
		// This function should be focused on returning pure data instead

		// Get the title, URL and thumb of the entity.
		$title = esc_attr( $entity->post_title );
		$link  = esc_attr( get_permalink( $entity->ID ) );
		if ( '' !== ( $thumbnail_id = get_post_thumbnail_id( $entity->ID ) ) &&
		     false !== ( $attachment = wp_get_attachment_image_src( $thumbnail_id ) )
		) {
			$img_src = esc_attr( $attachment[0] );
		}

		// Build HTML popup. TODO: move thumb width in css
		$content = "<a href=$link><h6>$title</h6>";
		if ( isset( $img_src ) ) {
			$content = $content . "<img src=$img_src style='width:100%'/>";
		}
		$content = $content . "</a><ul>";
		// Get the related posts (published) and print them in the popup.
		$related_posts = wl_core_get_related_post_ids( $entity->ID, array(
			'status' => 'publish'
		) );
		foreach ( $related_posts as $rp_id ) {

			$rp      = get_post( $rp_id );
			$title   = esc_attr( $rp->post_title );
			$link    = esc_attr( get_permalink( $rp->ID ) );
			$content = $content . "<li><a href=$link>$title</a></li>";
		}
		$content = $content . "</ul>";

		// Formatting POI in geoJSON.
		// http://leafletjs.com/examples/geojson.html
		$poi = array(
			'type'       => 'Feature',
			'properties' => array( 'popupContent' => $content ),
			'geometry'   => array(
				'type'        => 'Point',
				'coordinates' => array(
					// Leaflet geoJSON wants them swapped
					$coordinates['longitude'],
					$coordinates['latitude']
				)
			)
		);

		$pois[] = $poi;

		// Formatting boundaries in a Leaflet-like format (see LatLngBounds).
		// http://leafletjs.com/reference.html#latlngbounds
		$boundaries[] = array( $coordinates['latitude'], $coordinates['longitude'] );

	}

	$map_data               = array();
	$map_data['features']   = $pois;
	$map_data['boundaries'] = $boundaries;

	return $map_data;
}

/**
 * Print both global or post related places in json. It's executed via Ajax
 *
 * @uses wl_shortcode_geomap_get_places() in order to retrieve places
 * @uses wl_shortcode_geomap_prepare_map() in order to encode retireved places in a Leaflet friendly format
 *
 * @param array $places An array of place posts.
 *
 * @return array An array of place posts.
 */
function wl_shortcode_geomap_ajax() {
	// Get the post Id.
	$post_id = ( isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : null );

	$places   = wl_shortcode_geomap_get_places( $post_id );
	$map_data = wl_shortcode_geomap_prepare_map( $places );

	wl_core_send_json( $map_data );
}

add_action( 'wp_ajax_wl_geomap', 'wl_shortcode_geomap_ajax' );
add_action( 'wp_ajax_nopriv_wl_geomap', 'wl_shortcode_geomap_ajax' );

///**
// * Print geomap shortcode
// *
// * @param array $atts An array of shortcode attributes.
// *
// * @return string A dom element represneting a geomap.
// */
//function wl_shortcode_geomap( $atts ) {
//
//	// Extract attributes and set default values.
//	$geomap_atts = shortcode_atts( array(
//		'width'  => '100%',
//		'height' => '300px',
//		'global' => false
//	), $atts );
//
//	// Get id of the post
//	$post_id = get_the_ID();
//
//	if ( $geomap_atts['global'] || is_null( $post_id ) ) {
//		// Global geomap
//		$geomap_id = 'wl_geomap_global';
//		$post_id   = null;
//	} else {
//		// Post-specific geomap
//		$geomap_id = 'wl_geomap_' . $post_id;
//	}
//
//	// Add leaflet css and library.
//	wp_enqueue_style(
//		'leaflet',
//		dirname( plugin_dir_url( __FILE__ ) ) . '/bower_components/leaflet/dist/leaflet.css'
//	);
//	wp_enqueue_script(
//		'leaflet',
//		dirname( plugin_dir_url( __FILE__ ) ) . '/bower_components/leaflet/dist/leaflet.js'
//	);
//
//	// Add wordlift-ui css and library.
//	wp_enqueue_style( 'wordlift-ui-css', dirname( plugin_dir_url( __FILE__ ) ) . '/css/wordlift-ui.min.css' );
//
//	wp_enqueue_script( 'wordlift-ui', dirname( plugin_dir_url( __FILE__ ) ) . '/js/wordlift-ui.min.js', array( 'jquery' ) );
//
//	wp_localize_script( 'wordlift-ui', 'wl_geomap_params', array(
//		'ajax_url' => admin_url( 'admin-ajax.php' ),    // Global param
//		'action'   => 'wl_geomap'            // Global param
//	) );
//
//	// Escaping atts.
//	$esc_class   = esc_attr( 'wl-geomap' );
//	$esc_id      = esc_attr( $geomap_id );
//	$esc_width   = esc_attr( $geomap_atts['width'] );
//	$esc_height  = esc_attr( $geomap_atts['height'] );
//	$esc_post_id = esc_attr( $post_id );
//
//	// Return HTML template.
//	return <<<EOF
//<div class="$esc_class"
//	id="$esc_id"
//	data-post-id="$esc_post_id"
//	style="width:$esc_width;
//        height:$esc_height;
//        background-color:gray
//        ">
//</div>
//EOF;
//
//}
//
//add_shortcode( 'wl_geomap', 'wl_shortcode_geomap' );
