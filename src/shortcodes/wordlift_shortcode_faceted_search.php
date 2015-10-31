<?php

/*
 * Function in charge of diplaying the [wl-faceted-search]
 */
function wl_shortcode_faceted_search( $atts ) {

	$div_id = 'wordlift-faceted-entity-search-widget';

	wp_enqueue_style( 'wordlift-faceted-search', plugin_dir_url( __FILE__ ) . 'css/wordlift-faceted-entity-search-widget.min.css' );

	wp_enqueue_script( 'angularjs', plugin_dir_url( __FILE__ ) . 'bower_components/angular/angular.min.js' );

	wp_enqueue_script( 'wordlift-faceted-search', plugin_dir_url( __FILE__ ) . 'js/wordlift-faceted-entity-search-widget.min.js' );

	wp_localize_script( 'wordlift-faceted-search', 'wl_faceted_search_params', array(
			'ajax_url'             => admin_url( 'admin-ajax.php' ),
			'action'               => 'wl_faceted_search',
			'entity_id'            => get_the_ID(),
			'entity_uri'           => wl_get_entity_uri( get_the_ID() ),
			'div_id'               => $div_id,
			'defaultThumbnailPath' => WL_DEFAULT_THUMBNAIL_PATH
		)
	);

	return '<div id="' . $div_id . '" style="width:100%"></div>';
}

add_shortcode( 'wl_faceted_search', 'wl_shortcode_faceted_search' );


/*
 * Ajax call for the faceted search widget
 */
function wl_shortcode_faceted_search_ajax( $http_raw_data = null ) {
	// Entity ID must be defined
	if ( ! isset( $_GET['entity_id'] ) ) {
		wp_die( 'No entity_id given' );

		return;
	}

	$entity_id = $_GET['entity_id'];

	// If the current post is not an entity post an exception needs to be raised
	$entity = get_post( $entity_id );
	if ( WL_ENTITY_TYPE_NAME !== $entity->post_type ) {
		wp_die( 'Faceted search supports only entity posts' );

		return;
	}

	// Which type was requested?
	if ( isset( $_GET['type'] ) ) {
		$required_type = $_GET['type'];
	} else {
		$required_type = null;
	}

	// Extract filtering conditions
	$filtering_entity_uris = ( null == $http_raw_data ) ? file_get_contents( "php://input" ) : $http_raw_data;
	$filtering_entity_uris = json_decode( $filtering_entity_uris );

	// Set up data structures
	$referencing_post_ids = wl_core_get_related_post_ids( $entity_id, array( 'status' => 'publish' ) );
	$results              = array();

	if ( 'posts' == $required_type ) {
		// Required filtered posts.
		wl_write_log( "Going to find related posts for the current entity [ entity ID :: $entity_id ]" );

		if ( empty( $filtering_entity_uris ) ) {

			// No filter, just get referencing posts
			foreach ( $referencing_post_ids as $post_obj_id ) {
				$post_obj            = get_post( $post_obj_id );
				$thumbnail           = wp_get_attachment_url( get_post_thumbnail_id( $post_obj->ID, 'thumbnail' ) );
				$post_obj->thumbnail = ( $thumbnail ) ?
					$thumbnail : WL_DEFAULT_THUMBNAIL_PATH;
				$post_obj->permalink = get_post_permalink( $post_obj->ID );

				$results[] = $post_obj;
			}
		} else {

			$filtering_entity_ids = wl_get_entity_post_ids_by_uris( $filtering_entity_uris );

			// Search posts that reference all the filtering entities.
			$filtered_posts = wl_core_get_posts( array(
				'get'            => 'posts',
				'post__in'       => $referencing_post_ids,
				'related_to__in' => $filtering_entity_ids,
				'post_type'      => 'post',
				'as'             => 'subject',
			) );

			foreach ( $filtered_posts as $post_obj ) {

				$thumbnail           = wp_get_attachment_url( get_post_thumbnail_id( $post_obj->ID, 'thumbnail' ) );
				$post_obj->thumbnail = ( $thumbnail ) ?
					$thumbnail : WL_DEFAULT_THUMBNAIL_PATH;
				$post_obj->permalink = get_post_permalink( $post_obj->ID );

				$results[] = $post_obj;
			}

			$results = $filtered_posts;
		}

	} else {

		global $wpdb;

		wl_write_log( "Going to find related entities for the current entity [ entity ID :: $entity_id ]" );

		// Retrieve Wordlift relation instances table name
		$table_name = wl_core_get_relation_instances_table_name();

		$ids = implode( ',', $referencing_post_ids );

		// TODO - if an entity is related with different predicates each predicate impacts on counter
		$query = <<<EOF
            SELECT object_id as ID, count( object_id ) as counter 
            FROM $table_name 
            WHERE subject_id IN ($ids) and object_id != $entity_id
            GROUP BY object_id;
EOF;
		wl_write_log( "Going to find related entities for the current entity [ entity ID :: $entity_id ] [ query :: $query ]" );

		$entities = $wpdb->get_results( $query, OBJECT );

		wl_write_log( "Entities found " . count( $entities ) );

		foreach ( $entities as $obj ) {

			$entity            = get_post( $obj->ID );
			$entity            = wl_serialize_entity( $entity );
			$entity['counter'] = $obj->counter;
			$results[]         = $entity;

		}

	}

	wl_core_send_json( $results );

}

add_action( 'wp_ajax_wl_faceted_search', 'wl_shortcode_faceted_search_ajax' );
add_action( 'wp_ajax_nopriv_wl_faceted_search', 'wl_shortcode_faceted_search_ajax' );

