<?php

namespace Wordlift\Modules\Plugin_Diagnostics;

use Wordlift\Api\Api_Service;

class Plugin_Diagnostics_API {

	/**
	 * @var Api_Service
	 */
	private $api_service;

	/**
	 * @param Api_Service $api_service
	 */
	public function __construct( Api_Service $api_service ) {
		$this->api_service = $api_service;
	}

	/**
	 * Update.
	 *
	 * @param $payload
	 */
	public function update( $payload ) {
		$this->api_service->request(
			'PUT',
			'/accounts/me/plugin/diagnostics/plugins-collection',
			array( 'content-type' => 'application/json' ),
			wp_json_encode( $payload )
		);
	}
}
