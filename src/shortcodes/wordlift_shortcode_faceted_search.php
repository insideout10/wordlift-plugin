<?php

/**
 * Function in charge of diplaying the [wl-faceted-search]
 */
function wl_shortcode_faceted_search( $atts ) {

	// Extract attributes and set default values.
	$shortcode_atts = shortcode_atts( array(
		'title'          => __( 'Related articles', 'wordlift' ),
		'show_facets'    => true,
		'with_carousel'  => true,
		'squared_thumbs' => false,

	), $atts );

	foreach (
		array(
			'show_facets',
			'with_carousel',
			'squared_thumbs',
		) as $att
	) {

		// See http://wordpress.stackexchange.com/questions/119294/pass-boolean-value-in-shortcode
		$shortcode_atts[ $att ] = filter_var(
			$shortcode_atts[ $att ], FILTER_VALIDATE_BOOLEAN
		);
	}


	// If the current post is not an entity and has no related entities
	// than the shortcode cannot be rendered
	// TODO Add an alert visibile only for connected admin users
	$current_post = get_post();

	$entity_ids = ( Wordlift_Entity_Service::TYPE_NAME === $current_post->post_type ) ?
		$current_post->ID :
		wl_core_get_related_entity_ids( $current_post->ID );

	if ( 0 === count( $entity_ids ) ) {
		return '';
	}

	$div_id = 'wordlift-faceted-entity-search-widget';

	wp_enqueue_style( 'wordlift-faceted-search', dirname( plugin_dir_url( __FILE__ ) ) . '/css/wordlift-faceted-entity-search-widget.min.css' );
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

add_shortcode( 'wl_faceted_search', 'wl_shortcode_faceted_search' );


/*
 * Ajax call for the faceted search widget
 */
function wl_shortcode_faceted_search_ajax( $http_raw_data = null ) {

	// Post ID must be defined
	if ( ! isset( $_GET['post_id'] ) ) {
		wp_die( 'No post_id given' );

		return;
	}

	// Extract filtering conditions
	$filtering_entity_uris = ( null == $http_raw_data ) ? file_get_contents( "php://input" ) : $http_raw_data;
	$filtering_entity_uris = json_decode( $filtering_entity_uris );

	$current_post_id = $_GET['post_id'];
	$current_post    = get_post( $current_post_id );

	// Post ID has to match an existing item
	if ( null === $current_post ) {
		wp_die( 'No valid post_id given' );

		return;
	}

	// If the current post is an entity, 
	// the current post is used as main entity.
	// Otherwise, current post related entities are used. 
	$entity_ids = ( Wordlift_Entity_Service::TYPE_NAME === $current_post->post_type ) ?
		array( $current_post->ID ) :
		wl_core_get_related_entity_ids( $current_post->ID );

	// If there are no entities we cannot render the widget
	if ( 0 === count( $entity_ids ) ) {
		wp_die( 'No entities available' );

		return;
	}

	// Retrieve requested type
	$required_type = ( isset( $_GET['type'] ) ) ? $_GET['type'] : null;

	// Set up data structures            
	$referencing_posts = wl_core_get_posts( array(
		'get'            => 'posts',
		'post__not_in'   => array( $current_post_id ),
		'related_to__in' => $entity_ids,
		'post_type'      => 'post',
		'as'             => 'subject',
		'post_status'    => 'publish',
	) );

	$referencing_post_ids = array_map( function ( $p ) {
		return $p->ID;
	}, $referencing_posts );
	$results              = array();

	if ( 'posts' === $required_type ) {

		// Required filtered posts.
		wl_write_log( "Going to find related posts for the current post [ post ID :: $current_post_id ]" );

		$filtered_posts = ( empty( $filtering_entity_uris ) ) ?
			$referencing_posts :
			wl_core_get_posts( array(
				'get'            => 'posts',
				'post__in'       => $referencing_post_ids,
				'related_to__in' => wl_get_entity_post_ids_by_uris( $filtering_entity_uris ),
				'post_type'      => 'post',
				'as'             => 'subject',
			) );

		if ( $filtered_posts ) {
			foreach ( $filtered_posts as $post_obj ) {

				$thumbnail           = wp_get_attachment_url( get_post_thumbnail_id( $post_obj->ID, 'thumbnail' ) );
				$post_obj->thumbnail = ( $thumbnail ) ?
					$thumbnail : WL_DEFAULT_THUMBNAIL_PATH;
				$post_obj->permalink = get_post_permalink( $post_obj->ID );

				$results[] = $post_obj;
			}
		}
	} else {

		global $wpdb;

		wl_write_log( "Going to find related entities for the current post [ post ID :: $current_post_id ]" );

		// Retrieve Wordlift relation instances table name
		$table_name = wl_core_get_relation_instances_table_name();

		$subject_ids = implode( ',', $referencing_post_ids );

		$query = <<<EOF
            SELECT object_id as ID, count( object_id ) as counter 
            FROM $table_name 
            WHERE subject_id IN ($subject_ids) and object_id != ($current_post_id)
            GROUP BY object_id;
EOF;
		wl_write_log( "Going to find related entities for the current post [ post ID :: $current_post_id ] [ query :: $query ]" );

		$entities = $wpdb->get_results( $query, OBJECT );

		wl_write_log( "Entities found " . count( $entities ) );

		foreach ( $entities as $obj ) {

			$entity = get_post( $obj->ID );
			// Ensure only valid and published entities are returned
			if ( ( null !== $entity ) && ( 'publish' === $entity->post_status ) ) {

				$serialized_entity              = wl_serialize_entity( $entity );
				$serialized_entity['counter']   = $obj->counter;
				$serialized_entity['createdAt'] = $entity->post_date;

				$results[] = $serialized_entity;
			}
		}

	}

	wl_core_send_json( $results );

}

add_action( 'wp_ajax_wl_faceted_search', 'wl_shortcode_faceted_search_ajax' );
add_action( 'wp_ajax_nopriv_wl_faceted_search', 'wl_shortcode_faceted_search_ajax' );

