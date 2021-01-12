<?php

namespace Wordlift\Widgets\Navigator\Filler_Posts;
/**
 * @since 3.27.8
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * Returns all the posts without restricting by category.
 * Class Default_Filler_Posts
 * @package Wordlift\Widgets\Navigator\Filler_Posts
 */
class Any_Post_Type_Filler_Posts extends Filler_Posts {

	function get_posts( $filler_count, $post_ids_to_be_excluded ) {
		$post_types = get_post_types();
		$key        = array_search( 'entity', $post_types );
		if ( $key !== false ) {
			unset( $post_types[ $key ] );
		}

		return get_posts( array( 'post_type' => $post_types )
		                  + $this->get_posts_config( $filler_count, $post_ids_to_be_excluded ) );
	}
}
