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
	 * Submit the posts for batch analysis.
	 *
	 * @since 3.14.2
	 */
	public function submit() {

		// Get the parameters from the $_REQUEST.
		$params = self::create_params_from_request();

		// Submit the request.
		$count = $this->batch_analysis_service->submit( $params );

		// Clear any buffer.
		ob_clean();

		// Send the response.
		wp_send_json_success( array( 'count' => $count ) );

	}

	/**
	 * Submit the posts for batch analysis.
	 *
	 * @since 3.14.2
	 */
	public function submit_posts() {

		if ( empty( $_REQUEST['post'] ) ) {
			wp_send_json_error( 'The `post` parameter is required.' );
		}

		$ids = wp_parse_id_list( $_REQUEST['post'] );

		// Submit the request.
		$count = $this->batch_analysis_service->submit_posts( $ids );

		// Clear any buffer.
		ob_clean();

		// Send the response.
		wp_send_json_success( array( 'count' => $count ) );

	}


	/**
	 * A helper function to create the parameters from the $_REQUEST.
	 *
	 * @since 3.17.0
	 *
	 * @return array An array or parameters.
	 */
	private static function create_params_from_request() {

		// Build params array and check if param exists.
		// @codingStandardsIgnoreStart, Ignore phpcs indentation errors.
		$params = array(
			// Get the `link` parameter, or use `default` if not provided.
			'link'              => isset( $_REQUEST['link'] ) ? $_REQUEST['link'] : 'default',
			// If `include_annotated` is set to `yes`, the set the parameter to true.
			'include_annotated' => isset( $_REQUEST['include_annotated'] ) && 'yes' === $_REQUEST['include_annotated'],
			// Set the minimum amount of occurrences, use `1` by default.
			'min_occurrences'   => isset( $_REQUEST['min_occurrences'] ) && is_numeric( $_REQUEST['min_occurrences'] ) ? intval( $_REQUEST['min_occurrences'] ) : 1,
			// Set the `post_type` to `post` if none provided.
			'post_type'         => isset( $_REQUEST['post_type'] ) ? (array) $_REQUEST['post_type'] : 'post',
			// Set the exclude array.
			'exclude'           => isset( $_REQUEST['exclude'] ) ? (array) $_REQUEST['exclude'] : array(),
			// Set the `from` date, or null if not provided.
			'from'              => isset( $_REQUEST['from'] ) ? $_REQUEST['from'] : null,
			// Set the `to` date, or null if not provided.
			'to'                => isset( $_REQUEST['to'] ) ? $_REQUEST['to'] : null,
		);

		// @codingStandardsIgnoreEnd

		return $params;
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

}
