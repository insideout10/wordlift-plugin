<?php

namespace Wordlift\Widgets\Navigator;
/**
 * @since 3.27.8
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Filler_Posts {

	/**
	 * @var int
	 */
	private $filler_count;

	/**
	 * @var array
	 */
	private $referencing_post_ids;
	/**
	 * @var array
	 */
	private $post_types;
	/**
	 * @var int
	 */
	private $current_post_id;

	/**
	 * Filler_Posts constructor.
	 *
	 * @param $current_post_id int
	 * @param $filler_count int
	 * @param $referencing_post_ids array<int>
	 * @param $post_types array<string>
	 */
	public function __construct( $current_post_id, $filler_count, $referencing_post_ids, $post_types ) {
		
		$this->current_post_id = $current_post_id;

		$this->filler_count = $filler_count;

		$this->referencing_post_ids = $referencing_post_ids;

		$this->post_types = $post_types;


	}


	static function get_filler_posts( $filler_count, $current_post_id, $referencing_post_ids, $post_types = array() ) {

		$filler_posts = array();


		// First check if there are any filler posts for current post type.


		// First add latest posts from same categories as the current post
		if ( $filler_count > 0 ) {

			$filler_posts = self::get_filler_posts_by_same_category( $current_post_id, $filler_count, $referencing_post_ids, $post_types );
		}

		$filler_count = $filler_count - count( $filler_posts );

		$filler_post_ids = array_map( function ( $post ) {
			return $post->ID;
		}, $filler_posts );

		// If that does not fill, add latest posts irrespective of category
		if ( $filler_count > 0 ) {

			$filler_posts = self::get_filler_posts_from_different_categories( $filler_count, $current_post_id, $filler_post_ids, $referencing_post_ids, $post_types, $filler_posts );

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

	/**
	 * @param $current_post_id
	 * @param $filler_count
	 * @param $referencing_post_ids
	 * @param array $post_types
	 *
	 * @return int[]|\WP_Post[]
	 */
	private static function get_filler_posts_by_same_category( $current_post_id, $filler_count, $referencing_post_ids, $post_types ) {
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

		return get_posts( $args );
	}

	/**
	 * @param $filler_count
	 * @param $current_post_id
	 * @param $filler_post_ids
	 * @param $referencing_post_ids
	 * @param array $post_types
	 * @param $filler_posts
	 *
	 * @return array
	 */
	private static function get_filler_posts_from_different_categories( $filler_count, $current_post_id, $filler_post_ids, $referencing_post_ids, $post_types, $filler_posts ) {
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

		return $filler_posts;
	}



}
