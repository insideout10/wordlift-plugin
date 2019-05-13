<?php
/**
 * This file provides functions for ajax and wp-json calls 
 * required by the shortcode `wl_faceted_search`.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/shortcodes
 */

/**
 * Function in charge of fetching data for [wl-faceted-search] in web mode.
 * 
 * @since		3.20.0
 * @return array $results
 */
function wl_shortcode_faceted_search_data_ajax( $http_raw_data = null ) {

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
 * Function in charge of fetching data for [wl-faceted-search] in amp mode.
 * 
 * @since		3.20.0
 * @return array $results
 */
function wl_shortcode_faceted_search_data_wp_json( $http_raw_data = null ) {

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
			foreach ( $filtered_posts as $i => $post_obj ) {

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

				$results[$i] = $post_obj;

				// Get Entity URLs needed for client side filtering in amp
				foreach( Wordlift_Relation_Service::get_instance()->get_objects( $post_obj->ID, 'ids' ) as $entity_id ){
					$results[$i]->entities[] = Wordlift_Entity_Service::get_instance()->get_uri( $entity_id );
				}
			}
		}

		return array( 
			array('values' => $results)
		);

	} else {

		global $wpdb;

		wl_write_log( "Going to find related entities for the current post [ post ID :: $current_post_id ]" );

		// Retrieve Wordlift relation instances table name.
		$table_name = wl_core_get_relation_instances_table_name();

		// Response interface with l10n strings, grouped entities and empty data array
		$serialized_entity_groups = array(
			array(
				'l10n' => $_GET['l10n'] ? $_GET['l10n']['what'] : 'what',
				'entities' => array('thing', 'creative-work', 'recipe'),
				'data' => array()
			),
			array(
				'l10n' => $_GET['l10n'] ? $_GET['l10n']['who'] : 'who',
				'entities' => array('person', 'organization', 'local-business'),
				'data' => array()
			),
			array(
				'l10n' => $_GET['l10n'] ? $_GET['l10n']['where'] : 'where',
				'entities' => array('place'),
				'data' => array()
			),
			array(
				'l10n' => $_GET['l10n'] ? $_GET['l10n']['when'] : 'when',
				'entities' => array('event'),
				'data' => array()
			)
		);

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

					// Populate $serialized_entity_groups maintaining array sequence
					foreach ( $serialized_entity_groups as $seg_key => $seg_value ) {
						if ( in_array( $serialized_entity['mainType'], $seg_value['entities'] ) ) {
							$serialized_entity_groups[$seg_key]['data'][] = $serialized_entity;
						}
					}
				}
			}

			// Clean-up $serialized_entity_groups by removing empty items and entities
			foreach ( $serialized_entity_groups as $seg_key => $seg_value ) {
				if( empty($seg_value['data']) ) {
					unset($serialized_entity_groups[$seg_key]);
				}
				unset($serialized_entity_groups[$seg_key]['entities']);
			}

			$results = $serialized_entity_groups;
		}

		return $results;
	}

}

/**
 * Ajax call for the faceted search widget
 */
function wl_shortcode_faceted_search_ajax( $http_raw_data = null ) {

	$results = wl_shortcode_faceted_search_data_ajax( $http_raw_data );
	wl_core_send_json( $results );

}

/**
 * Adding `wp_ajax` and `wp_ajax_nopriv` action for web backend of faceted-search
 */
add_action( 'wp_ajax_wl_faceted_search', 'wl_shortcode_faceted_search_ajax' );
add_action( 'wp_ajax_nopriv_wl_faceted_search', 'wl_shortcode_faceted_search_ajax' );

/**
 * wp-json call for the faceted search widget
 */
function wl_shortcode_faceted_search_wp_json( $http_raw_data = null ) {

	$results = wl_shortcode_faceted_search_data_wp_json( $http_raw_data );
	if ( ob_get_contents() ) {
		ob_clean();
	}
	return array(
		'items' => $results
	);

}

/**
 * Adding `rest_api_init` action for amp backend of faceted-search
 */
add_action( 'rest_api_init', function () {
	register_rest_route( WL_REST_ROUTE_DEFAULT_NAMESPACE, '/faceted-search', array(
	  'methods' => 'GET',
	  'callback' => 'wl_shortcode_faceted_search_wp_json',
	) );
} );
