<?php

namespace Wordlift\Api;

interface Api_Service {

	/**
	 * @param $method
	 * @param $url
	 * @param array $headers
	 * @param null $body
	 * @param null $timeout
	 * @param null $user_agent
	 * @param array $args
	 *
	 * @return Response
	 */
	public function request( $method, $url, $headers = array(), $body = null, $timeout = null, $user_agent = null, $args = array() );

	/**
	 * @param $url
	 * @param array $headers
	 * @param null $body
	 * @param null $timeout
	 * @param null $user_agent
	 * @param array $args
	 *
	 * @return Response
	 */
	public function get( $url, $headers = array(), $body = null, $timeout = null, $user_agent = null, $args = array() );

}
