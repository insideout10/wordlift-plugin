<?php
/**
 * Faceted Search Shortcode.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/shortcodes
 */

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-amp-service.php';

/**
 * Function in charge of diplaying the [wl-faceted-search].
 *
 * @param array $atts Shortcode attributes.
 */
function wl_web_shortcode_faceted_search( $atts ) {

	// Extract attributes and set default values.
	$shortcode_atts = shortcode_atts( array(
		'title'          => __( 'Related articles', 'wordlift' ),
		'show_facets'    => true,
		'with_carousel'  => true,
		'squared_thumbs' => false,
		'limit'          => 20,

	), $atts );

	foreach (
		array(
			'show_facets',
			'with_carousel',
			'squared_thumbs',
		) as $att
	) {

		// See http://wordpress.stackexchange.com/questions/119294/pass-boolean-value-in-shortcode.
		$shortcode_atts[ $att ] = filter_var(
			$shortcode_atts[ $att ], FILTER_VALIDATE_BOOLEAN
		);
	}

	// If the current post is not an entity and has no related entities
	// than the shortcode cannot be rendered
	// TODO Add an alert visibile only for connected admin users.
	$current_post = get_post();

	$entity_service = Wordlift_Entity_Service::get_instance();
	$entity_ids     = $entity_service->is_entity( $current_post->ID ) ?
		array( $current_post->ID ) :
		wl_core_get_related_entity_ids( $current_post->ID );

	// Bail if there are no entity ids.
	if ( 0 === count( $entity_ids ) ) {
		return '';
	}

	$div_id = 'wordlift-faceted-entity-search-widget';

	$deps = apply_filters( 'wl_include_font_awesome', true )
		? array( 'wordlift-font-awesome' )
		: array();
	wp_enqueue_style( 'wordlift-faceted-search', dirname( plugin_dir_url( __FILE__ ) ) . '/css/wordlift-faceted-entity-search-widget.min.css', $deps, Wordlift::get_instance()->get_version() );
	wp_enqueue_script( 'angularjs', 'https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.3.11/angular.min.js' );
	wp_enqueue_script( 'angularjs-touch', 'https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.3.11/angular-touch.min.js' );

	wp_enqueue_script( 'wordlift-faceted-search', dirname( plugin_dir_url( __FILE__ ) ) . '/js/wordlift-faceted-entity-search-widget.min.js' );

	wp_localize_script(
		'wordlift-faceted-search',
		'wl_faceted_search_params', array(
			'ajax_url'             => admin_url( 'admin-ajax.php' ),
			'action'               => 'wl_faceted_search',
			'post_id'              => $current_post->ID,
			'entity_ids'           => $entity_ids,
			'limit'                => apply_filters( 'wl_faceted_search_limit', $shortcode_atts['limit'] ),
			'div_id'               => $div_id,
			'defaultThumbnailPath' => WL_DEFAULT_THUMBNAIL_PATH,
			'attrs'                => $shortcode_atts,
			'l10n'                 => array(
				'what'  => _x( 'What', 'Faceted Search Widget', 'wordlift' ),
				'who'   => _x( 'Who', 'Faceted Search Widget', 'wordlift' ),
				'where' => _x( 'Where', 'Faceted Search Widget', 'wordlift' ),
				'when'  => _x( 'When', 'Faceted Search Widget', 'wordlift' ),
			),
		)
	);

	return '<div id="' . $div_id . '" style="width:100%"></div>';
}

/**
 * Function in charge of diplaying the [wl-faceted-search].
 *
 * @param array $atts Shortcode attributes.
 * 
 * @since 3.20.0
 * 
 * @return String <amp-list><template><amp-carousel> tags
 */
