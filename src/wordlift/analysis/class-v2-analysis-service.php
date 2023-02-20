<?php
/**
 *
 * This file provides access to v1 (/analysis/single) analysis service.
 *
 * @package  Wordlift\Analysis
 */

namespace Wordlift\Analysis;

/**
 * V1_Analysis_Service constructor.
 */
class V2_Analysis_Service extends Abstract_Analysis_Service {

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function get_analysis_response( $data, $content_type, $post_id ) {

		$json = json_decode( $data, true );

		// `text` or `html.fragment`
		if ( isset( $json['contentType'] ) && 'text/plain' === $json['contentType'] ) {
			$request['text'] = $json['content'];
		} else if ( strpos( $json['content'], '<html ' ) !== false ) {
			$request['html'] = array(
				'page' => $json['content'],
			);
		} else {
			$request['html'] = array(
				'fragment' => $json['content'],
			);
		}

		if ( isset( $json['contentLanguage'] ) ) {
			$request['language'] = $json['contentLanguage'];
		}

		if ( isset( $json['exclude'] ) ) {
			$request['exclude'] = $json['exclude'];
		}

		if ( isset( $json['scope'] ) ) {
			$request['scope'] = $json['scope'];
		}

		if ( isset( $json['minimumOccurrences'] ) ) {
			$request['matches'] = $json['minimumOccurrences'];
		}

		if ( isset( $json['links'] ) ) {
			$request['links'] = $json['links'];
		}

		$request_as_string = wp_json_encode( $request, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		$response_as_json  = $this->api_service->post_custom_content_type(
			'analysis/v2/analyze',
			$request_as_string,
			$content_type
		);

		return $this->filter_response_json(
			$response_as_json,
			isset( $request['html']['page'] )
		);
	}

	/**
	 * Remove the text annotations when analyzing a full html page since they do not match what we
	 * have in post content.
	 *
	 * @param object $json The response JSON
	 * @param bool $is_html_page Whether the request was for a full html page.
	 *
	 * @return mixed
	 */
	private function filter_response_json( $json, $is_html_page ) {
		if ( $is_html_page ) {
			unset( $json->annotations );
		}

		return $json;
	}
}
