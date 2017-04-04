<?php
/**
 * Ajax Adapters: Navigator Ajax Adapter.
 *
 * An adapter to connect AJAX actions to the {@link Wordlift_Navigator_Service}.
 *
 * @since   3.12.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Navigator_Ajax_Adapter} class.
 *
 * @since   3.12.0
 * @package Wordlift
 */
class Wordlift_Navigator_Ajax_Adapter {

	/**
	 * The {@link Wordlift_Navigator_Service} instance.
	 *
	 * @since  3.12.0
	 * @access private
	 * @var \Wordlift_Navigator_Service $navigator_service The {@link Wordlift_Navigator_Service} instance.
	 */
	private $navigator_service;

	/**
	 * Create a {@link Wordlift_Navigator_Ajax_Adapter} instance.
	 *
	 * @since 3.12.0
	 *
	 * @param \Wordlift_Navigator_Service $navigator_service The {@link Wordlift_Navigator_Service} instance.
	 */
	function __construct( $navigator_service ) {

		$this->navigator_service = $navigator_service;
	}

	/**
	 * The `wl_navigator_get` AJAX action.
	 *
	 * @since 3.12.0
	 */
	function get() {

		// Clean any potential corrupting buffer.
		ob_clean();

		// Check that we have a post id.
		if ( ! isset( $_POST['post_id'] ) ) {
			wp_send_json_error( '`post_id` is a required parameter.' );
		}

		// Check that the post id is numeric.
		if ( ! is_numeric( $_POST['post_id'] ) ) {
			wp_send_json_error( '`post_id` must be numeric.' );
		}

		// Get the post id.
		$post_id = (int) $_POST['post_id'];

		// Return the related posts/entities.
		wp_send_json_success( $this->navigator_service->get( $post_id ) );

	}

}