function wl_amp_shortcode_faceted_search( $atts ) {

	// Extract attributes and set default values.
	$shortcode_atts = shortcode_atts( array(
		'title'          => __( 'Related articles', 'wordlift' ),
		'show_facets'    => true,
		'with_carousel'  => true,
		'squared_thumbs' => false,
		'limit'          => 20,

	), $atts );

	foreach (
		array(
			'show_facets',
			'with_carousel',
			'squared_thumbs',
		) as $att
	) {

		// See http://wordpress.stackexchange.com/questions/119294/pass-boolean-value-in-shortcode.
		$shortcode_atts[ $att ] = filter_var(
			$shortcode_atts[ $att ], FILTER_VALIDATE_BOOLEAN
		);
	}

	// If the current post is not an entity and has no related entities
	// than the shortcode cannot be rendered
	// TODO Add an alert visibile only for connected admin users.
	$current_post = get_post();

	$entity_service = Wordlift_Entity_Service::get_instance();
	$entity_ids     = $entity_service->is_entity( $current_post->ID ) ?
		array( $current_post->ID ) :
		wl_core_get_related_entity_ids( $current_post->ID );

	// Bail if there are no entity ids.
	if ( 0 === count( $entity_ids ) ) {
		return '';
	}
	
	$wp_json_base = get_rest_url() . WL_REST_ROUTE_DEFAULT_NAMESPACE;
	$query = array(
		'post_id'	=> $current_post->ID,
		'limit'		=> apply_filters( 'wl_faceted_search_limit', $shortcode_atts['limit'] ),
	);

	if ( strpos($wp_json_base, 'wp-json/' . WL_REST_ROUTE_DEFAULT_NAMESPACE) ){
		$delimiter = '?';
	} else {
		$delimiter = '&';
	}

	// Use a protocol-relative URL as amp-list spec says that URL's protocol must be HTTPS.
	// This is a hackish way, but this works for http and https URLs
	$wp_json_url = str_replace(array('http:', 'https:'), '', $wp_json_base) . '/faceted-search' . $delimiter . http_build_query($query);

	return '
	<amp-list width="auto"
    	height="300"
    	layout="fixed-height"
    	src="'.$wp_json_url.'">
		<template type="amp-mustache">  
			<amp-carousel 
				height="300"
				layout="fixed-height"
				type="carousel">
			{{#values}}
				<div style="width: 300px; height:300px">
				<amp-img src="{{images}}"
					height="225"
					layout="flex-item"
					alt="{{label}}"></amp-img>
				<div style="white-space:normal!important"><a href="{{id}}">{{label}}</a></div> 
				</div>	
			{{/values}}
			</amp-carousel>
		</template>
    </amp-list>';

}

function wl_shortcode_faceted_search( $atts ) {
	if( Wordlift_AMP_Service::is_amp_endpoint() ) {
		return wl_amp_shortcode_faceted_search( $atts );
	} else {
		return wl_web_shortcode_faceted_search( $atts );
	}
}

add_shortcode( 'wl_faceted_search', 'wl_shortcode_faceted_search' );


/**
 * Shared function between Ajax call and wp-json for the faceted search widget
 */
function wl_shortcode_faceted_search_data( $http_raw_data = null ) {

	// Post ID must be defined.
	if ( ! isset( $_GET['post_id'] ) ) { // WPCS: input var ok; CSRF ok.
		wp_die( 'No post_id given' );

		return;
	}

	// Extract filtering conditions.
	$filtering_entity_uris = ( null == $http_raw_data ) ? file_get_contents( 'php://input' ) : $http_raw_data;
	$filtering_entity_uris = json_decode( $filtering_entity_uris );

	$current_post_id = $_GET['post_id']; // WPCS: input var ok; CSRF ok.
	$current_post    = get_post( $current_post_id );

	// Post ID has to match an existing item.
	if ( null === $current_post ) {
		wp_die( 'No valid post_id given' );

		return;
	}

	// If the current post is an entity,
	// the current post is used as main entity.
	// Otherwise, current post related entities are used.
	$entity_service = Wordlift_Entity_Service::get_instance();
	$entity_ids     = $entity_service->is_entity( $current_post->ID ) ?
		array( $current_post->ID ) :
		wl_core_get_related_entity_ids( $current_post->ID );

	// If there are no entities we cannot render the widget.
	if ( 0 === count( $entity_ids ) ) {
		wp_die( 'No entities available' );

		return;
	}

	// Retrieve requested type
	$required_type = ( isset( $_GET['type'] ) ) ? $_GET['type'] : null; // WPCS: input var ok; CSRF ok.

	$limit = ( isset( $_GET['limit'] ) ) ? (int) $_GET['limit'] : 20;  // WPCS: input var ok; CSRF ok.

	$referencing_posts = Wordlift_Relation_Service::get_instance()->get_article_subjects(
		$entity_ids,
		'*',
		null,
		'publish',
		array( $current_post_id ),
		$limit
	);

	$referencing_post_ids = array_map( function ( $p ) {
		return $p->ID;
	}, $referencing_posts );
	$results              = array();

	if ( 'posts' === $required_type ) {

		// Required filtered posts.
		wl_write_log( "Going to find related posts for the current post [ post ID :: $current_post_id ]" );

		$filtered_posts = ( empty( $filtering_entity_uris ) ) ?
			$referencing_posts :
			Wordlift_Relation_Service::get_instance()->get_article_subjects(
				wl_get_entity_post_ids_by_uris( $filtering_entity_uris ),
				'*',
				null,
				null,
				array(),
				null,
				$referencing_post_ids
			);

		if ( $filtered_posts ) {
			foreach ( $filtered_posts as $post_obj ) {

				/**
				 * Use the thumbnail.
				 *
				 * @see https://github.com/insideout10/wordlift-plugin/issues/825 related issue.
				 * @see https://github.com/insideout10/wordlift-plugin/issues/837
				 *
				 * @since 3.19.3 We're using the medium size image.
				 */
				$thumbnail           = get_the_post_thumbnail_url( $post_obj, 'medium' );
				$post_obj->thumbnail = ( $thumbnail ) ?
					$thumbnail : WL_DEFAULT_THUMBNAIL_PATH;
				$post_obj->permalink = get_post_permalink( $post_obj->ID );

				$results[] = $post_obj;
			}
		}
	} else {

		global $wpdb;

		wl_write_log( "Going to find related entities for the current post [ post ID :: $current_post_id ]" );

		// Retrieve Wordlift relation instances table name.
		$table_name = wl_core_get_relation_instances_table_name();

		/*
		 * Make sure we have some referenced post, otherwise the IN parts of
		 * the SQL will produce an SQL error.
		 */
		if ( ! empty( $referencing_post_ids ) ) {
			$subject_ids = implode( ',', $referencing_post_ids );

			$query = "
				SELECT
					object_id AS ID,
					count( object_id ) AS counter
				FROM $table_name
				WHERE
					subject_id IN ($subject_ids)
					AND object_id != ($current_post_id)
				GROUP BY object_id
				LIMIT $limit;
			";

			wl_write_log( "Going to find related entities for the current post [ post ID :: $current_post_id ] [ query :: $query ]" );

			$entities = $wpdb->get_results( $query, OBJECT ); // No cache ok.

			wl_write_log( 'Entities found ' . count( $entities ) );

			foreach ( $entities as $obj ) {

				$entity = get_post( $obj->ID );

				// Ensure only valid and published entities are returned.
				if ( ( null !== $entity ) && ( 'publish' === $entity->post_status ) ) {

					$serialized_entity              = wl_serialize_entity( $entity );
					$serialized_entity['counter']   = $obj->counter;
					$serialized_entity['createdAt'] = $entity->post_date;

					$results[] = $serialized_entity;
				}
			}
		}
	}

	return $results;

}

/**
 * Ajax call for the faceted search widget
 */
function wl_shortcode_faceted_search_ajax( $http_raw_data = null ) {

	$results = wl_shortcode_faceted_search_data( $http_raw_data );
	wl_core_send_json( $results );

}

add_action( 'wp_ajax_wl_faceted_search', 'wl_shortcode_faceted_search_ajax' );
add_action( 'wp_ajax_nopriv_wl_faceted_search', 'wl_shortcode_faceted_search_ajax' );

/**
 * wp-json call for the faceted search widget
 */
function wl_shortcode_faceted_search_wp_json( $http_raw_data = null ) {

	$results = wl_shortcode_faceted_search_data( $http_raw_data );
	if ( ob_get_contents() ) {
		ob_clean();
	}
	return array(
		'items' => array( 
			array('values' => $results) 
		)
	);

}

add_action( 'rest_api_init', function () {
	register_rest_route( WL_REST_ROUTE_DEFAULT_NAMESPACE, '/faceted-search', array(
	  'methods' => 'GET',
	  'callback' => 'wl_shortcode_faceted_search_wp_json',
	) );
  } );
