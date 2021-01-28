<?php

namespace Wordlift\Widgets\Navigator\Filler_Posts;
/**
 * @since 3.28.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Same_Post_Type_Same_Category_Posts extends Filler_Posts {


	function get_posts( $filler_count, $post_ids_to_be_excluded ) {
		$current_post_categories = wp_get_post_categories( $this->post_id );

		return get_posts( array(
			                  'category__in' => $current_post_categories,
			                  'post_type'    => get_post_type( $this->post_id )
		                  )
		                  + $this->get_posts_config( $filler_count, $post_ids_to_be_excluded ) );
	}
}
