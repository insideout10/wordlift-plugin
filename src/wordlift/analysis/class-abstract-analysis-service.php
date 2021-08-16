<?php
/**
 *
 * This file provides abstract class encapsulating $api_service.
 *
 * @package  Wordlift\Analysis
 */

namespace Wordlift\Analysis;

use Wordlift\Common\Singleton;

/**
 * The abstract analysis service is extended by the v1, v2 analysis services.
 */
abstract class Abstract_Analysis_Service extends Singleton implements  Analysis_Service {

	/**
	 * Wordlift api service.
	 *
	 * @var \Wordlift_Api_Service
	 */
	protected $api_service;

	/**
	 * Abstract_Analysis_Service constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->api_service = \Wordlift_Api_Service::get_instance();
	}

}
