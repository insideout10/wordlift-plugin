<?php
/**
 * This file provides methods for the shortcode *wl_geomap*.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/shortcodes
 */

/**
 * Print both global or post related places in json. It's executed via Ajax
 *
 * @param array $places An array of place posts.
 *
 * @return array An array of place posts.
 *
 */
function wl_shortcode_geomap_ajax() {
	// Get the post Id.
	$post_id = ( isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : null );

	$map_data = ( is_numeric( $post_id )
		? wl_shortcode_geomap_ajax_single_post( $post_id )
		: wl_shortcode_geomap_ajax_all_posts() );

	wl_core_send_json( wl_shortcode_geomap_format_results( $map_data, $post_id ) );
}

function wl_shortcode_geomap_ajax_all_posts() {
	global $wpdb;

	return $wpdb->get_results( $wpdb->prepare(
		"SELECT DISTINCT p1.ID, p1.post_title, pm1.meta_value AS longitude, pm2.meta_value AS latitude"
		. " FROM {$wpdb->posts} p1 "
		. " INNER JOIN {$wpdb->prefix}wl_relation_instances ri"
		. "  ON ri.object_id = p1.ID AND ri.predicate = %s"
		. " INNER JOIN {$wpdb->postmeta} pm1"
		. "  ON pm1.post_id = p1.ID AND pm1.meta_key = %s AND '0' != pm1.meta_value"
		. " INNER JOIN {$wpdb->postmeta} pm2"
		. "  ON pm2.post_id = p1.ID AND pm2.meta_key = %s AND '0' != pm2.meta_value"
		. " WHERE p1.post_status = %s",
		'where',
		'wl_geo_latitude',
		'wl_geo_longitude',
		'publish'
	) );
}

function wl_shortcode_geomap_ajax_single_post( $post_id ) {
	global $wpdb;

	return $wpdb->get_results( $wpdb->prepare(
		"SELECT p2.ID, p2.post_title, pm1.meta_value AS longitude, pm2.meta_value AS latitude"
		. " FROM {$wpdb->posts} p1 "
		. " INNER JOIN {$wpdb->prefix}wl_relation_instances ri"
		. "  ON ri.subject_id = p1.ID AND ri.predicate = %s"
		. " INNER JOIN {$wpdb->posts} p2"
		. "  ON p2.ID = ri.object_id AND p2.post_status = %s"
		. " INNER JOIN {$wpdb->postmeta} pm1"
		. "  ON pm1.post_id = p2.ID AND pm1.meta_key = %s AND '0' != pm1.meta_value"
		. " INNER JOIN {$wpdb->postmeta} pm2"
		. "  ON pm2.post_id = p2.ID AND pm2.meta_key = %s AND '0' != pm2.meta_value"
		. " WHERE p1.ID = %d",
		'where',
		'publish',
		'wl_geo_latitude',
		'wl_geo_longitude',
		$post_id
	) );
}

function wl_shortcode_geomap_get_subjects( $post_id, $exclude_post_id ) {

	global $wpdb;

	return $wpdb->get_results( $wpdb->prepare(
		"SELECT p.ID, p.post_title"
		. " FROM {$wpdb->prefix}wl_relation_instances ri" .
		" INNER JOIN {$wpdb->posts} p" .
		"  ON p.ID = ri.subject_id AND p.post_status = %s AND p.ID != %d" .
		" WHERE ri.object_id = %d AND ri.predicate = %s",
		'publish',
		$exclude_post_id,
		$post_id,
		'where'
	) );
}

function wl_shortcode_geomap_format_results( $results, $post_id = null ) {

	$boundaries = array();
	$features   = array_map( function ( $item ) use ( &$boundaries, $post_id ) {

		$thumbnail_url  = get_the_post_thumbnail_url( $item->ID );
		$thumbnail_html = ( $thumbnail_url ? "<img src='$thumbnail_url' width='100%'>" : '' );

		// Related posts.
		$subjects            = wl_shortcode_geomap_get_subjects( $item->ID, $post_id );
		$subjects_inner_html = array_reduce( $subjects, function ( $carry, $subject ) {

			$permalink = get_permalink( $subject->ID );

			return $carry . sprintf( '<li><a href="%s">%s</a></li>', $permalink, esc_html( $subject->post_title ) );
		}, '' );
		$subjects_html       = ( ! empty( $subjects_inner_html ) ? '<ul>' . $subjects_inner_html . '</ul>' : '' );

		$popup_content = sprintf( '<a href="%s"><h6>%s</h6>%s</a>%s', get_permalink( $item->ID ), $thumbnail_html, esc_html( $item->post_title ), $subjects_html );
		$latitude      = floatval( $item->latitude );
		$longitude     = floatval( $item->longitude );
		$coordinates   = array( $latitude, $longitude, );
		$geometry      = array( 'type' => 'Point', 'coordinates' => $coordinates, );

		$boundaries[] = array( $longitude, $latitude, );

		return array(
			'type'       => 'Feature',
			'properties' => array( 'popupContent' => $popup_content ),
			'geometry'   => $geometry,
		);
	}, $results );

	return array( 'features' => $features, 'boundaries' => $boundaries, );
}

add_action( 'wp_ajax_wl_geomap', 'wl_shortcode_geomap_ajax' );
add_action( 'wp_ajax_nopriv_wl_geomap', 'wl_shortcode_geomap_ajax' );

/**
 * register_block_type for Gutenberg blocks
 */
add_action( 'init', function () {

	// Bail out if the `register_block_type` function isn't available.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	register_block_type( 'wordlift/geomap', array(
		'editor_script'   => 'wl-block-editor',
		'render_callback' => function ( $attributes ) {
			$attr_code = '';
			foreach ( $attributes as $key => $value ) {
				$attr_code .= $key . '="' . $value . '" ';
			}

			return '[wl_geomap ' . $attr_code . ']';
		},
		'attributes'      => array(
			'width'  => array(
				'type'    => 'string',
				'default' => '100%'
			),
			'height' => array(
				'type'    => 'string',
				'default' => '300px'
			),
			'global' => array(
				'type'    => 'bool',
				'default' => false
			),
		)
	) );
} );

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
