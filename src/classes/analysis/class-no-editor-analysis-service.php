<?php
/**
 *
 * This file provides access to v2 analysis service.
 *
 * @package  Wordlift\Analysis
 */

namespace Wordlift\Analysis;

/**
 * V2_Analysis_Service constructor.
 */
class No_Editor_Analysis_Service extends Abstract_Analysis_Service {

	public function get_analysis_response( $data, $content_type, $post_id ) {

		$v2_analysis_request = new No_Editor_Analysis_Request( $post_id );

		$json_encoded_body = wp_json_encode( $v2_analysis_request->get_data() );

		return $this->api_service->post_custom_content_type( 'analysis/v2/analyze', $json_encoded_body, $content_type );
	}
}
