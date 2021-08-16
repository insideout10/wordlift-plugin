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
class V2_Analysis_Service extends Abstract_Analysis_Service {

	public function get_analysis_response( $data, $content_type, $post_id ) {


		return $this->api_service->post_custom_content_type( 'analysis/v2/analysis', $data, $content_type );
	}
}