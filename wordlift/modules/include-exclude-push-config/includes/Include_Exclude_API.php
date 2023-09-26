<?php

namespace Wordlift\Modules\Include_Exclude_Push_Config;

use Wordlift\Api\Api_Service;

class Include_Exclude_API {

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
			'/accounts/me/include-excludes',
			array( 'content-type' => 'application/json' ),
			wp_json_encode( $payload )
		);
	}
}
