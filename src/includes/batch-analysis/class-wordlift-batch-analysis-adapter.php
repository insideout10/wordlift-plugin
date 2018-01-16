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
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.17.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * The {@link Wordlift_Batch_Analysis_Service} instance.
	 *
	 * @since  3.17.0
	 * @access private
	 * @var \Wordlift_Batch_Analysis_Service $batch_analysis_service The {@link Wordlift_Batch_Analysis_Service} instance.
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

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		$this->batch_analysis_service = $batch_analysis_service;

		add_action( 'wp_ajax_wl_batch_analysis_complete', array(
			$this,
			'complete',
		) );

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

		$this->log->trace( 'Received Batch Analysis request for posts...' );

		if ( empty( $_REQUEST['post'] ) ) {
			$this->log->error( 'Batch Analysis request for posts missing the post(s) id.' );

			wp_send_json_error( 'The `post` parameter is required.' );
		}

		// Get the parameters from the $_REQUEST.
		$params = self::create_params_from_request();

		// Submit the request.
		$count = $this->batch_analysis_service->submit_posts( $params );

		// Clear any buffer.
		ob_clean();

		// Send the response.
		wp_send_json_success( array( 'count' => $count ) );

	}

	public function complete() {

		$this->batch_analysis_service->complete();

		wp_send_json_success();

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
			// Get the `links` parameter, or use `default` if not provided.
			'links' => isset( $_REQUEST['links'] ) ? $_REQUEST['links'] : 'default',
			// If `include_annotated` is set to `yes`, the set the parameter to true.
			'include_annotated' => isset( $_REQUEST['include_annotated'] ) && 'yes' === $_REQUEST['include_annotated'],
			// Set the minimum amount of occurrences, use `1` by default.
			'min_occurrences' => isset( $_REQUEST['min_occurrences'] ) && is_numeric( $_REQUEST['min_occurrences'] ) ? intval( $_REQUEST['min_occurrences'] ) : 1,
			// Set the `post_type` to `post` if none provided.
			'post_type' => isset( $_REQUEST['post_type'] ) ? (array) $_REQUEST['post_type'] : 'post',
			// Set the exclude array.
			'exclude' => isset( $_REQUEST['exclude'] ) ? (array) $_REQUEST['exclude'] : array(),
			// Set the `from` date, or null if not provided.
			'from' => isset( $_REQUEST['from'] ) ? $_REQUEST['from'] : null,
			// Set the `to` date, or null if not provided.
			'to' => isset( $_REQUEST['to'] ) ? $_REQUEST['to'] : null,
			//
			'ids' => isset( $_REQUEST['post'] ) ? wp_parse_id_list( (array) $_REQUEST['post'] ) : array(),
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
