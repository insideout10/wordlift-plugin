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
	 * The desired excerpt length.
	 *
	 * @since  3.12.0
	 * @access private
	 * @var int|null $length The desired excerpt length. If null the length won't be changed.
	 */
	private $length;

	/**
	 * The desired more string.
	 *
	 * @since  3.12.0
	 * @access private
	 * @var string|null $length The desired more string. If null the length won't be changed.
	 */
	private $more;

	/**
	 * Create a {@link Wordlift_Post_Excerpt_Helper} to tweak the excerpt length and `more`.
	 *
	 * WordPress uses filters to change the excerpt parameters, a {@link Wordlift_Post_Excerpt_Helper}
	 * instance hooks to the filters to alter the parameters.
	 *
	 * @param int|null    $length The desired excerpt length. If null the length won't be changed.
	 * @param string|null $more   The desired more string. If null the length won't be changed.
	 */
	function __construct( $length, $more ) {

		$this->length = $length;
		$this->more   = $more;

	}

	/**
	 * Get the excerpt for the provided {@link WP_Post}.
	 *
	 * @since 3.10.0
	 *
	 * @param WP_Post     $post   The {@link WP_Post}.
	 * @param int|null    $length The desired excerpt length, or null to get the default.
	 * @param string|null $more   The desired more string, or null to get the default.
	 *
	 * @return string The excerpt.
	 */
	public static function get_excerpt( $post, $length = null, $more = null ) {

		// Temporary pop the previous post.
		$original = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;

		// Setup our own post.
		setup_postdata( $GLOBALS['post'] = $post );

		// Crete a helper instance which can tweak the excerpt length and `more`.
		$helper = new Wordlift_Post_Excerpt_Helper( $length, $more );
		self::add_filters( $helper );

		$excerpt = get_the_excerpt();

		self::remove_filters( $helper );

		// Restore the previous post.
		if ( null !== $original ) {
			setup_postdata( $GLOBALS['post'] = $original );
		}

		// Finally return the excerpt.
		return html_entity_decode( $excerpt );
	}

	/**
	 * Add filters to tweak the excerpt length and more.
	 *
	 * @since 3.12.0
	 *
	 * @param \Wordlift_Post_Excerpt_Helper $helper A {@link Wordlift_Post_Excerpt_Helper} instance.
	 */
	private static function add_filters( $helper ) {

		add_filter( 'excerpt_length', array(
			$helper,
			'excerpt_length',
		), PHP_INT_MAX, 1 );

		add_filter( 'excerpt_more', array(
			$helper,
			'excerpt_more',
		), PHP_INT_MAX, 1 );

	}

	/**
	 * Remove filters to tweak the excerpt length and more.
	 *
	 * @since 3.12.0
	 *
	 * @param \Wordlift_Post_Excerpt_Helper $helper A {@link Wordlift_Post_Excerpt_Helper} instance.
	 */
	private static function remove_filters( $helper ) {

		remove_filter( 'excerpt_length', array(
			$helper,
			'excerpt_length',
		), PHP_INT_MAX );

		remove_filter( 'excerpt_more', array(
			$helper,
			'excerpt_more',
		), PHP_INT_MAX );

	}

	/**
	 * Called by the `excerpt_length` filter.
	 *
	 * @since 3.12.0
	 *
	 * @param int $value The existing value.
	 *
	 * @return int The existing value or our tweaked value.
	 */
	public function excerpt_length( $value ) {

		return isset( $this->length ) ? $this->length : $value;
	}

	/**
	 * Called by the `excerpt_more` filter.
	 *
	 * @since 3.12.0
	 *
	 * @param string $value The existing value.
	 *
	 * @return string The existing value or our tweaked value.
	 */
	public function excerpt_more( $value ) {

		return isset( $this->more ) ? $this->more : $value;
	}

}
