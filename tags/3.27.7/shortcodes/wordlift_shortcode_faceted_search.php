<?php
/**
 * This file provides functions for ajax and wp-json calls
 * required by the shortcode `wl_faceted_search`.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/shortcodes
 */

use Wordlift\Cache\Ttl_Cache;

/**
 * Ajax call for the faceted search widget.
 *
 * @since 3.21.4 fix the cache key by also using the request body.
 * @since 3.21.2 add a caching layer.
 */
function wl_shortcode_faceted_search( $request ) {

	// Create the cache key.
	$cache_key = array(
		'request_params' => $_GET,
	);

	// Create the TTL cache and try to get the results.
	$cache         = new Ttl_Cache( "faceted-search", 8 * 60 * 60 ); // 8 hours.
	$cache_results = $cache->get( $cache_key );

	if ( isset( $cache_results ) ) {
		header( 'X-WordLift-Cache: HIT' );
		wl_core_send_json( $cache_results );

		return;
	}

	header( 'X-WordLift-Cache: MISS' );

	$results = wl_shortcode_faceted_search_origin( $request );

	// Put the result before sending the json to the client, since sending the json will terminate us.
	$cache->put( $cache_key, $results );

	wl_core_send_json( $results );

}

/**
 * Function in charge of fetching data for [wl-faceted-search].
 *
 * @return array $results
 * @since        3.26.0
 */
function wl_shortcode_faceted_search_origin( $request ) {
	// Post ID must be defined.
	if ( ! isset( $_GET['post_id'] ) ) { // WPCS: input var ok; CSRF ok.
		wp_die( 'No post_id given' );

		return;
	}

	$current_post_id = $_GET['post_id']; // WPCS: input var ok; CSRF ok.
	$current_post    = get_post( $current_post_id );
	$faceted_id      = $_GET['uniqid'];

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
		$entity_service->get_related_entities( $current_post->ID );

	// If there are no entities we cannot render the widget.
	if ( 0 === count( $entity_ids ) ) {
		/**
		 * If this function is not called from ajax
		 * then this should not throw an error.
		 * Note: Used in scripbox longtail project on json endpoint.
		 */
		if ( apply_filters( 'wp_doing_ajax', defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			wp_die( 'No entities available' );
		}

	}

	$limit = ( isset( $_GET['limit'] ) ) ? (int) $_GET['limit'] : 4;  // WPCS: input var ok; CSRF ok.
	$amp   = ( isset( $_GET['amp'] ) ) ? true : false;


	/**
	 * see https://github.com/insideout10/wordlift-plugin/issues/1181
	 * The ordering should be descending by date on default.
	 */
	$order_by = 'DESC';
	if ( isset( $_GET['sort'] ) && is_string( $_GET['sort'] ) ) {
		$order_by = (string) $_GET['sort'];
	}

	$referencing_posts = Wordlift_Relation_Service::get_instance()->get_article_subjects(
		$entity_ids,
		'*',
		null,
		'publish',
		array( $current_post_id ),
		$limit,
		null,
		$order_by
	);

	$referencing_post_ids = array_map( function ( $p ) {
		return $p->ID;
	}, $referencing_posts );

	$post_results   = array();
	$entity_results = array();

	// Populate $post_results

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
			$post_obj->permalink = get_permalink( $post_obj->ID );

			$result         = $post_obj;
			$post_results[] = $result;
		}
	}

	// Add filler posts if needed

	$filler_count         = $limit - count( $post_results );
	$filler_posts         = wl_shortcode_faceted_search_filler_posts( $filler_count, $current_post_id, $referencing_post_ids );
	$post_results         = array_merge( $post_results, $filler_posts );
	$referencing_post_ids = array_map( function ( $post ) {
		return $post->ID;
	}, $post_results );

	// Populate $entity_results

	global $wpdb;

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

				$serialized_entity                    = wl_serialize_entity( $entity );
				$serialized_entity['label']           = wl_shortcode_faceted_search_get_the_title( $obj->ID );
				$serialized_entity['counter']         = $obj->counter;
				$serialized_entity['createdAt']       = $entity->post_date;
				$serialized_entity['referencedPosts'] = Wordlift_Relation_Service::get_instance()->get_article_subjects(
					$obj->ID,
					'ids',
					null,
					null,
					array(),
					null,
					$referencing_post_ids
				);
				$entity_results[]                     = $serialized_entity;
			}
		}
	}

	$post_results   = apply_filters( 'wl_faceted_data_posts', $post_results, $faceted_id );
	$entity_results = apply_filters( 'wl_faceted_data_entities', $entity_results, $faceted_id );

	return array(
		'posts'    => $amp ? array( array( 'values' => $post_results ) ) : $post_results,
		'entities' => $entity_results
	);

}

function wl_shortcode_faceted_search_get_the_title( $post_id ) {

	$title = get_the_title( $post_id );

	if ( get_post_type( $post_id ) !== Wordlift_Entity_Service::TYPE_NAME ) {
		$alternative_labels = Wordlift_Entity_Service::get_instance()->get_alternative_labels( $post_id );

		if ( count( $alternative_labels ) > 0 ) {
			$title = $alternative_labels[0];
		}
	}

	return remove_accents( $title );

}

function wl_shortcode_faceted_search_filler_posts( $filler_count, $current_post_id, $referencing_post_ids ) {

	$filler_posts = array();

	// First add latest posts from same categories as the current post
	if ( $filler_count > 0 ) {

		$current_post_categories = wp_get_post_categories( $current_post_id );

		$args = array(
			'meta_query'          => array(
				array(
					'key' => '_thumbnail_id'
				)
			),
			'category__in'        => $current_post_categories,
			'numberposts'         => $filler_count,
			'post__not_in'        => array_merge( array( $current_post_id ), $referencing_post_ids ),
			'ignore_sticky_posts' => 1
		);

		$filler_posts = get_posts( $args );
	}

	$filler_count    = $filler_count - count( $filler_posts );
	$filler_post_ids = array_map( function ( $post ) {
		return $post->ID;
	}, $filler_posts );

	// If that does not fill, add latest posts irrespective of category
	if ( $filler_count > 0 ) {

		$args = array(
			'meta_query'          => array(
				array(
					'key' => '_thumbnail_id'
				)
			),
			'numberposts'         => $filler_count,
			'post__not_in'        => array_merge( array( $current_post_id ), $referencing_post_ids, $filler_post_ids ),
			'ignore_sticky_posts' => 1
		);

		$filler_posts = array_merge( $filler_posts, get_posts( $args ) );

	}

	// Add thumbnail and permalink to filler posts
	foreach ( $filler_posts as $post_obj ) {
		$thumbnail           = get_the_post_thumbnail_url( $post_obj, 'medium' );
		$post_obj->thumbnail = ( $thumbnail ) ?
			$thumbnail : WL_DEFAULT_THUMBNAIL_PATH;
		$post_obj->permalink = get_permalink( $post_obj->ID );
	}

	return $filler_posts;

}

/**
 * Adding `rest_api_init` action for network faceted-search
 */
add_action( 'rest_api_init', function () {
	register_rest_route( WL_REST_ROUTE_DEFAULT_NAMESPACE, '/faceted-search', array(
		'methods'             => 'GET',
		'callback'            => 'wl_shortcode_faceted_search',
		'permission_callback' => '__return_true',
	) );
} );
