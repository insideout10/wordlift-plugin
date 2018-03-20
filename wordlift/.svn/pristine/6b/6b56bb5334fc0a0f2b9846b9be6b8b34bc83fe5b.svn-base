<?php

/**
 *
 * @since 3.0.0
 *
 * @param null $http_raw_data
 */
function wordlift_ajax_related_posts( $http_raw_data = null ) {

	// Extract filtering conditions
	if ( ! isset( $_GET["post_id"] ) || ! is_numeric( $_GET["post_id"] ) ) {
		wp_die( 'Post id missing or invalid!' );

		return;
	}

	// Get the current post
	$post_id = $_GET["post_id"];
	$post    = get_post( $post_id );

	wl_write_log( "Going to find posts related to current with post id: $post_id ..." );

	// Extract filtering conditions
	$filtering_entity_uris = ( null == $http_raw_data ) ? file_get_contents( "php://input" ) : $http_raw_data;
	$filtering_entity_uris = json_decode( $filtering_entity_uris );

	$filtering_entity_ids = wl_get_entity_post_ids_by_uris( $filtering_entity_uris );
	$related_posts        = array();

	// If the current post is an entity, related posts to the current entity are
	// returned.
	if ( Wordlift_Entity_Service::TYPE_NAME === $post->post_type ) {
		$filtering_entity_ids = array( $post_id );
	}

	if ( ! empty( $filtering_entity_ids ) ) {

		$related_posts = wl_core_get_posts( array(
			'get'            => 'posts',
			'related_to__in' => $filtering_entity_ids,
			'post__not_in'   => array( $post_id ),
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'as'             => 'subject',
			// Return 10 results top.
			//
			// See https://github.com/insideout10/wordlift-plugin/issues/426.
			'first'          => 5,
		) );

		foreach ( $related_posts as $post_obj ) {

			$thumbnail           = wp_get_attachment_url( get_post_thumbnail_id( $post_obj->ID, 'thumbnail' ) );
			$post_obj->thumbnail = ( $thumbnail ) ? $thumbnail : WL_DEFAULT_THUMBNAIL_PATH;
			$post_obj->link      = get_edit_post_link( $post_obj->ID, 'none' );
			$post_obj->permalink = get_post_permalink( $post_obj->ID );
		}
	}

	wl_core_send_json( $related_posts );

}

add_action( 'wp_ajax_wordlift_related_posts', 'wordlift_ajax_related_posts' );
