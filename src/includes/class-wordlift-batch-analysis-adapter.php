<?php
/**
 * Adapters: Batch Analysis Adapter.
 *
 * @since      3.14.2
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_Batch_Analysis_Adapter} class.
 *
 * @since      3.14.2
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_Batch_Analysis_Adapter {

	/**
	 * @var Wordlift_Batch_Analysis_Service
	 */
	private $batch_analysis_service;


	/**
	 * Wordlift_Batch_Analysis_Adapter constructor.
	 *
	 * @since 3.14.2
	 *
	 * @param \Wordlift_Batch_Analysis_Service $batch_analysis_service
	 */
	public function __construct( $batch_analysis_service ) {

		$this->batch_analysis_service = $batch_analysis_service;

	}

	/**
	 * Submit all the auto selected posts, i.e. non annotated posts.
	 *
	 * @since 3.14.2
	 */
	public function submit_auto_selected_posts() {

		if ( ! isset( $_REQUEST['link'] ) ) {
			wp_die( 'The `link` parameter is required.' );
		}

		// Check whether the post type was added and sets the analysis post type.
		$this->maybe_set_analysis_post_type();

		$count = $this->batch_analysis_service->submit_auto_selected_posts( $_REQUEST['link'] );

		// Clear any buffer.
		ob_clean();

		// Send the response.
		wp_send_json_success( array( 'count' => $count ) );

	}

	/**
	 * Submit all posts for analysis.
	 *
	 * @since 3.14.5
	 */
	public function submit_all_posts() {

		if ( ! isset( $_REQUEST['link'] ) ) {
			wp_die( 'The `link` parameter is required.' );
		}

		// Check whether the post type was added and sets the analysis post type.
		$this->maybe_set_analysis_post_type();

		$count = $this->batch_analysis_service->submit_all_posts( $_REQUEST['link'] );

		// Clear any buffer.
		ob_clean();

		// Send the response.
		wp_send_json_success( array( 'count' => $count ) );

	}

	/**
	 * Submit the specified post for batch analysis.
	 *
	 * @since 3.14.2
	 */
	public function submit() {

		if ( ! isset( $_REQUEST['link'] ) || ! isset( $_REQUEST['post'] ) ) {
			wp_die( 'The `link` and `post` parameters are required.' );
		}

		// Check whether the post type was added and sets the analysis post type.
		$this->maybe_set_analysis_post_type();

		$count = $this->batch_analysis_service->submit( (array) $_REQUEST['post'], $_REQUEST['link'] );

		// Clear any buffer.
		ob_clean();

		// Send the response.
		wp_send_json_success( array( 'count' => $count ) );

	}

	/**
	 * Cancel the batch analysis for the specified post.
	 *
	 * @since 3.14.0
	 */
	public function cancel() {

		if ( ! isset( $_REQUEST['post'] ) ) {
			wp_die( 'The `post` parameter is required.' );
		}

		$count = $this->batch_analysis_service->cancel( (array) $_REQUEST['post'] );

		// Clear any buffer.
		ob_clean();

		// Send the response.
		wp_send_json_success( array( 'count' => $count ) );

	}

	/**
	 * Clear warnings for the specified post.
	 *
	 * @since 3.14.0
	 */
	public function clear_warning() {

		if ( ! isset( $_REQUEST['post'] ) ) {
			wp_die( 'The `post` parameter is required.' );
		}

		$this->batch_analysis_service->clear_warning( (array) $_REQUEST['post'] );

		// Clear any buffer.
		ob_clean();

		// Send the response.
		wp_send_json_success();

	}

	/**
	 * Set the analysis post type if the param is set in the request
	 *
	 * @since 3.17.0
	 */
	public function maybe_set_analysis_post_type() {
		if ( isset( $_REQUEST['post_type'] ) ) {
			$this->batch_analysis_service->set_post_type( $_REQUEST['post_type'] );
		}
	}

}
