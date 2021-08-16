<?php
/**
 *
 * This file provides abstract class encapsulating $api_service.
 *
 * @package  Wordlift\Analysis
 */

namespace Wordlift\Analysis;

/**
 * The abstract analysis service is extended by the v1, v2 analysis services.
 */
abstract class Abstract_Analysis_Service implements  Analysis_Service {

	/**
	 * Wordlift api service.
	 *
	 * @var \Wordlift_Api_Service
	 */
	private $api_service;

	/**
	 * @param \Wordlift_Api_Service $api_service The WordLift Api service.
	 */
	public function __construct( $api_service ) {
		$this->api_service = $api_service;
	}

}
