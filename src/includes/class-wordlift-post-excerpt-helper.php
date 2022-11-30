<?php
/**
 * Helpers: Post Excerpt Helper.
 *
 * The Post Excerpt Helper provides the `get_excerpt` handy function to get the
 * excerpt for any post.
 *
 * While WordPress' own `get_the_excerpt` does exactly the same only since version
 * 4.5, the function allows to specify a post id.
 *
 * Since we need to maintain compatibility with 4.2+ we need therefore this helper
 * function.
 *
 * @since   3.12.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Post_Excerpt_Helper} class.
 *
 * @since   3.12.0
 * @package Wordlift
 */
class Wordlift_Post_Excerpt_Helper {

	/**
	 * Get the text excerpt for the provided {@link WP_Post}.
	 *
	 * Since anyone can hook on the excerpt generation filters, and
	 * amend it with non textual content, we play it self and generate
	 * the excerpt ourselves, mimicking the way WordPress core does it.
	 *
	 * @since 3.10.0
	 *
	 * @param WP_Post $post The {@link WP_Post}.
	 * @param int     $length The desired excerpt length.
	 * @param string  $more The desired more string.
	 *
	 * @return string The excerpt.
	 */
	public static function get_text_excerpt( $post, $length = 55, $more = '...' ) {

		/*
		 * Apply the `wl_post_content` filter, in case 3rd parties want to change the post content, e.g.
		 * because the content is written elsewhere.
		 *
		 * @since 3.20.0
		 */
		$post_content = apply_filters( 'wl_post_content', $post->post_content, $post );

		// Filter shortcode content in post_content, before using it for trimming
		$post_content = do_shortcode( $post_content );

		// Get the excerpt and trim it. Use the `post_excerpt` if available.
		$excerpt = wp_trim_words( ! empty( $post->post_excerpt ) ? $post->post_excerpt : $post_content, $length, $more );

		// Remove shortcodes and decode html entities.
		return wp_strip_all_tags( html_entity_decode( self::strip_all_shortcodes( $excerpt ) ) );
	}

	/**
	 * Remove all the shortcodes from the content. We're using our own function
	 * because WordPress' own `strip_shortcodes` only takes into consideration
	 * shortcodes for installed plugins/themes.
	 *
	 * @since 3.12.0
	 *
	 * @param string $content The content with shortcodes.
	 *
	 * @return string The content without shortcodes.
	 */
	private static function strip_all_shortcodes( $content ) {

		return preg_replace( '/\[[^]]+\]/', '', $content );
	}

}
