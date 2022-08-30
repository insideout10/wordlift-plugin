<?php
/**
 * Services: ShareThis Service.
 *
 * @since      3.2.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * A service to maintain a compatibility layer with the ShareThis plugin (which
 * only displays itself on pages and posts).
 *
 * @since      3.2.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_ShareThis_Service {

	/**
	 * The ShareThis function which prints the buttons.
	 *
	 * @since 3.2.0
	 */
	const ADD_WIDGET_FUNCTION_NAME = 'st_add_widget';

	/**
	 * The Log service.
	 *
	 * @since  3.2.0
	 * @access private
	 * @var \Wordlift_Log_Service $log_service The Log service.
	 */
	private $log_service;

	/**
	 * Create an instance of the ShareThis service.
	 *
	 * @since 3.2.0
	 */
	public function __construct() {

		$this->log_service = Wordlift_Log_Service::get_logger( 'Wordlift_ShareThis_Service' );

	}

	/**
	 * Receive <em>the_content</em> filter calls from WordPress.
	 *
	 * @since 3.2.0
	 *
	 * @param string $content The post content.
	 *
	 * @return string The updated post content.
	 */
	public function the_content( $content ) {

		return $this->call_sharethis( 'the_content', $content );
	}

	/**
	 * Receive <em>the_excerpt</em> filter calls from WordPress.
	 *
	 * @since 3.2.0
	 *
	 * @param string $content The post excerpt.
	 *
	 * @return string The updated post excerpt.
	 */
	public function the_excerpt( $content ) {

		return $this->call_sharethis( 'the_excerpt', $content );
	}

	/**
	 * Call the ShareThis function.
	 *
	 * @since 3.2.0
	 *
	 * @param string $tag The filter tag.
	 * @param string $content The post content.
	 *
	 * @return string The updated post content.
	 */
	private function call_sharethis( $tag, $content ) {

		// Get the current post.
		global $post;

		// Bail out if the global $post instance isn't set.
		if ( ! isset( $post ) ) {
			return $content;
		}

		// Bail out if the current entity is a post/page since this is already handled by ShareThis.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/819
		if ( 'post' === $post->post_type || 'page' === $post->post_type ) {
			return $content;
		}

		// If it's not the entity type, return.
		$entity_service = Wordlift_Entity_Service::get_instance();
		if ( null === $post || ! $entity_service->is_entity( get_the_ID() ) ) {
			return $content;
		}

		// If the ShareThis function doesn't exist, return.
		if ( ! function_exists( self::ADD_WIDGET_FUNCTION_NAME ) ) {
			return $content;
		}

		// If ShareThis hasn't been added as a filter, return.
		if ( ! has_filter( $tag, self::ADD_WIDGET_FUNCTION_NAME ) ) {
			return $content;
		}

		// Temporary pop the post type and replace it with post.
		$post_type       = $post->post_type;
		$post->post_type = 'post';

		// Call ShareThis (disguised as a post).
		$content = call_user_func_array( self::ADD_WIDGET_FUNCTION_NAME, array( $content ) );

		// Restore our post type.
		$post->post_type = $post_type;

		// Finally return the content.
		return $content;
	}

}
