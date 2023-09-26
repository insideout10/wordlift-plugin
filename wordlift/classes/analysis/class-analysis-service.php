<?php
/**
 *
 * This file provides interface for analysis service.
 *
 * @package  Wordlift\Analysis
 */

namespace Wordlift\Analysis;

/**
 * @since 3.32.5
 * Interface for analysis service.
 */
interface Analysis_Service {

	/**
	 * Return analysis response from the service.
	 *
	 * @param array  $data The analysis data.
	 * @param String $content_type Content type for the request.
	 * @param int    $post_id Post id.
	 *
	 * @return string|object|\WP_Error A {@link WP_Error} instance or the actual response content.
	 */
	public function get_analysis_response( $data, $content_type, $post_id );

}
