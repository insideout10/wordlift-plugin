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
use Wordlift\Widgets\Navigator\Filler_Posts\Filler_Posts_Util;
use Wordlift\Widgets\Srcset_Util;

/**
 * Ajax call for the faceted search widget.
 *
 * @since 3.21.4 fix the cache key by also using the request body.
 * @since 3.21.2 add a caching layer.
 */
function wl_shortcode_faceted_search( $request ) {

	// Filter the allowed parameters for caching.
	$cache_params = array_intersect_key(
		$_GET, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		array(
			'post_id'    => 1,
			'post_types' => 1,
			'limit'      => 1,
			'amp'        => 1,
			'sort'       => 1,
		)
	);

	// Create the cache key.
	$cache_key = array(
		'request_params' => $cache_params,
	);

	// Create the TTL cache and try to get the results.
	$cache         = new Ttl_Cache( 'faceted-search', 8 * 60 * 60 ); // 8 hours.
	$cache_results = $cache->get( $cache_key );

	// So that the endpoint can be used remotely
	header( 'Access-Control-Allow-Origin: *' );

	if ( isset( $cache_results ) ) {
		header( 'X-WordLift-Cache: HIT' );
		wl_core_send_json( $cache_results );

		return;
	}

	header( 'X-WordLift-Cache: MISS' );

	$results = wl_shortcode_faceted_search_origin( $request->get_query_params() );

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
// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
function wl_shortcode_faceted_search_origin( $request ) {
	// Post ID must be defined.
	if ( ! isset( $request['post_id'] ) ) { // WPCS: input var ok; CSRF ok.
		wp_die( 'No post_id given' );

		return;
	}

	$current_post_id = (int) $request['post_id']; // WPCS: input var ok; CSRF ok.
	$current_post    = get_post( $current_post_id );
	$faceted_id      = isset( $request['uniqid'] ) ? sanitize_text_field( wp_unslash( (string) $request['uniqid'] ) ) : '';

	$post_types          = isset( $request['post_types'] ) ? sanitize_text_field( wp_unslash( (string) $request['post_types'] ) ) : '';
	$post_types          = explode( ',', $post_types );
	$existing_post_types = get_post_types();
	$post_types          = array_values( array_intersect( $existing_post_types, $post_types ) );

	// Post ID has to match an existing item.
	if ( null === $current_post ) {
		wp_die( 'No valid post_id given' );

		return;
	}

	// If the current post is an entity,
	// the current post is used as main entity.
	// Otherwise, current post related entities are used.
	$entity_service = Wordlift_Entity_Service::get_instance();

	$entity_ids = $entity_service->is_entity( $current_post->ID ) ?
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

	// phpcs:ignore Standard.Category.SniffName.ErrorCode
	$limit = ( isset( $request['limit'] ) ) ? (int) $request['limit'] : 4;
	$amp   = isset( $request['amp'] );

	/**
	 * see https://github.com/insideout10/wordlift-plugin/issues/1181
	 * The ordering should be descending by date on default.
	 */
	$order_by = 'DESC';
	if ( isset( $request['sort'] ) && is_string( $request['sort'] ) ) {
		$order_by = sanitize_sql_orderby( wp_unslash( (string) $request['sort'] ) );
	}

	$referencing_posts = Wordlift_Relation_Service::get_instance()->get_article_subjects(
		$entity_ids,
		'*',
		null,
		'publish',
		array( $current_post_id ),
		$limit,
		null,
		$order_by,
		$post_types
	);

	$referencing_post_ids = array_map(
		function ( $p ) {
			return $p->ID;
		},
		$referencing_posts
	);

	$post_results   = array();
	$entity_results = array();

	// Populate $post_results

	if ( $referencing_posts ) {
		foreach ( $referencing_posts as $post_obj ) {

			/**
			 * Use the thumbnail.
			 *
			 * @see https://github.com/insideout10/wordlift-plugin/issues/825 related issue.
			 * @see https://github.com/insideout10/wordlift-plugin/issues/837
			 *
			 * @since 3.19.3 We're using the medium size image.
			 */
			$thumbnail            = get_the_post_thumbnail_url( $post_obj, 'medium' );
			$post_obj->thumbnail  = ( $thumbnail ) ?
				$thumbnail : WL_DEFAULT_THUMBNAIL_PATH;
			$post_obj->permalink  = get_permalink( $post_obj->ID );
			$post_obj->srcset     = Srcset_Util::get_srcset( $post_obj->ID, Srcset_Util::FACETED_SEARCH_WIDGET );
			$post_obj->post_title = wp_strip_all_tags( html_entity_decode( $post_obj->post_title, ENT_QUOTES, 'UTF-8' ) );
			$result               = $post_obj;
			$post_results[]       = $result;
		}
	}

	// Add filler posts if needed

	$filler_count = $limit - count( $post_results );
	if ( 0 < apply_filters( 'wl_faceted_search__filler_count', $filler_count, $current_post_id, $referencing_post_ids ) ) {
		$filler_posts_util       = new Filler_Posts_Util( $current_post_id );
		$post_ids_to_be_excluded = array_merge( array( $current_post_id ), $referencing_post_ids );
		$filler_posts            = $filler_posts_util->get_filler_posts( $filler_count, $post_ids_to_be_excluded );

		$post_results = array_merge( $post_results, $filler_posts );
	}
	$referencing_post_ids = array_map(
		function ( $post ) {
			return $post->ID;
		},
		$post_results
	);

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

		$entities = $wpdb->get_results( $query, OBJECT ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		wl_write_log( 'Entities found ' . count( $entities ) );

		foreach ( $entities as $obj ) {

			$entity = get_post( $obj->ID );

			// Ensure only valid and published entities are returned.
			if ( ( null !== $entity ) && ( 'publish' === $entity->post_status ) ) {

				$serialized_entity                    = wl_serialize_entity( $entity );
				$serialized_entity['label']           = html_entity_decode( wl_shortcode_faceted_search_get_the_title( $obj->ID ), ENT_QUOTES, 'UTF-8' );
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

	$post_results = apply_filters( 'wl_faceted_data_posts', $post_results, $faceted_id );

	// Add srcset attribute.
	$post_results = array_map(
		function ( $post ) {
			$post->srcset = Srcset_Util::get_srcset( $post->ID, Srcset_Util::FACETED_SEARCH_WIDGET );

			return $post;
		},
		$post_results
	);

	$entity_results = apply_filters( 'wl_faceted_data_entities', $entity_results, $faceted_id );

	return array(
		'posts'    => $amp ? array( array( 'values' => $post_results ) ) : $post_results,
		'entities' => $entity_results,
	);

}

function wl_shortcode_faceted_search_get_the_title( $post_id ) {

	$title = wp_strip_all_tags( get_the_title( $post_id ) );

	if ( get_post_type( $post_id ) !== Wordlift_Entity_Service::TYPE_NAME ) {
		$alternative_labels = Wordlift_Entity_Service::get_instance()->get_alternative_labels( $post_id );

		if ( count( $alternative_labels ) > 0 ) {
			$title = $alternative_labels[0];
		}
	}

	return remove_accents( $title );

}

/**
 * Adding `rest_api_init` action for network faceted-search
 */
add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/faceted-search',
			array(
				'methods'             => 'GET',
				'callback'            => 'wl_shortcode_faceted_search',
				'permission_callback' => '__return_true',
			)
		);
	}
);
