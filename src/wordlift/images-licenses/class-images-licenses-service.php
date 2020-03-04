<?php
/**
 *
 */

namespace Wordlift\Images_Licenses;

use Wordlift\Api\Api_Service;

class Images_Licenses_Service {
	/**
	 * @var Api_Service
	 */
	private $api_service;

	/**
	 * Images_Licenses_Service constructor.
	 *
	 * @param Api_Service $api_service
	 */
	public function __construct( $api_service ) {

		$this->api_service = $api_service;

	}

	public function get_non_public_domain_images() {

		$response      = $this->api_service->get( '/images/GetNonPublicDomainImages' );
		$response_body = $response->get_body();

		if ( empty( $response_body ) ) {
			return array();
		}

		return json_decode( $response_body, true );
	}

}
