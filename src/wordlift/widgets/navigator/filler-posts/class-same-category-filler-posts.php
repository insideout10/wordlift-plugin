<?php

namespace Wordlift\Widgets\Navigator\Filler_Posts;

/**
 * @since 3.27.8
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * This class returns the filler posts from same category.
 *
 * Class Same_Category_Filler_Posts
 *
 * @package Wordlift\Widgets\Navigator\Filler_Posts
 */
class Same_Category_Filler_Posts extends Filler_Posts {

	public function get_posts( $filler_count, $post_ids_to_be_excluded ) {

		$current_post_categories = wp_get_post_categories( $this->post_id );
		$post_type               = $this->alternate_post_type ? $this->alternate_post_type : get_post_types();

		return get_posts(
			array(
				'category__in' => $current_post_categories,
				'post_type'    => $post_type,
			)
						  + $this->get_posts_config( $filler_count, $post_ids_to_be_excluded )
		);
	}
}
