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
	 * Get the excerpt for the provided {@link WP_Post}.
	 *
	 * Since anyone can hook on the excerpt generation filters, and
	 * amend it with non textual content, we play it self and generate
	 * the excerpt ourselves, mimicking the way wordpress core does it.
	 *
	 * @since 3.10.0
	 *
	 * @param WP_Post     $post   The {@link WP_Post}.
	 * @param int    	  $length The desired excerpt length.
	 * @param string	  $more   The desired more string.
	 *
	 * @return string The excerpt.
	 */
	public static function get_excerpt( $post, $length = 55, $more = '[&hellip;]' ) {

		// Temporary pop the previous post.
		$original = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;

		// Setup our own post.
		setup_postdata( $GLOBALS['post'] = $post );

		if ( '' == $post->post_excerpt ) {
			$text = get_the_content( '' );
			$text = strip_shortcodes( $text );
			$excerpt = wp_trim_words( $text, $length, $more );
			// Adjust html output same way as for the normal excerpt,
			// just force all functions depending on the_excerpt hook.
			$excerpt = shortcode_unautop( wpautop( convert_chars( convert_smilies( wptexturize( $excerpt ) ) ) ) );
		} else {
			$excerpt = shortcode_unautop( wpautop( convert_chars( convert_smilies( wptexturize( $post->post_excerpt ) ) ) ) );
		}

		// Restore the previous post.
		if ( null !== $original ) {
			setup_postdata( $GLOBALS['post'] = $original );
		}

		// Finally return the excerpt.
		return $excerpt;
	}

}
