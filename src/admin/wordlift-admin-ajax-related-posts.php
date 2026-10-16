<?php
/**
 * Ajax: Related Posts.
 *
 * @since   3.0.0
 * @package Wordlift/admin
 */

use Wordlift\Content\WordPress\Wordpress_Content_Service;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Get the related posts.
 *
 * @param null $http_raw_data
 *
 * @since 3.0.0
 */
function wordlift_ajax_related_posts( $http_raw_data = null ) {

	// Check user capabilities.
	if ( ! current_user_can( 'edit_posts' ) ) {
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		@ob_clean();
		return wp_send_json_error( __( 'Insufficient permissions.', 'wordlift' ), 403 );
	}

	// Extract filtering conditions
	if ( ! isset( $_GET['post_id'] ) || ! is_numeric( $_GET['post_id'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_die( 'Post id missing or invalid!' );

		return;
	}

	// Get the current post
	$post_id = (int) $_GET['post_id']; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$post    = get_post( $post_id );

	if ( null === $post || ! current_user_can( 'read_post', $post_id ) ) {
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		@ob_clean();
		return wp_send_json_error( __( 'Insufficient permissions.', 'wordlift' ), 403 );
	}

	Wordlift_Log_Service::get_logger( 'wordlift_ajax_related_posts' )->trace( "Going to find posts related to current with post id: $post_id ..." );

	// Extract filtering conditions
	$filtering_entity_uris = empty( $http_raw_data ) ? file_get_contents( 'php://input' ) : $http_raw_data;
	$filtering_entity_uris = json_decode( $filtering_entity_uris );

	// If the current post is an entity, related posts to the current entity are
	// returned.
	$filtering_entity_ids = Wordlift_Entity_Service::get_instance()->is_entity( $post->ID )
		? array( $post_id )
		: wordlift_ajax_related_posts_entity_ids( $filtering_entity_uris );

	$related_posts = array();
	if ( ! empty( $filtering_entity_ids ) ) {
		$related_post_ids = Wordlift_Relation_Service::get_instance()
													->get_article_subjects( $filtering_entity_ids, 'ids', null, 'publish', array( $post_id ), 5 );
		$related_posts    = array_map( 'wordlift_ajax_related_posts_format', $related_post_ids );
	}

	wl_core_send_json( $related_posts );
}

/**
 * Get the post IDs of the given entity URIs.
 *
 * @param array $entity_uris The entity URIs.
 *
 * @return int[] The post IDs.
 */
function wordlift_ajax_related_posts_entity_ids( $entity_uris ) {
	$content_service = Wordpress_Content_Service::get_instance();

	return array_filter(
		array_map(
			function ( $uri ) use ( $content_service ) {
				$content = $content_service->get_by_entity_id( $uri );

				return isset( $content ) && is_a( $content->get_bag(), '\WP_Post' ) ? $content->get_bag()->ID : null;
			},
			(array) $entity_uris
		)
	);
}

/**
 * Get the fields the UI needs for a related post.
 *
 * @param int $related_post_id The related post ID.
 *
 * @return object The related post fields.
 */
function wordlift_ajax_related_posts_format( $related_post_id ) {
	/**
	 * Use the thumbnail.
	 *
	 * @see https://github.com/insideout10/wordlift-plugin/issues/825 related issue.
	 * @see https://github.com/insideout10/wordlift-plugin/issues/837
	 *
	 * @since 3.19.3 We're using the medium size image.
	 */
	$thumbnail = get_the_post_thumbnail_url( $related_post_id, 'medium' );

	return (object) array(
		'ID'         => (int) $related_post_id,
		'post_title' => get_post_field( 'post_title', $related_post_id ),
		'post_type'  => get_post_type( $related_post_id ),
		'thumbnail'  => ( $thumbnail ) ? $thumbnail : WL_DEFAULT_THUMBNAIL_PATH,
		'link'       => get_edit_post_link( $related_post_id, 'none' ),
		'permalink'  => get_post_permalink( $related_post_id ),
	);
}

add_action( 'wp_ajax_wordlift_related_posts', 'wordlift_ajax_related_posts' );
