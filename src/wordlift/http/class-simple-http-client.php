<?php

namespace Wordlift\Http;

/**
 * Define a simple Simple_Http_Client class for an http client
 * which simply performs the http requests using WordPress functions.
 *
 * @since 1.0.0
 */
class Simple_Http_Client implements Http_Client {

	/**
	 * @inheritDoc
	 */
	public function get( $url, $options = array() ) {

		$options['method'] = 'GET';

		return $this->request( $url, $options );
	}

	/**
	 * @inheritDoc
	 */
	public function request( $url, $options = array() ) {

		return wp_remote_request( $url, $options );
	}

}
