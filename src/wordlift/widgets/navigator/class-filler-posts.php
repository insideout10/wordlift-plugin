<?php

namespace Wordlift\Widgets\Navigator;
/**
 * @since 3.28.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Filler_Posts {

	static function get_filler_posts( $filler_count, $current_post_id, $referencing_post_ids, $post_types = array() ) {

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

			if ( $post_types ) {
				$args['post_type'] = $post_types;
			}
			$filler_posts = get_posts( $args );
		}

		$filler_count = $filler_count - count( $filler_posts );

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
				'post__not_in'        => array_merge( array( $current_post_id ), $filler_post_ids, $referencing_post_ids ),
				'ignore_sticky_posts' => 1
			);
			if ( $post_types ) {
				$args['post_type'] = $post_types;
			}
			$filler_posts = array_merge( $filler_posts, get_posts( $args ) );


		}

		// Add thumbnail and permalink to filler posts
		$filler_response = array();
		foreach ( $filler_posts as $post_obj ) {
			$thumbnail         = get_the_post_thumbnail_url( $post_obj, 'medium' );
			$filler_response[] = array(
				'post'   => array(
					'id'        => $post_obj->ID,
					'permalink' => get_permalink( $post_obj->ID ),
					'thumbnail' => ( $thumbnail ) ? $thumbnail : WL_DEFAULT_THUMBNAIL_PATH,
					'title'     => get_the_title( $post_obj->ID )
				),
				'entity' => array(
					'id' => 0
				)
			);
		}

		return $filler_response;

	}


}
