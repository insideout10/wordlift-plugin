<?php
/**
 * Ajax: Related Posts.
 *
 * @since   3.0.0
 * @package Wordlift
 * @package Wordlift/admin
 */

/**
 * Get the related posts.
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

	Wordlift_Log_Service::get_logger( 'wordlift_ajax_related_posts' )->trace( "Going to find posts related to current with post id: $post_id ..." );

	// Extract filtering conditions
	$filtering_entity_uris = ( null == $http_raw_data ) ? file_get_contents( "php://input" ) : $http_raw_data;
	$filtering_entity_uris = json_decode( $filtering_entity_uris );

	$filtering_entity_ids = wl_get_entity_post_ids_by_uris( $filtering_entity_uris );
	$related_posts        = array();


	/**
	 * @see https://github.com/insideout10/wordlift-plugin/issues/1312
	 * The entity check should be done on post type, not on the entity type taxonomy
	 */
	if ( get_post_type( $post_id ) === Wordlift_Entity_Service::TYPE_NAME ) {
		$filtering_entity_ids = array( $post_id );
	}

	if ( ! empty( $filtering_entity_ids ) ) {

		$related_posts = Wordlift_Relation_Service::get_instance()
		                                          ->get_article_subjects( $filtering_entity_ids, '*', null, 'publish', array( $post_id ), 5 );

		foreach ( $related_posts as $post_obj ) {

			/**
			 * Use the thumbnail.
			 *
			 * @see https://github.com/insideout10/wordlift-plugin/issues/825 related issue.
			 * @see https://github.com/insideout10/wordlift-plugin/issues/837
			 *
			 * @since 3.19.3 We're using the medium size image.
			 */
			$thumbnail           = get_the_post_thumbnail_url( $post_obj, 'medium' );
			$post_obj->thumbnail = ( $thumbnail ) ? $thumbnail : WL_DEFAULT_THUMBNAIL_PATH;
			$post_obj->link      = get_edit_post_link( $post_obj->ID, 'none' );
			$post_obj->permalink = get_post_permalink( $post_obj->ID );
		}
	}

	wl_core_send_json( $related_posts );

}

add_action( 'wp_ajax_wordlift_related_posts', 'wordlift_ajax_related_posts' );
