<?php

namespace Wordlift\Widgets\Navigator\Filler_Posts;

/**
 * @since 3.27.8
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Same_Post_Type_Filler_Posts extends Filler_Posts {

	public function get_posts( $filler_count, $post_ids_to_be_excluded ) {

		$post_type = $this->alternate_post_type ? $this->alternate_post_type : get_post_type( $this->post_id );

		if ( 'entity' === $post_type ) {
			$post_type = 'post';
		}

		return get_posts(
			array( 'post_type' => $post_type )
						  + $this->get_posts_config( $filler_count, $post_ids_to_be_excluded )
		);
	}
}
