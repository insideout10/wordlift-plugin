<?php

/**
 * Services: Post Service
 *
 * @since 3.10.0
 */
class Wordlift_Post_Service {

	/**
	 * Find posts by their URI.
	 *
	 * @since 3.10.0
	 *
	 * @param string $uri The URI.
	 *
	 * @return WP_Post|null A WP_Post instance or null if not found.
	 */
	public function get_by_uri( $uri ) {

		// Check if we've been provided with a value otherwise return null.
		if ( empty( $uri ) ) {
			return null;
		}

		$query_args = array(
			'posts_per_page' => 1,
			'post_status'    => 'any',
			'post_type'      => array( 'post', 'page' ),
			'meta_query'     => array(
				array(
					'key'     => WL_ENTITY_URL_META_NAME,
					'value'   => $uri,
					'compare' => '=',
				),
			),
		);

		$query = new WP_Query( $query_args );

		// Get the matching entity posts.
		$posts = $query->get_posts();

		// Return null if no post is found.
		if ( 0 === count( $posts ) ) {
			return null;
		}

		// Return the found post.
		return $posts[0];
	}


}