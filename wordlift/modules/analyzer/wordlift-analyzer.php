<?php

use Wordlift\Analysis\Analysis_Service_Factory;
use Wordlift\Analysis\Response\Analysis_Response_Ops_Factory;

/**
 * This function returns empty array response from analysis,
 * this is usually called when the analysis is disabled using
 * `wl_feature__enable__analysis` hook.
 *
 * @since 3.27.6
 */
function wl_ajax_analyze_disabled_action() {
	// adding the below header for debugging purpose.
	if ( ! headers_sent() ) {
		header( 'X-WordLift-Analysis: OFF' );
	}
	wp_send_json_success(
		array(
			'entities'    => array(),
			'annotations' => array(),
			'topics'      => array(),
		)
	);
}

/**
 * Receive some content, run a remote analysis task and return the results. The content is read from the body
 * input (php://input).
 *
 * @since 1.0.0
 * @since 3.24.2 this function doesn't fail anymore. If an error occurres in the analysis, then an empty response
 *  is returned.
 * @uses  wl_analyze_content() to analyze the provided content.
 */
function wl_ajax_analyze_action() {

	check_admin_referer( 'wl_analyze' );
	$data = '';

	if ( isset( $_POST['data'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		// We need to send the data from editor without sanitizing to analysis service.
		$filtered_data = filter_var_array( $_POST, array( 'data' => array( 'flags' => FILTER_UNSAFE_RAW ) ) );
		$data          = $filtered_data['data'];
	}

	wp_send_json_success( wl_analyze_content( $data, 'application/json; charset=' . strtolower( get_bloginfo( 'charset' ) ) ) );

}

/**
 * Analyze the provided content. The analysis will make use of the method *wl_ajax_analyze_action*
 * provided by the WordLift plugin.
 *
 * @param string $data The data structure containing information about the content to analyze as a string.
 *
 * @param string $content_type The content type.
 *
 * @return string Returns null on failure, or the WP_Error, or a WP_Response with the response.
 *
 * @uses  wl_configuration_get_analyzer_url() to get the API for the analysis.
 *
 * @since 1.0.0
 * @since 3.24.2 We don't return an error anymore, but an empty analysis response. This is required to allow the editor
 *   to manage entities or to manually add them even when analysis isn't available.
 */
function wl_analyze_content( $data, $content_type ) {

	$default_response = json_decode( '{ "entities": {}, "annotations": {}, "topics": {} }' );
	$request_body     = json_decode( $data, true );

	$post_id = isset( $_REQUEST['postId'] ) ? intval( $_REQUEST['postId'] ) : 0; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( null === $request_body ) {
		/**
		 * @since 3.27.7
		 *
		 * Conditionally try using stripslashes as $_POST['data'] could be escaped
		 */
		$request_body = json_decode( stripslashes( $data ), true );
	}
	$request_body['contentLanguage'] = Wordlift_Configuration_Service::get_instance()->get_language_code();
	$excluded_uris                   = array_key_exists( 'exclude', $request_body ) ? (array) $request_body['exclude'] : array();
	$data                            = wp_json_encode( $request_body );

	// If dataset is not enabled, return a locally prepared response without analysis API.
	if ( ! apply_filters( 'wl_feature__enable__dataset', true ) ) {

		return Analysis_Response_Ops_Factory::get_instance()
											->create( $default_response, $post_id )
											->make_entities_local()
											->add_occurrences( $request_body['content'] )
											->add_local_entities()
											->get_json();
	}

	add_filter( 'wl_api_service_api_url_path', 'wl_use_analysis_on_api_wordlift_io' );

	$json = Analysis_Service_Factory::get_instance( $post_id )
									->get_analysis_response( $data, $content_type, $post_id );

	remove_filter( 'wl_api_service_api_url_path', 'wl_use_analysis_on_api_wordlift_io' );

	// If it's an error log it.
	if ( is_wp_error( $json ) ) {
		return Analysis_Response_Ops_Factory::get_instance()
											->create( $default_response, $post_id )
											->make_entities_local()
											->add_occurrences( $request_body['content'] )
											->get_json();
	}

	/*
	 * We pass the response to the Analysis_Response_Ops to ensure that we make remote entities local.
	 *
	 * @see https://github.com/insideout10/wordlift-plugin/issues/944
	 * @since 3.21.5
	 */

	// Get the actual content sent to the analysis, so that we can pass it to the Analysis_Response_Ops to populate
	// the occurrences for the local entities.
	if ( 0 === strpos( $content_type, 'application/json' ) ) {
		$request_content = $request_body['content'];
	} else {
		$request_content = $data;
	}

	return Analysis_Response_Ops_Factory::get_instance()
										->create( $json, $post_id )
										->make_entities_local()
										->remove_excluded_entities( $excluded_uris )
										->add_occurrences( $request_content )
										->get_json();

}

function wl_use_analysis_on_api_wordlift_io( $value ) {

	return preg_replace( '|https://api\.wordlift\.it/|', apply_filters( 'wl_api_base_url', 'https://api.wordlift.io' ) . '/', $value );
}
