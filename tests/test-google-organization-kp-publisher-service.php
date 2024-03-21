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
	 * The {@link Wordlift_Configuration_Service} instance to test.
	 *
	 * @var Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance to test.
	 */
	private $configuration_service;

	/**
	 * Set Up.
	 */
	public function setUp() {
		parent::setUp();

//		if ( ! apply_filters( 'wl_feature__enable__', false ) ) {
//			$this->markTestSkipped( 'Include/Exclude is not enabled.' );
//		}

		$this->publisher_service_instance = new Publisher_Service(
			Wordlift_Publisher_Service::get_instance(),
			Wordlift_Entity_Type_Service::get_instance(),
			Wordlift_Configuration_Service::get_instance()
		);

		$this->configuration_service = Wordlift_Configuration_Service::get_instance();

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
	 * Test get() method returns correct data on initial set up
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
	}

	/**
	 * Test get() method returns correct data after modification
	 **/
	public function test_publisher_service_get_method_returns_correct_data_after_modification() {
		// Create a post for an about page and set it in the configuration
		$about_post = $this->factory()->post->create_and_get();
		Wordlift_Configuration_Service::get_instance()->set_about_page_id( $about_post->ID );

		// Create a new `Person` publisher entity and set it in the configuration
		$publisher_post = $this->entity_factory->create_and_get( array(
			'post_title' => 'Nicholas Cage',
		) );

		Wordlift_Entity_Type_Service::get_instance()->set( $publisher_post->ID, 'http://schema.org/Person' );

		$this->configuration_service->set_publisher_id( $publisher_post->ID );

		// Set alt_name
		$this->configuration_service->set_alternate_name( 'Cagelas Nick' );

		// Set legal_name
		update_post_meta(
			$publisher_post->ID,
			Wordlift_Schema_Service::FIELD_LEGAL_NAME,
			'Mr. Nick Cage'
		);

		// Set description
		wp_update_post( array(
			'ID' => $publisher_post->ID,
			'post_content' => 'The human embodiment of a rollercoaster'
		) );

		// Set image
		$attachment_id = $this->factory()->attachment->create_upload_object( __DIR__ . '/assets/cat-1200x1200.jpg' );
		set_post_thumbnail( $publisher_post->ID, $attachment_id );

		// @todo: need some help to understand how to make this work.
//		$this->configuration_service->set_override_website_url( 'http://www.acme.inc' );

		// Set same_as
		$same_as = array(
			'https://abc.com',
			'https://123.com'
		);

		foreach( $same_as as $url ) {
			add_post_meta(
				$publisher_post->ID,
				Wordlift_Schema_Service::FIELD_SAME_AS,
				$url
			);
		}

		// Set address
		update_post_meta(
			$publisher_post->ID,
			Wordlift_Schema_Service::FIELD_ADDRESS,
			'Via Giulia 117'
		);

		// Set locality
		update_post_meta(
			$publisher_post->ID,
			Wordlift_Schema_Service::FIELD_ADDRESS_LOCALITY,
			'Roma'
		);

		// Set region
		update_post_meta(
			$publisher_post->ID,
			Wordlift_Schema_Service::FIELD_ADDRESS_REGION,
			'Lazio'
		);

		// Set country
		update_post_meta(
			$publisher_post->ID,
			Wordlift_Schema_Service::FIELD_ADDRESS_COUNTRY,
			'IT'
		);

		// Set postal code
		update_post_meta(
			$publisher_post->ID,
			Wordlift_Schema_Service::FIELD_ADDRESS_POSTAL_CODE,
			'00186'
		);

		// Set telephone
		update_post_meta(
			$publisher_post->ID,
			Wordlift_Schema_Service::FIELD_TELEPHONE,
			'+9876543210'
		);

		// Set email
		update_post_meta(
			$publisher_post->ID,
			Wordlift_Schema_Service::FIELD_EMAIL,
			'nick.cage@hollywood.com'
		);

		// Set no_of_employees
		update_post_meta(
			$publisher_post->ID,
			Wordlift_Schema_Service::FIELD_NO_OF_EMPLOYEES,
			'123'
		);

		// Set founding_date
		update_post_meta(
			$publisher_post->ID,
			Wordlift_Schema_Service::FIELD_FOUNDING_DATE,
			'1993-02-01'
		);

		// Set iso_6523
		update_post_meta(
			$publisher_post->ID,
			Wordlift_Schema_Service::FIELD_ISO_6523_CODE,
			'iso123'
		);

		// Set naics
		update_post_meta(
			$publisher_post->ID,
			Wordlift_Schema_Service::FIELD_NAICS,
			'naics123'
		);

		// Set global_loc_no
		update_post_meta(
			$publisher_post->ID,
			Wordlift_Schema_Service::FIELD_GLOBAL_LOCATION_NO,
			'glc123'
		);

		// Set vat_id
		update_post_meta(
			$publisher_post->ID,
			Wordlift_Schema_Service::FIELD_VAT_ID,
			'vat123'
		);

		// Set tax_id
		update_post_meta(
			$publisher_post->ID,
			Wordlift_Schema_Service::FIELD_TAX_ID,
			'tax123'
		);

		// Get the data from the Publisher service
		$data = $this->publisher_service_instance->get();

		// Check everything was correctly set
		$this->assertEquals( $about_post->ID, $data['page']['id'] );
		$this->assertEquals( $about_post->post_title, $data['page']['title'] );
		$this->assertEquals( 'Person', $data['type'] );
		$this->assertEquals( 'Nicholas Cage', $data['name'] );
		$this->assertEquals( 'Cagelas Nick', $data['alt_name'] );
		$this->assertEquals( 'Mr. Nick Cage', $data['legal_name'] );
		$this->assertEquals( 'The human embodiment of a rollercoaster', $data['description'] );
//		$this->assertEquals( 'http://www.acme.inc', $data['url'] );
		$this->assertEquals( $same_as, $data['same_as'] );
		$this->assertEquals( 'Via Giulia 117', $data['address'] );
		$this->assertEquals( 'Roma', $data['locality'] );
		$this->assertEquals( 'Lazio', $data['region'] );
		$this->assertEquals( 'IT', $data['country'] );
		$this->assertEquals( '00186', $data['postal_code'] );
		$this->assertEquals( '+9876543210', $data['telephone'] );
		$this->assertEquals( 'nick.cage@hollywood.com', $data['email'] );
		$this->assertEquals( '123', $data['no_employees'] );
		$this->assertEquals( '1993-02-01', $data['founding_date'] );
		$this->assertEquals( 'iso123', $data['iso_6523'] );
		$this->assertEquals( 'naics123', $data['naics'] );
		$this->assertEquals( 'glc123', $data['global_loc_no'] );
		$this->assertEquals( 'vat123', $data['vat_id'] );
		$this->assertEquals( 'tax123', $data['tax_id'] );
	}

	/**
	 * Test save() method throws errors with incorrect parameters provided
	 */
	public function test_save_method_errors_with_insufficient_params() {
		$this->expectExceptionMessage( 'Too few arguments' );
		$this->publisher_service_instance->save();

		$this->expectExceptionMessage( 'No parameters provided' );
		$this->publisher_service_instance->save( array() );
	}

	/**
	 * Test save() method throws error with required fields to create
	 * publisher not provided or invalid.
	 */
	public function test_save_method_errors_when_publisher_not_set() {
		$this->configuration_service->set_publisher_id("(none)");

		$this->expectExceptionMessage( 'Required parameters not provided' );
		$this->publisher_service_instance->save( array(
			'type' => 'Elephant'
		) );

		$this->expectExceptionMessage( 'Publisher type not valid' );
		$this->publisher_service_instance->save( array(
			'name' => 'Bo Jangles',
			'type' => 'Elephant'
		) );
	}

	/**
	 * Test POST method sets correct data
	 */
	public function test_publisher_service_post_method_sets_correct_data() {
		$about_post = $this->factory()->post->create_and_get();

		$mock_image_path = dirname( __FILE__ ) . '/assets/cat-800x600.jpg';
		$mock_image_data = array(
			'name' => basename( $mock_image_path ),
			'type' => mime_content_type( $mock_image_path ),
			'tmp_name' => $mock_image_path,
			'error' => 0,
			'size' => filesize( $mock_image_path )
		);

		$data = array(
			'page' => $about_post->ID,
			'type' => 'Organization',
			'name' => 'ACME inc.',
			'alt_name' => 'Wacky industries',
			'legal_name' => 'ACME inc.',
			'description' => 'Where Murphy\'s law meets corporate innovation.',
			'image' => $mock_image_data,
			'same_as' => array(
				'https://dontlookdown.com'
			),
			'address' => 'W road runner drive.',
			'locality' => 'Wiley Desert',
			'region' => 'Nevada',
			'country' => 'US',
			'postal_code' => '90210',
			'telephone' => '+18009876543210',
			'email' => 'roadrunnersucks@coyotesrule.com',
			'no_employees' => '1',
			'founding_date' => '1993-02-01',
			'iso_6523' => 'iso123',
			'naics' => 'naics123',
			'global_loc_no' => 'glc123',
			'vat_id' => 'vat123',
			'tax_id' => 'tax123'
		);

		$this->publisher_service_instance->save( $data );

		$publisher_id = $this->configuration_service->get_publisher_id();

		$this->assertEquals( $about_post->ID, $this->configuration_service->get_about_page_id() );

		$publisher_entity = Wordlift_Entity_Type_Service::get_instance()->get( $publisher_id );
		$this->assertEquals( 'Organization', $publisher_entity['label'] );

		$publisher_post = get_post( $publisher_id );
		$this->assertEquals( 'ACME inc.', $publisher_post->post_title );

		$this->assertEquals( 'Wacky industries', $this->configuration_service->get_alternate_name() );
		$this->assertEquals( 'ACME inc.', get_post_meta( $publisher_id, Wordlift_Schema_Service::FIELD_LEGAL_NAME, true ) );
		$this->assertEquals( 'Where Murphy\'s law meets corporate innovation.', $publisher_post->post_content );

//		$wp_upload_dir = wp_upload_dir();
//
//		$saved_file_path = esc_url( get_the_post_thumbnail_url( $publisher_id ) );
//		$saved_file_path = ltrim( $saved_file_path, $wp_upload_dir['baseurl'] );
//		$saved_file_path = $wp_upload_dir['basedir'] . '/' . $saved_file_path;
//
//		$images_are_the_same = file_get_contents( $mock_image_path ) === file_get_contents( $saved_file_path );
//		$this->assertTrue( $images_are_the_same );

		// saved image basename matches provided
		$saved_file_path = esc_url( get_the_post_thumbnail_url( $publisher_id ) );
		$this->assertContains( $mock_image_data['name'], $saved_file_path );

		// same_as matches 'https://dontlookdown.com'
		$same_as = get_post_meta( $publisher_id, Wordlift_Schema_Service::FIELD_SAME_AS, false );
		$this->assertIsArray( $same_as );
		$this->assertContains( 'https://dontlookdown.com', $same_as );

		$this->assertEquals( 'W road runner drive.', get_post_meta( $publisher_id, Wordlift_Schema_Service::FIELD_ADDRESS, true ) );
		$this->assertEquals( 'Wiley Desert', get_post_meta( $publisher_id, Wordlift_Schema_Service::FIELD_ADDRESS_LOCALITY, true ) );
		$this->assertEquals( 'Nevada', get_post_meta( $publisher_id, Wordlift_Schema_Service::FIELD_ADDRESS_REGION, true ) );
		$this->assertEquals( 'US', get_post_meta( $publisher_id, Wordlift_Schema_Service::FIELD_ADDRESS_COUNTRY, true ) );
		$this->assertEquals( '90210', get_post_meta( $publisher_id, Wordlift_Schema_Service::FIELD_ADDRESS_POSTAL_CODE, true ) );
		$this->assertEquals( 'roadrunnersucks@coyotesrule.com', get_post_meta( $publisher_id, Wordlift_Schema_Service::FIELD_EMAIL, true ) );
		$this->assertEquals( '+18009876543210', get_post_meta( $publisher_id, Wordlift_Schema_Service::FIELD_TELEPHONE, true ) );
		$this->assertEquals( '1', get_post_meta( $publisher_id, Wordlift_Schema_Service::FIELD_NO_OF_EMPLOYEES, true ) );
		$this->assertEquals( '1993-02-01', get_post_meta( $publisher_id, Wordlift_Schema_Service::FIELD_FOUNDING_DATE, true ) );
		$this->assertEquals( 'iso123', get_post_meta( $publisher_id, Wordlift_Schema_Service::FIELD_ISO_6523_CODE, true ) );
		$this->assertEquals( 'naics123', get_post_meta( $publisher_id, Wordlift_Schema_Service::FIELD_NAICS, true ) );
		$this->assertEquals( 'glc123', get_post_meta( $publisher_id, Wordlift_Schema_Service::FIELD_GLOBAL_LOCATION_NO, true ) );
		$this->assertEquals( 'vat123', get_post_meta( $publisher_id, Wordlift_Schema_Service::FIELD_VAT_ID, true ) );
		$this->assertEquals( 'tax123', get_post_meta( $publisher_id, Wordlift_Schema_Service::FIELD_TAX_ID, true ) );
	}

	/**
	 * Test POST method unsets value when empty
	 */
	public function test_publisher_service_post_method_unsets_when_empty() {
		$about_post = $this->factory()->post->create_and_get();
		$publisher_id = $this->configuration_service->get_publisher_id();

		$this->publisher_service_instance->save( array(
			'page' => $about_post->ID
		) );

		// about page ID matches test post
		$this->assertEquals( $about_post->ID, $this->configuration_service->get_about_page_id() );

		$this->publisher_service_instance->save( array(
			'page' => ''
		) );

		$this->assertEmpty( $this->configuration_service->get_about_page_id() );

		$data = $this->publisher_service_instance->get();

		$this->assertEmpty( $data['page'] );
	}
}