<?php

class Wordlift_Batch_analysis_Service {

	/**
	 * Option Name
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
	 * @since 3.14.0
	 *
	 * @var \Wordlift $plugin The {@link Wordlift} plugin instance.
	 */
	private $plugin;

	/**
	 * The {@link Class_Wordlift_Batch_Analys_Service} instance.
	 *
	 * @since 3.14.0
	 *
	 * @param \Wordlift $plugin The {@link Wordlift} plugin instance.
	 */
	public function __construct( $plugin ) {

		add_action( 'wl_batch_analyze', array( $this, 'batch_analyze' ) );

		$this->plugin = $plugin;
	}

	/**
	 * Get the batch queues.
	 *
	 * Simplifies setting defaults if the option do not exists.
	 *
	 * @since 3.14.0
	 *
	 * @return the batch queues.
	 */
	private function get_batch_queues() {

		$batch = get_option( self::OPTION_NAME, array(
						self::ANALYZE_QUEUE => array(),
						self::RESPONSE_QUEUE => array(),
		) );

		return $batch;
	}

	/**
	 * Get the array of post IDS waiting in the queue to start processing.
	 *
	 * @since 3.14.0
	 *
	 * @return the waiting to be processed post ids queue
	 */
	public function waiting_for_analysis() {
		$batch = $this->get_batch_queues();

		return $batch[ self::ANALYZE_QUEUE ];
	}

	/**
	 * Get the array of post IDS waiting for response.
	 *
	 * @since 3.14.0
	 *
	 * @return the waiting for response post ids queue.
	 */
	public function waiting_for_response() {
		$batch = $this->get_batch_queues();

		return $batch[ self::RESPONSE_QUEUE ];
	}

	/**
	 * Enqueue post IDs to be analyzed. For each set a link setting should be supplied.
	 *
	 * @since 3.14.0
	 *
	 * @param int|array $post_ids A post ID of the posts to analyze, or an array of them.
	 * @param string 	$link_setting The link setting to be applied for the analyzsis of the posts.
	 */
	public function enqueue_for_analysis( $post_ids, $link_setting ) {
		if ( ! is_array( $post_ids ) ) {
			$post_ids = array( $post_ids );
		}

		$batch = $this->get_batch_queues();

		foreach ( $post_ids as $pid ) {
			// If it was already batch processed, just skip.
			if ( ! get_post_meta( $pid, '_wl_batch_analysis', true ) ) {
				$batch[ self::ANALYZE_QUEUE ][ $pid ] = array( 'id' => $pid, 'link' => $link_setting );
			}
		}
		update_option( self::OPTION_NAME, $batch );
	}

	/**
	 * Helper function to handle the waiting to be analyzed queue.
	 *
	 * @since 3.14.0
	 *
	 * @param array &$analyze_queue reference to the waiting queue.
	 * @param array &$response_queue reference to the response queue.
	 *
	 */
	private function handle_waiting_queue( &$analyze_queue, &$response_queue ) {

		if ( ! empty( $analyze_queue ) ) {
			/*
			 * If we have any post waiting in the queue, send a request
			 * to the wordlift server to process it, when the requests includes
			 * the content and the id of the post.
			 */
			$item = array_pop( $analyze_queue );
			if ( $item ) { // just being extra careful.
				$post = get_post( $item['id'] );
				if ( empty( $post ) ) {
					// Post was possibly deleted, just bailout.
					return;
				}
				$url = wl_configuration_get_batch_analysis_url();
				$param = array(
					'id'	=> $item['id'],
					'key'	=> wl_configuration_get_key(),
					'content' => $post->post_content,
					'contentLanguage' => Wordlift_Configuration_Service::get_instance()->get_language_code(),
					'version' => $this->plugin->get_version(),
					'links' => $item['link'],
					'scope' => 'local',
				);
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

				$response = wp_remote_post( $url, $args );
				// If it's an error log it.
				if ( is_wp_error( $response ) ) {
					$analyze_queue[ $id ] = $item;
					$message = "An error occurred while requesting a batch analysis to $url: {$response->get_error_message()}";
					Wordlift_Log_Service::get_logger( 'wl_analyze_content' )->error( $message );
					throw new Exception( $response->get_error_message(), $response->get_error_code() );
				} else {
					$response_queue[ $item['id'] ] = $item;
				}
			}
		}
	}

	/**
	 * Helper function to handle the waiting for response queue.
	 *
	 * @since 3.14.0
	 *
	 * @param array &$analyze_queue reference to the waiting queue.
	 * @param array &$response_queue reference to the response queue.
	 *
	 */
	private function handle_response_queue( &$analyze_queue, &$response_queue ) {

		if ( ! empty( $response_queue ) ) {
			/*
		 	 * If we have any post waiting for a reply to any post, send a status
			 * request to the server.
			 */
			$item = array_pop( $response_queue );
			if ( $item ) { // just being extra careful.
				$post = get_post( $item['id'] );
				if ( empty( $post ) ) {
					// Post was possibly deleted, just bailout.
					return;
				}
				$apiurl = wl_configuration_get_batch_analysis_url();
				$id	= $item['id'];
				$key = wl_configuration_get_key();
				$url = $apiurl . '/' . $id . '?key=' . $key;
				$response = wp_remote_get( $url, unserialize( WL_REDLINK_API_HTTP_OPTIONS ) );
				// If it's an error log it.
				if ( is_wp_error( $response ) ) {
					$analyze_queue[ $id ] = $item;
					$message = "An error occurred while requesting a batch analysis to $url: {$response->get_error_message()}";
					Wordlift_Log_Service::get_logger( 'wl_analyze_content' )->error( $message );
					throw new Exception( $response->get_error_message(), $response->get_error_code() );
				} elseif ( 200 != $response['response']['code'] ) {
					$analyze_queue[ $id ] = $item;
				} else {
					// Save the returned content as new revision.
					$decode = json_decode( $response['body'] );
					$content = $decode->content;
					wp_update_post( array(
						'ID' => $id,
						'post_content' => wp_slash( $content ),
					) );
					// Add indication that the post was batch processed.
					add_post_meta( $id, '_wl_batch_analysis', true, true );
				}
			}
		}
	}

	/**
	 * Execute the batch analysis for the next items in the analyze and response queues.
	 *
	 * @since 3.14.0
	 *
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
