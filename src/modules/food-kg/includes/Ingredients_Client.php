<?php

namespace Wordlift\Modules\Food_Kg;

use Wordlift\Api\Api_Service;

class Ingredients_Client {

	/**
	 * @var Api_Service
	 */
	private $api_service;

	public function __construct( Api_Service $api_service ) {
		$this->api_service = $api_service;
	}

	/**
	 * @param string[] $names
	 *
	 * @return Ingredients
	 */
	public function ingredients( $names ) {

		$request_body = implode( "\n", $names );
		$response     = $this->api_service->request( 'POST', '/thirdparty/cafemedia/food-kg/ingredients', [ 'content-type' => 'text/plain' ], $request_body );

		return Ingredients::create_from_string( $response->get_body() );
	}

}
