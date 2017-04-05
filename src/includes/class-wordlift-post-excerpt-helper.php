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
	 * @since 3.10.0
	 *
	 * @param WP_Post $post The {@link WP_Post}.
	 *
	 * @return string The excerpt.
	 */
	public static function get_excerpt( $post ) {

		// Temporary pop the previous post.
		$original = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;

		// Setup our own post.
		setup_postdata( $GLOBALS['post'] = $post );

		$excerpt = get_the_excerpt();

		// Restore the previous post.
		if ( null !== $original ) {
			setup_postdata( $GLOBALS['post'] = $original );
		}

		// Finally return the excerpt.
		return html_entity_decode( $excerpt );
	}

}
