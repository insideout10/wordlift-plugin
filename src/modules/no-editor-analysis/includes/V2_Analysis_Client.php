<?php
/**
 *
 * This file provides access to v1 (/analysis/single) analysis service.
 *
 * @package  Wordlift\Analysis
 */

namespace Wordlift\Modules\No_Editor_Analysis;

use Wordlift\Api\Api_Service;

/**
 */
class V2_Analysis_Client {

	/**
	 * @var Api_Service
	 */
	private $api_service;

	public function __construct( Api_Service $api_service ) {
		$this->api_service = $api_service;
	}

	public function register_hooks() {
		add_filter( 'wl_analysis_service_factory__get_instance', array( $this, '__return_this' ) );
	}

	public function __return_this() {
		return $this;
	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function get_analysis_response( $data, $content_type, $post_id ) {

		// `text` or `html.fragment`
		if ( isset( $data['contentType'] ) && 'text' === $data['contentType'] ) {
			$request['text'] = $data['content'];
		} else {
			$request['html'] = array(
				'fragment' => $data['content'],
			);
		}

		if ( isset( $data['contentLanguage'] ) ) {
			$request['language'] = $data['contentLanguage'];
		}

		if ( isset( $data['exclude'] ) ) {
			$request['exclude'] = $data['exclude'];
		}

		if ( isset( $data['scope'] ) ) {
			$request['scope'] = $data['scope'];
		}

		if ( isset( $data['minimumOccurrences'] ) ) {
			$request['matches'] = $data['minimumOccurrences'];
		}

		if ( isset( $data['links'] ) ) {
			$request['links'] = $data['links'];
		}

		$request_json = wp_json_encode( $request, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

		return $this->api_service->post_custom_content_type(
			'analysis/v2/analyze',
			$request_json,
			$content_type
		);
	}
}
