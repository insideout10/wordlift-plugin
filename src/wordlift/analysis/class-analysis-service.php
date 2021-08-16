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

	public function get_analysis_response( $data, $content_type );

}