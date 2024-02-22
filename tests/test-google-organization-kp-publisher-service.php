<?php

use Wordlift\Modules\Google_Organization_Kp\Publisher_Service;

class Google_Organization_KG_Publisher_Service_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Publisher_Service} instance to test.
	 *
	 * @var Publisher_Service $publisher_service_instance The {@link Publisher_Service} instance to test.
	 */
	private $publisher_service_instance;

	/**
	 * Set Up.
	 */
	public function setUp() {
		parent::setUp();

		$this->publisher_service_instance = new Publisher_Service(
			Wordlift_Publisher_Service::get_instance(),
			Wordlift_Entity_Type_Service::get_instance(),
			Wordlift_Configuration_Service::get_instance()
		);

		global $wpdb;
		$this->wpdb = $wpdb;
	}

	/**
	 * Testing if instance is not null, check to determine this class is included.
	 */
	public function test_instance_not_null() {
		$this->assertNotNull( $this->publisher_service_instance );
	}

	/**
	 * Test get method returns correct data on initial set up
	 **/
	public function test_publisher_service_get_method_returns_correct_initial_data() {
		$data = $this->publisher_service_instance->get();

		$this->assertEmpty( $data['page'] );
		$this->assertEquals( 'Organization', $data['type'] );
		$this->assertEquals( 'Edgar Allan Poe', $data['name'] );
		$this->assertEquals( 'Just another WordPress site', $data['alt_name'] );
		$this->assertEmpty( $data['legal_name'] );
		$this->assertEmpty( $data['description'] );
		$this->assertNotEmpty( $data['image'] );
		$this->assertEmpty( $data['url'] );
		$this->assertEmpty( $data['same_as'] );
		$this->assertEmpty( $data['address'] );
		$this->assertEmpty( $data['locality'] );
		$this->assertEmpty( $data['region'] );
		$this->assertEmpty( $data['country'] );
		$this->assertEmpty( $data['postal_code'] );
		$this->assertEmpty( $data['telephone'] );
		$this->assertEmpty( $data['email'] );
		$this->assertEmpty( $data['no_employees'] );
		$this->assertEmpty( $data['founding_date'] );
		$this->assertEmpty( $data['iso_6523'] );
		$this->assertEmpty( $data['naics'] );
		$this->assertEmpty( $data['global_loc_no'] );
		$this->assertEmpty( $data['vat_id'] );

		fwrite(STDERR, print_r($data, TRUE));
	}

	/**
	 * Test get method returns correct data after modification
	 **/
	public function test_publisher_service_get_method_returns_correct_data_after_modification() {
		$post = $this->factory()->post->create_and_get();

		Wordlift_Configuration_Service::get_instance()->set_about_page_id( $post->ID );

		$publisher = $this->entity_factory->create_and_get( array(
			'post_title' => 'Nicholas Cage',
		) );

		Wordlift_Entity_Type_Service::get_instance()->set( $publisher->ID, 'http://schema.org/Person' );

		Wordlift_Configuration_Service::get_instance()->set_publisher_id( $publisher->ID );



		$data = $this->publisher_service_instance->get();

		$this->assertEmpty( $data['page'] );
		$this->assertEquals( 'Person', $data['type'] );
		$this->assertEquals( 'Nicholas Cage', $data['name'] );
		$this->assertEquals( 'Just another WordPress site', $data['alt_name'] );
		$this->assertEmpty( $data['legal_name'] );
		$this->assertEmpty( $data['description'] );
		$this->assertEmpty( $data['url'] );
		$this->assertEmpty( $data['same_as'] );
		$this->assertEmpty( $data['address'] );
		$this->assertEmpty( $data['locality'] );
		$this->assertEmpty( $data['region'] );
		$this->assertEmpty( $data['country'] );
		$this->assertEmpty( $data['postal_code'] );
		$this->assertEmpty( $data['telephone'] );
		$this->assertEmpty( $data['email'] );
		$this->assertEmpty( $data['no_employees'] );
		$this->assertEmpty( $data['founding_date'] );
		$this->assertEmpty( $data['iso_6523'] );
		$this->assertEmpty( $data['naics'] );
		$this->assertEmpty( $data['global_loc_no'] );
		$this->assertEmpty( $data['vat_id'] );

		fwrite(STDERR, print_r($data, TRUE));
	}
}