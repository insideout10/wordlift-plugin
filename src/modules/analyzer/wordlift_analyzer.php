<?php

use Wordlift\Analysis\Response\Analysis_Response_Ops;

/**
 * Receive some content, run a remote analysis task and return the results. The content is read from the body
 * input (php://input).
 *
 * @since 1.0.0
 *
 * @uses  wl_analyze_content() to analyze the provided content.
 */
function wl_ajax_analyze_action() {

	try {
		if ( $analysis = wl_analyze_content( file_get_contents( "php://input" ) ) ) {
			header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
			echo( $analysis );
			wp_die();
		}

		status_header( 500 );
		wp_send_json( __( 'An error occurred while request an analysis to the remote service. Please try again later.', 'wordlift' ) );

	} catch ( Exception $e ) {
		wp_send_json_error( array(
			'code'    => $e->getCode(),
			'message' => $e->getMessage(),
			'trace'   => $e->getTraceAsString(),
		) );
	}

}

add_action( 'wp_ajax_wordlift_analyze', 'wl_ajax_analyze_action' );

/**
 * Analyze the provided content. The analysis will make use of the method *wl_ajax_analyze_action*
 * provided by the WordLift plugin.
 *
 * @param string $content The content to analyze.
 *
 * @return string Returns null on failure, or the WP_Error, or a WP_Response with the response.
 *
 * @throws Exception
 * @uses  wl_configuration_get_analyzer_url() to get the API for the analysis.
 *
 * @since 1.0.0
 *
 */
function wl_analyze_content( $content ) {

	// Set the content type to the request content type or to text/plain by default.
	$content_type = isset( $_SERVER['CONTENT_TYPE'] ) ? $_SERVER['CONTENT_TYPE'] : 'text/plain';

	add_filter( 'wl_api_service_api_url_path', 'wl_use_analysis_on_api_wordlift_io' );
	$json = Wordlift_Api_Service::get_instance()
	                            ->post_custom_content_type( 'analysis/single', $content, $content_type );
	remove_filter( 'wl_api_service_api_url_path', 'wl_use_analysis_on_api_wordlift_io' );

	// If it's an error log it.
	if ( is_wp_error( $json ) ) {

		$message = "An error occurred while requesting an analysis: {$json->get_error_message()}";

		Wordlift_Log_Service::get_logger( 'wl_analyze_content' )->error( $message );

		throw new Exception( $json->get_error_message(), is_numeric( $json->get_error_code() ) ? $json->get_error_code() : - 1 );
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
		$request_json    = json_decode( $content );
		$request_content = $request_json->content;
	} else {
		$request_content = $content;
	}

	return Analysis_Response_Ops::create( $json )
	                            ->make_entities_local()
	                            ->add_occurrences( $request_content )
	                            ->to_string();

}

function wl_use_analysis_on_api_wordlift_io( $value ) {

	return preg_replace( '|https://api\.wordlift\.it/|', 'https://api.wordlift.io/', $value );
}
