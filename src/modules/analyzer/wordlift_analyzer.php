<?php

use Wordlift\Analysis\Response\Analysis_Response_Ops;

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

	$data      = filter_input( INPUT_POST, 'data' );

	wp_send_json_success( wl_analyze_content( $data ) );

}

add_action( 'wp_ajax_wl_analyze', 'wl_ajax_analyze_action' );

/**
 * Analyze the provided content. The analysis will make use of the method *wl_ajax_analyze_action*
 * provided by the WordLift plugin.
 *
 * @param string $data The data structure containing information about the content to analyze as a string.
 *
 * @return string Returns null on failure, or the WP_Error, or a WP_Response with the response.
 *
 * @uses  wl_configuration_get_analyzer_url() to get the API for the analysis.
 *
 * @since 1.0.0
 * @since 3.24.2 We don't return an error anymore, but an empty analysis response. This is required to allow the editor
 *   to manage entities or to manually add them even when analysis isn't available.
 */
function wl_analyze_content( $data ) {

	// Set the content type to the request content type or to text/plain by default.
	$content_type = isset( $_SERVER['CONTENT_TYPE'] ) ? $_SERVER['CONTENT_TYPE'] : 'text/plain';

	add_filter( 'wl_api_service_api_url_path', 'wl_use_analysis_on_api_wordlift_io' );
	$json = Wordlift_Api_Service::get_instance()
	                            ->post_custom_content_type( 'analysis/single', $data, $content_type );
	remove_filter( 'wl_api_service_api_url_path', 'wl_use_analysis_on_api_wordlift_io' );

	// If it's an error log it.
	if ( is_wp_error( $json ) ) {
		$request_body = json_decode( $data, true );

		return Analysis_Response_Ops::create( json_decode( '{ "entities": {}, "annotations": {}, "topics": {} }' ) )
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
		$request_json    = json_decode( $data );
		$request_content = $request_json->content;
	} else {
		$request_content = $data;
	}

	return Analysis_Response_Ops::create( $json )
	                            ->make_entities_local()
	                            ->add_occurrences( $request_content )
	                            ->get_json();

}

function wl_use_analysis_on_api_wordlift_io( $value ) {

	return preg_replace( '|https://api\.wordlift\.it/|', 'https://api.wordlift.io/', $value );
}
