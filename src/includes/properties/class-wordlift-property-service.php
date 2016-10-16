<?php

class Wordlift_Property_Service_2 {

	/**
	 * @var Wordlift_Simple_Property_Service[]
	 */
	private $services = array();

	/**
	 * @var Wordlift_Simple_Property_Service
	 */
	private $default;

	public function __construct( $default ) {

		$this->default = $default;

	}

	public function register( $meta_key, $property_service ) {

		$this->services[ $meta_key ] = $property_service;

	}

	public function get( $post_id, $meta_key ) {

		return isset( $this->services[ $meta_key ] ) ? $this->services[ $meta_key ]->get( $post_id, $meta_key ) : $this->default->get( $post_id, $meta_key );
	}

}
