<?php

class Wordlift_Batch_analysis_Service {

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
	 * @var \Wordlift $plugin The {@link Wordlift} plugin instance.
	 */
	public function __construct( $plugin ) {

		add_action( 'wl_batch_analyze', array( $this, 'batch_analyze' ) );

		$this->plugin = $plugin;
	}

	public function batch_analyze() {
		$batch = get_option( 'wl_analyze_batch', array(
												'queue' => array(),
												'processing' => array(),
		) );
		if ( ! empty( $batch['queue'] ) ) {
			/*
			 * If we have any post waiting in the queue, send a request
			 * to the wordlift server to process it, when the requests includes
			 * the content and the id of the post.
			 */
			$item = array_pop( $batch['queue'] );
			if ( $item ) { // just being extra careful.
				$post = get_post( $item['id'] );
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
					$batch['queue'][ $id ] = $item;
					$message = "An error occurred while requesting a batch analysis to $url: {$response->get_error_message()}";
					Wordlift_Log_Service::get_logger( 'wl_analyze_content' )->error( $message );
					throw new Exception( $response->get_error_message(), $response->get_error_code() );
				} else {
					$batch['processing'][ $item['id'] ] = $item;
				}
			}
		}
		if ( ! empty( $batch['processing'] ) ) {
			/*
		 	 * If we have any post waiting for a reply to any post, send a status
			 * request to the server.
			 */
			$item = array_pop( $batch['processing'] );
			if ( $item ) { // just being extra careful.
				$post = get_post( $item['id'] );
				$apiurl = wl_configuration_get_batch_analysis_url();
				$id	= $item['id'];
				$key = wl_configuration_get_key();
				$url = $apiurl . '/' . $id . '?key=' . $key;
				$response = wp_remote_get( $url, unserialize( WL_REDLINK_API_HTTP_OPTIONS ) );
				// If it's an error log it.
				if ( is_wp_error( $response ) ) {
					$batch['queue'][ $id ] = $item;
					$message = "An error occurred while requesting a batch analysis to $url: {$response->get_error_message()}";
					Wordlift_Log_Service::get_logger( 'wl_analyze_content' )->error( $message );
					throw new Exception( $response->get_error_message(), $response->get_error_code() );
				} elseif ( 200 != $response['response']['code'] ) {
					$batch['queue'][ $id ] = $item;
				} else {
					// Save the returned content as new revision.
					$decode = json_decode( $response['body'] );
					$content = $decode->content;
					wp_update_post( array(
						'ID' => $id,
						'post_content' => wp_slash( $content ),
					) );
				}
			}
		}
		update_option( 'wl_analyze_batch', $batch );
		if ( ! empty( $batch['queue'] ) || ! empty( $batch['processing'] ) ) {
			wp_schedule_single_event( time(), 'wl_batch_analyze' );
		}
	}
}
