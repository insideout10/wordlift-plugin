<?php

namespace Wordlift\Widgets\Navigator\Filler_Posts;
/**
 * @since 3.27.8
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Filler_Posts_Util {

	/**
	 * @var array<Filler_Posts>
	 */
	private $sources = array();

	public function __construct( $post_id ) {
		$this->sources = array(
			new Same_Category_Filler_Posts( $post_id ),
			new Same_Post_Type_Filler_Posts( $post_id ),
			new Any_Post_Type_Filler_Posts( $post_id )
		);
	}


	/**
	 * @param $posts array<\WP_Post>
	 *
	 * @return array<int>
	 */
	private function extract_post_ids( $posts ) {
		return array_map( function ( $post ) {
			/**
			 * @var $post \WP_Post
			 */
			return $post->ID;
		}, $posts );
	}

	public function get_filler_posts( $filler_count, $post_ids_to_be_excluded ) {

		$filler_posts = array();

		foreach ( $this->sources as $source ) {

			if ( $filler_count <= 0 ) {
				break;
			}
			/**
			 * @var Filler_Posts $source
			 */
			$source->post_ids_to_be_excluded = $post_ids_to_be_excluded;
			$source->filler_count            = $filler_count;

			$posts    = $source->get_posts( $filler_count, $post_ids_to_be_excluded );
			$post_ids = $this->extract_post_ids( $posts );

			// Update the post ids, filler posts and filler count
			$post_ids_to_be_excluded = array_merge( $post_ids_to_be_excluded, $post_ids );
			$filler_count            = $filler_count - count( $posts );
			$filler_posts            = array_merge( $filler_posts, $posts );


		}

		return $filler_posts;

	}

	public function get_filler_response( $filler_count, $post_ids_to_be_excluded ) {
		$filler_posts = $this->get_filler_posts( $filler_count, $post_ids_to_be_excluded );
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
