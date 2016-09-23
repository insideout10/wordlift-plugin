<?php

/**
 * @since 3.6.0
 */
class Wordlift_Schema_Url_Property_Service_Test extends WP_UnitTestCase {

	/**
	 * @var Wordlift_Schema_Url_Property_Service $schema_url_property_service
	 */
	private $schema_url_property_service;

	public function setUp() {
		parent::setUp();

		$this->schema_url_property_service = Wordlift_Schema_Url_Property_Service::get_instance();
	}


	public function test() {

//		$this->schema_url_property_service->get_post_metadata();

	}
}
