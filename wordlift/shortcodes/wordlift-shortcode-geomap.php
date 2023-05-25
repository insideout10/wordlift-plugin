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
 */
function wl_shortcode_geomap_ajax() {
	check_ajax_referer( 'wl_geomap' );
	// Get the post Id.
	$post_id = isset( $_REQUEST['post_id'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['post_id'] ) ) : null;

	$map_data = ( is_numeric( $post_id )
		? wl_shortcode_geomap_ajax_single_post( (int) $post_id )
		: wl_shortcode_geomap_ajax_all_posts() );

	wl_core_send_json( wl_shortcode_geomap_format_results( $map_data, $post_id ) );

}

function wl_shortcode_geomap_ajax_all_posts() {
	global $wpdb;

	return $wpdb->get_results(
		$wpdb->prepare(
			'SELECT DISTINCT p1.ID, p1.post_title, pm1.meta_value AS longitude, pm2.meta_value AS latitude'
			. " FROM {$wpdb->posts} p1 "
			. " INNER JOIN {$wpdb->prefix}wl_relation_instances ri"
			. '  ON ri.object_id = p1.ID AND ri.predicate = %s'
			. " INNER JOIN {$wpdb->postmeta} pm1"
			. "  ON pm1.post_id = p1.ID AND pm1.meta_key = %s AND '0' != pm1.meta_value"
			. " INNER JOIN {$wpdb->postmeta} pm2"
			. "  ON pm2.post_id = p1.ID AND pm2.meta_key = %s AND '0' != pm2.meta_value"
			. ' WHERE p1.post_status = %s',
			'where',
			'wl_geo_latitude',
			'wl_geo_longitude',
			'publish'
		)
	);
}

function wl_shortcode_geomap_ajax_single_post( $post_id ) {
	global $wpdb;

	return $wpdb->get_results(
		$wpdb->prepare(
			"
		SELECT p2.ID, p2.post_title, pm1.meta_value AS longitude, pm2.meta_value AS latitude
		 FROM {$wpdb->prefix}wl_relation_instances ri
		 INNER JOIN {$wpdb->posts} p2
		  ON p2.ID = ri.object_id AND p2.post_status = %s
		 INNER JOIN {$wpdb->postmeta} pm1
		  ON pm1.post_id = p2.ID AND pm1.meta_key = %s AND '0' != pm1.meta_value
		 INNER JOIN {$wpdb->postmeta} pm2
		  ON pm2.post_id = p2.ID AND pm2.meta_key = %s AND '0' != pm2.meta_value
		 WHERE ri.subject_id = %d AND ri.predicate = %s
		UNION 
		SELECT p.ID, p.post_title, pm1.meta_value AS longitude, pm2.meta_value AS latitude
		 FROM {$wpdb->posts} p
		 INNER JOIN {$wpdb->postmeta} pm1
		  ON pm1.post_id = p.ID AND pm1.meta_key = %s AND '0' != pm1.meta_value
		 INNER JOIN {$wpdb->postmeta} pm2
		  ON pm2.post_id = p.ID AND pm2.meta_key = %s AND '0' != pm2.meta_value
		 WHERE p.ID = %s
		",
			'publish',
			'wl_geo_latitude',
			'wl_geo_longitude',
			$post_id,
			'where',
			// UNION
			'wl_geo_latitude',
			'wl_geo_longitude',
			$post_id
		)
	);
}

function wl_shortcode_geomap_get_subjects( $post_id, $exclude_post_id ) {

	global $wpdb;

	return $wpdb->get_results(
		$wpdb->prepare(
			'SELECT p.ID, p.post_title'
			. " FROM {$wpdb->prefix}wl_relation_instances ri" .
			" INNER JOIN {$wpdb->posts} p" .
			'  ON p.ID = ri.subject_id AND p.post_status = %s AND p.ID != %d' .
			' WHERE ri.object_id = %d AND ri.predicate = %s',
			'publish',
			$exclude_post_id,
			$post_id,
			'where'
		)
	);
}

function wl_shortcode_geomap_format_results( $results, $post_id = null ) {

	$boundaries = array();
	$features   = array_map(
		function ( $item ) use ( &$boundaries, $post_id ) {

			$thumbnail_url  = get_the_post_thumbnail_url( $item->ID );
			$thumbnail_html = ( $thumbnail_url ? "<img src='$thumbnail_url' width='100%'>" : '' );

			// Related posts.
			// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
			$subjects            = wl_shortcode_geomap_get_subjects( $item->ID, $post_id );
			$subjects_inner_html = array_reduce(
				$subjects,
				function ( $carry, $subject ) {

					$permalink = get_permalink( $subject->ID );

					return $carry . sprintf( '<li><a href="%s">%s</a></li>', $permalink, esc_html( $subject->post_title ) );
				},
				''
			);
			$subjects_html       = ( ! empty( $subjects_inner_html ) ? '<ul>' . $subjects_inner_html . '</ul>' : '' );

			$popup_content = sprintf( '<a href="%s"><h6>%s</h6>%s</a>%s', get_permalink( $item->ID ), $thumbnail_html, esc_html( $item->post_title ), $subjects_html );
			$latitude      = floatval( $item->latitude );
			$longitude     = floatval( $item->longitude );
			$coordinates   = array( $latitude, $longitude );
			$geometry      = array(
				'type'        => 'Point',
				'coordinates' => $coordinates,
			);

			// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
			$boundaries[] = array( $longitude, $latitude );

			return array(
				'type'       => 'Feature',
				'properties' => array( 'popupContent' => $popup_content ),
				'geometry'   => $geometry,
			);
		},
		$results
	);

	return array(
		'features'   => $features,
		'boundaries' => $boundaries,
	);
}

add_action( 'wp_ajax_wl_geomap', 'wl_shortcode_geomap_ajax' );
add_action( 'wp_ajax_nopriv_wl_geomap', 'wl_shortcode_geomap_ajax' );
