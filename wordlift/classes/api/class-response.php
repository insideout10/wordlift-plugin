<?php

namespace Wordlift\Api;

class Response {

	private $response;

	/**
	 * @var int|string
	 */
	private $code;

	/**
	 * @var bool
	 */
	private $is_success;

	/**
	 * @var string
	 */
	private $body;

	public function __construct( $response ) {

		$this->response   = $response;
		$this->code       = wp_remote_retrieve_response_code( $this->response );
		$this->is_success = ! empty( $this->code ) && 2 === intval( $this->code / 100 );
		$this->body       = wp_remote_retrieve_body( $this->response );

	}

	public function is_success() {

		return $this->is_success;

	}

	public function get_body() {

		return $this->body;
	}

	public function get_response() {

		return $this->response;
	}

}
