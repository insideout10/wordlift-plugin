<?php

/**
 * Test the {@link Wordlift_Post_Excerpt_Helper}.
 *
 * @since 3.12.0
 */
class PostExcerptHelperTest extends Wordlift_Unit_Test_Case {

	/**
	 * Test excerpt generation for posts with no manual excerpt.
	 *
	 * @since 3.12.0
	 */
	function test_automatic_excerpt_generation() {

		// Test basic functionality with automatic excerpt generation.

		// test shortcode and tag stripping.
		$post_id = wl_create_post( 'this is <a href="">link</a> and [gallery] shortcode', 'post-1', 'Post 1', 'publish', 'entity' );
		$post = get_post( $post_id );
		$text = Wordlift_Post_Excerpt_Helper::get_text_excerpt( $post );
		$this->assertEquals( 'this is link and  shortcode', $text );

		// Test handling of "more" indication
		$text = Wordlift_Post_Excerpt_Helper::get_text_excerpt( $post, 3 );
		$this->assertEquals( 'this is link...', $text );

		// Test manual excerpt.
		wp_update_post( array( 'ID' => $post_id, 'post_excerpt' => 'Manual excerpt with <img src=""> image' ) );
		$post = get_post( $post_id );
		$text = Wordlift_Post_Excerpt_Helper::get_text_excerpt( $post, 3 );
		$this->assertEquals( 'Manual excerpt with...', $text );
	}

}
