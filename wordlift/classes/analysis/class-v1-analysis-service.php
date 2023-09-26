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
class V1_Analysis_Service extends Abstract_Analysis_Service {

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function get_analysis_response( $data, $content_type, $post_id ) {
		return $this->api_service->post_custom_content_type( 'analysis/single', $data, $content_type );
	}
}
