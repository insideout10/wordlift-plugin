<?php
/**
 * Services: Batch Analysis Service.
 *
 * The Batch Analysis service allows to queue analysis operations given a list
 * of URLs.
 *
 * The Batch Analysis service should also allow to queue all the posts/pages
 * that do not contain any annotation (example annotation:
 * <span id="urn:enhancement-{uuid}"
 *       class="textannotation disambiguated wl-{class} [wl-[no-]link]"
 *       itemid="{itemid-url}">{label}</span>
 *
 * We must identify the batch analysis status according to 3 stages:
 *  1. BATCH_ANALYSIS_SUBMIT, i.e. a post/page has been submitted for batch
 *      analysis.
 *  2. BATCH_ANALYSIS_REQUEST, i.e. a post/page batch analysis has been
 *      requested to the remote service.
 *  3. BATCH_ANALYSIS_SUCCESS / BATCH_ANALYSIS_ERROR: the outcome of the batch
 *      analysis.
 *
 * For each state we record the date time, this is especially useful since the
 * remote service doesn't provide a state management, therefore we need to
 * define a timeout on the client side.
 *
 * Upon reception of the results we need to check whether there are some
 * potential warning due to interpolation issues, i.e.
 *
 *  `\w<span id="urn:enhancement-` or `\s</span>` or `</span>\w`
 *
 * and in such a case, set a warning BATCH_ANALYSIS_WARNING in order to provide
 * a list of posts/pages that need manual review and allow the editor to clear
 * the warning flag.
 *
 * All the time-consuming operations must be executed asynchronously.
 *
 * Since setting the post meta for a large number of posts may be time consuming
 * in PHP, we can use prepared queries.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_Batch_Analysis_Service} class.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_Batch_Analysis_Service {

	/**
	 * The list of states for the Batch Analysis:
	 *  - STATE_META_KEY: the batch analysis state meta key,
	 *  - STATE_SUBMIT: a post/page has been submitted for analysis,
	 *  - STATE_REQUEST: the plugin requested an analysis for the submitted
	 *      post/page,
	 *  - STATE_SUCCESS: the analysis has completed successfully,
	 *  - STATE_ERROR: the analysis returned an error.
	 *
	 * @since 3.14.2
	 */
	const STATE_META_KEY = '_wl_batch_analysis_state';
	const STATE_SUBMIT = 0;
	const STATE_REQUEST = 1;
	//### COMPLETE states.
	const STATE_SUCCESS = 2;
	const STATE_ERROR = 2;

	const SUBMIT_TIMESTAMP_META_KEY = '_wl_batch_analysis_submit_timestamp';
	const REQUEST_TIMESTAMP_META_KEY = '_wl_batch_analysis_request_timestamp';
	const COMPLETE_TIMESTAMP_META_KEY = '_wl_batch_analysis_complete_timestamp';
	const LINK_META_KEY = '_wl_batch_analysis_link';
	const WARNING_META_KEY = '_wl_batch_analysis_warning';

	/**
	 * Option name.
	 *
	 * @since  3.14.0
	 */
	const OPTION_NAME = 'wl_analyze_batch';

	/**
	 * Name of waiting to be processed queue array inside the option.
	 *
	 * @since  3.14.0
	 */
	const ANALYZE_QUEUE = 'queue';

	/**
	 * Name of waiting for response queue array inside the option.
	 *
	 * @since  3.14.0
	 */
	const RESPONSE_QUEUE = 'processing';

	/**
	 * The {@link Wordlift} plugin instance.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift $plugin The {@link Wordlift} plugin instance.
	 */
	private $plugin;

	/**
	 * The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.14.2
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * The {@link Class_Wordlift_Batch_Analys_Service} instance.
	 *
	 * @since 3.14.0
	 *
	 * @param \Wordlift                       $plugin                The {@link Wordlift} plugin instance.
	 * @param \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	public function __construct( $plugin, $configuration_service ) {

		$this->plugin                = $plugin;
		$this->configuration_service = $configuration_service;
		$this->log                   = Wordlift_Log_Service::get_logger( 'Wordlift_Batch_Analysis_Service' );

		add_action( 'wl_batch_analyze', array( $this, 'batch_analyze' ) );
		add_action( 'wp_async_wl_batch_analysis_request', array(
			$this,
			'request',
		) );
		add_action( 'wp_async_wl_batch_analysis_complete', array(
			$this,
			'complete',
		) );

	}

	/**
	 * Submit for analysis all the posts/pages which do not have annotations
	 * and haven't been analyzed before.
	 *
	 * @since 3.14.2
	 *
	 * @param string $link The link setting.
	 *
	 * @return false|int Number of rows affected/selected or false on error.
	 */
	public function submit_auto_selected_posts( $link ) {
		global $wpdb;

		// Submit the posts/pages and return the number of affected results.
		// We're using a SQL query here because we could have potentially
		// thousands of rows.
		$count = $wpdb->query( $wpdb->prepare(
			"
			INSERT INTO $wpdb->postmeta ( post_id, meta_key, meta_value )
			SELECT
			p.ID, metas.*
			FROM (
				SELECT %s, 0 FROM dual
				UNION
				SELECT %s, UTC_TIMESTAMP() FROM dual
				UNION
				SELECT %s, %s FROM dual
			) metas, $wpdb->posts p
			LEFT JOIN $wpdb->postmeta batch_analysis_state
				ON batch_analysis_state.post_id = p.ID
					AND batch_analysis_state.meta_key = %s
			WHERE p.post_type IN ('post', 'page')
				AND p.post_status = 'publish'
				AND batch_analysis_state.meta_value IS NULL
				AND p.post_content NOT REGEXP %s;
			",
			self::STATE_META_KEY,
			self::SUBMIT_TIMESTAMP_META_KEY,
			self::LINK_META_KEY,
			$link,
			self::STATE_META_KEY,
			'<[a-z]+ id="urn:enhancement-[^"]+" class="[^"]+" itemid="[^"]+">'
		) );

		do_action( 'wl_batch_analysis_request' );

		// Divide the count by 3 to get the number of posts/pages queued.
		return $count / 3;
	}

	/**
	 * Request the batch analysis for submitted posts.
	 *
	 * @since 3.14.2
	 */
	public function request() {

		$this->log->debug( "Requesting analysis..." );

		// By default 5 posts of any post type are returned.
		$posts = get_posts( array(
			'fields'     => 'ids',
			'meta_key'   => self::STATE_META_KEY,
			'meta_value' => self::STATE_SUBMIT,
			'orderby'    => 'ID',
		) );

		// Bail out if there are no submitted posts.
		if ( empty( $posts ) ) {
			$this->log->debug( 'No posts to submit found, checking for completed requests...' );

			do_action( 'wl_batch_analysis_complete' );

			return;
		}

		// Send a request for each post.
		foreach ( $posts as $id ) {
			$this->log->debug( "Requesting analysis for post $id..." );

			// Change the state to `REQUEST`.
			$this->set_state( $id, self::STATE_REQUEST );

			// Send the actual request to the remote service.
			$result = $this->do_request( $id );

			$this->log->debug( "Analysis requested for post $id." );

			// Set an error if we received an error.
			if ( is_wp_error( $result ) ) {
				$this->log->error( "Analysis request for post $id returned {$result->get_error_message()}." );

				$this->set_state( $id, self::STATE_ERROR );
			}

		}

		// Call the `wl_batch_analysis_request` action again. This is going
		// to be handled by the async task.
		do_action( 'wl_batch_analysis_request' );

	}

	public function complete() {

		$this->log->debug( "Requesting results..." );

		// By default 5 posts of any post type are returned.
		$posts = get_posts( array(
			'fields'     => 'ids',
			'meta_key'   => self::STATE_META_KEY,
			'meta_value' => self::STATE_REQUEST,
			'orderby'    => 'ID',
		) );

		// Bail out if there are no submitted posts.
		if ( empty( $posts ) ) {
			$this->log->debug( 'No posts in request state found.' );

			return;
		}

		// Send a request for each post.
		foreach ( $posts as $id ) {
			$this->log->debug( "Requesting results for post $id..." );

			// Send the actual request to the remote service.
			$response = $this->do_complete( $id );

			$this->log->debug( "Results requested for post $id." );

			// Set an error if we received an error.
			if ( ! is_wp_error( $response ) && isset( $response['body'] ) ) {

				$this->log->debug( "Results received for post $id." );

				// Save the returned content as new revision.
				$json = json_decode( $response['body'] );

				// Continue if the content isn't set.
				if ( ! isset( $json->content ) || empty( $json->content ) ) {
					continue;
				}

				$content = wp_slash( $json->content );

				$this->set_warning_based_on_content( $id, $content );

				wp_update_post( array(
					'ID'           => $id,
					'post_content' => $content,
				) );

				// Update the status.
				$this->set_state( $id, self::STATE_SUCCESS );

				$this->log->debug( "Post $id updated with batch analysis results." );

				continue;
			}

			// @todo: implement a kind of timeout that sets an error if the
			// results haven't been received after a long time.

		}

		// Call the `wl_batch_analysis_request` action again. This is going
		// to be handled by the async task.
		do_action( 'wl_batch_analysis_complete' );

	}

	private function set_warning_based_on_content( $post_id, $content ) {

		$warning = preg_match( '/\\w<[a-z]+ id="urn:enhancement-[^"]+" class="[^"]+" itemid="[^"]+">/', $content )
		           || preg_match( '/<[a-z]+ id="urn:enhancement-[^"]+" class="[^"]+" itemid="[^"]+">\\s/', $content );

		$this->set_warning( $post_id, $warning );

		return $content;
	}

	private function set_warning( $post_id, $value ) {

		return update_post_meta( $post_id, self::WARNING_META_KEY, true === $value ? 'yes' : 'no' );
	}

	/**
	 * Get the post/page batch analysis state.
	 *
	 * @since 3.14.2
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return int|string The post state or an empty string if not set.
	 */
	public function get_state( $post_id ) {

		return get_post_meta( $post_id, self::STATE_META_KEY, true );
	}

	/**
	 * Set the post/page batch analysis state.
	 *
	 * @since 3.14.2
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 * @param int $value   The new state.
	 *
	 * @return int|bool Meta ID if the key didn't exist, true on successful update,
	 *                  false on failure.
	 */
	public function set_state( $post_id, $value ) {

		// Update the state.
		$result = update_post_meta( $post_id, self::STATE_META_KEY, $value );

		// Update timestamps as required.
		switch ( $value ) {

			//### REQUEST state.
			case self::STATE_REQUEST:
				update_post_meta( $post_id, self::REQUEST_TIMESTAMP_META_KEY, time() );
				break;

			//### SUCCESS/ERROR state.
			case self::STATE_SUCCESS:
			case self::STATE_ERROR:
				update_post_meta( $post_id, self::COMPLETE_TIMESTAMP_META_KEY, time() );
				break;
		}

		// Finally return the result.
		return $result;
	}

	/**
	 * Get the link setting for a {@link WP_Post}.
	 *
	 * If there are multiple link settings, only the last one is returned.
	 *
	 * @since 3.14.2
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return string The link setting or `default` if not set.
	 */
	public function get_link( $post_id ) {

		$values = get_post_meta( $post_id, self::LINK_META_KEY );

		return end( $values ) ?: 'default';
	}

	/**
	 * Get the batch queues.
	 *
	 * Simplifies setting defaults if the option do not exists.
	 *
	 * @deprecated
	 * @since 3.14.0
	 *
	 * @return array The batch queues.
	 */
	private function get_batch_queues() {

		$batch = get_option( self::OPTION_NAME, array(
			self::ANALYZE_QUEUE  => array(),
			self::RESPONSE_QUEUE => array(),
		) );

		return $batch;
	}

	/**
	 * Get the array of post IDS waiting in the queue to start processing.
	 *
	 * @deprecated
	 * @since 3.14.0
	 *
	 * @return array The waiting to be processed post ids queue.
	 */
	public function waiting_for_analysis() {

		$batch = $this->get_batch_queues();

		return $batch[ self::ANALYZE_QUEUE ];
	}

	/**
	 * Get the array of post IDS waiting for response.
	 *
	 * @deprecated
	 * @since 3.14.0
	 *
	 * @return array The waiting for response post ids queue.
	 */
	public function waiting_for_response() {

		$batch = $this->get_batch_queues();

		return $batch[ self::RESPONSE_QUEUE ];
	}

	/**
	 * Enqueue post IDs to be analyzed. For each set a link setting should be supplied.
	 *
	 * @deprecated
	 * @since 3.14.0
	 *
	 * @param int|array $post_ids     A post ID of the posts to analyze, or an array of them.
	 * @param string    $link_setting The link setting to be applied for the analyzsis of the posts.
	 */
	public function enqueue_for_analysis( $post_ids, $link_setting ) {
		if ( ! is_array( $post_ids ) ) {
			$post_ids = array( $post_ids );
		}

		$batch = $this->get_batch_queues();

		foreach ( $post_ids as $pid ) {
			$batch[ self::ANALYZE_QUEUE ][ $pid ] = array(
				'id'   => $pid,
				'link' => $link_setting,
			);
		}
		update_option( self::OPTION_NAME, $batch );
	}

	/**
	 * Helper function to handle the waiting to be analyzed queue.
	 *
	 * @deprecated
	 * @since 3.14.0
	 *
	 * @param array &$analyze_queue  reference to the waiting queue.
	 * @param array &$response_queue reference to the response queue.
	 *
	 * @throws Exception
	 */
	private function handle_waiting_queue( &$analyze_queue, &$response_queue ) {

		// Bail out if the queue is empty.
		if ( empty( $analyze_queue ) ) {
			return;
		}

		/*
		 * If we have any post waiting in the queue, send a request
		 * to the wordlift server to process it, when the requests includes
		 * the content and the id of the post.
		 */
		for ( $i = 0; $i < 10; $i ++ ) {

			$item = array_pop( $analyze_queue );

			if ( null === $item ) {
				continue;
			}

			$id = $item['id'];

			// Request the analysis for the specified post.
			$response = $this->do_request( $id );

			// If it's an error log it.
			if ( is_wp_error( $response ) ) {
				$analyze_queue[ $id ] = $item;
				$message              = "An error occurred while requesting a batch analysis: {$response->get_error_message()}";
				$this->log->error( $message );
				throw new Exception( $response->get_error_message(), $response->get_error_code() );
			} else {
				$response_queue[ $item['id'] ] = $item;
			}
		}

	}

	/**
	 * Request the analysis for the specified {@link WP_Post}.
	 *
	 * @since 3.14.2
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return WP_Error|array The response or WP_Error on failure.
	 */
	private function do_request( $post_id ) {

		// Get the post.
		$post = get_post( $post_id );

		// Bail out if the post isn't found.
		if ( null === $post ) {
			$this->log->warn( "Post $post_id not found." );

			return new WP_Error( 0, "Cannot find post $post_id." );
		}

		// Get the link setting.
		$link = $this->get_link( $post_id );

		$this->log->debug( "Sending analysis request for post $post_id [ link :: $link ]..." );

		// Get the batch analysis URL.
		$url = $this->configuration_service->get_batch_analysis_url();

		// Prepare the POST parameters.
		$param = array(
			'id'              => $post->ID,
			'key'             => $this->configuration_service->get_key(),
			'content'         => $post->post_content,
			'contentLanguage' => $this->configuration_service->get_language_code(),
			'version'         => $this->plugin->get_version(),
			'links'           => $link,
			'scope'           => 'local',
		);

		// Get the HTTP options.
		$args = array_merge_recursive( unserialize( WL_REDLINK_API_HTTP_OPTIONS ), array(
			'method'      => 'POST',
			'headers'     => array(
				'Accept'       => 'application/json',
				'Content-type' => 'application/json; charset=UTF-8',
			),
			// we need to downgrade the HTTP version in this case since chunked encoding is dumping numbers in the response.
			'httpversion' => '1.0',
			'body'        => wp_json_encode( $param ),
		) );

		$this->log->debug( "Posting analysis request for post $post_id to $url..." );

		// Post the parameter.
		return wp_remote_post( $url, $args );
	}

	/**
	 * Helper function to handle the waiting for response queue.
	 *
	 * @deprecated
	 * @since 3.14.0
	 *
	 * @param array &$analyze_queue  reference to the waiting queue.
	 * @param array &$response_queue reference to the response queue.
	 *
	 * @throws Exception
	 */
	private function handle_response_queue( &$analyze_queue, &$response_queue ) {

		// Bail out if the queue is empty.
		if ( empty( $response_queue ) ) {
			return;
		}

		// If we have any post waiting for a reply to any post, send a status
		// request to the server.
		for ( $i = 0; $i < 10; $i ++ ) {
			$item = array_pop( $response_queue );

			if ( null === $item ) {
				continue;
			}

			$id = $item['id'];

			$response = $this->do_complete( $id );

			// If it's an error log it.
			if ( is_wp_error( $response ) ) {
				$analyze_queue[ $id ] = $item;
				$message              = "An error occurred while requesting a batch analysis to $url: {$response->get_error_message()}";
				$this->log->error( $message );
				throw new Exception( $response->get_error_message(), $response->get_error_code() );
			} elseif ( 200 != $response['response']['code'] ) {
				$analyze_queue[ $id ] = $item;
			} else {
				// Save the returned content as new revision.
				$decode  = json_decode( $response['body'] );
				$content = $decode->content;
				wp_update_post( array(
					'ID'           => $id,
					'post_content' => wp_slash( $content ),
				) );
			}

		}

	}

	private function do_complete( $post_id ) {

		$post = get_post( $post_id );

		if ( null === $post ) {
			// Post was possibly deleted, just bailout.
			return new WP_Error( 0, "Post $post_id not found." );
		}

		$url = $this->configuration_service->get_batch_analysis_url();
		$key = $this->configuration_service->get_key();
		$url = $url . '/' . $post->ID . '?key=' . $key;

		return wp_remote_get( $url, unserialize( WL_REDLINK_API_HTTP_OPTIONS ) );
	}

	/**
	 * Execute the batch analysis for the next items in the analyze and response queues.
	 *
	 * @since 3.14.0
	 */
	public function batch_analyze() {

		$batch = $this->get_batch_queues();
		$this->handle_waiting_queue( $batch[ self::ANALYZE_QUEUE ], $batch[ self::RESPONSE_QUEUE ] );
		$this->handle_response_queue( $batch[ self::ANALYZE_QUEUE ], $batch[ self::RESPONSE_QUEUE ] );
		update_option( self::OPTION_NAME, $batch );

		if ( ! empty( $batch[ self::ANALYZE_QUEUE ] ) || ! empty( $batch[ self::RESPONSE_QUEUE ] ) ) {
			wp_schedule_single_event( time(), 'wl_batch_analyze' );
		}

	}

}
