<?php

class Wordlift_Admin_Search_Rankings_Service {

	/**
	 * The {@link Wordlift_Api_Service} instance.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var \Wordlift_Api_Service $api_service The {@link Wordlift_Api_Service} instance.
	 */
	private $api_service;

	/**
	 * Wordlift_Admin_Search_Rankings_Service constructor.
	 *
	 * @since 3.20.0
	 *
	 * @param $api_service
	 */
	public function __construct( $api_service ) {


		$this->api_service = $api_service;
	}

	public function get() {

		return $this->api_service->get( 'entityrank' );
	}

}
