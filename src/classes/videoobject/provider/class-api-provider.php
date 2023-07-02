<?php

namespace Wordlift\Videoobject\Provider;

use Wordlift\Videoobject\Provider\Client\Client;

/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This acts as abstract class for Providers we get data from using API.
 */
abstract class Api_Provider implements Provider {

	/**
	 * @var Client
	 */
	protected $api_client;

	/**
	 * Api_Provider constructor.
	 *
	 * @param $api_client
	 */
	public function __construct( $api_client ) {
		$this->api_client = $api_client;
	}

}
