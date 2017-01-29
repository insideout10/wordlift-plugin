<?php
/**
 * Tests: JSON-LD Service Test.
 *
 * This file contains the tests for the {@link Wordlift_Jsonld_Service} class.
 *
 * @since   3.8.0
 * @package Wordlift
 */

/**
 * Define the test class.
 *
 * @since   3.8.0
 * @package Wordlift
 */
class Wordlift_Jsonld_Service_Test extends Wordlift_Ajax_Unit_Test_Case {

	/**
	 * A {@link Wordlift_Entity_Type_Service} instance.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var Wordlift_Entity_Type_Service $entity_type_service A {@link Wordlift_Entity_Type_Service} instance.
	 */
	private $entity_type_service;

	/**
	 * A {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var Wordlift_Entity_Service $entity_service A {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * A {@link Wordlift_Entity_Post_To_Jsonld_Converter} instance.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var Wordlift_Entity_Post_To_Jsonld_Converter $entity_post_to_jsonld_converter A {@link Wordlift_Entity_Post_To_Jsonld_Converter} instance.
	 */
	private $entity_post_to_jsonld_converter;

	/**
	 * A {@link Wordlift_Jsonld_Service} instance to test.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var Wordlift_Jsonld_Service $jsonld_service A {@link Wordlift_Jsonld_Service} instance.
	 */
	private $jsonld_service;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		// We don't need to check the remote Linked Data store.
		Wordlift_Unit_Test_Case::turn_off_entity_push();;

		$wordlift = new Wordlift_Test();

		$this->entity_type_service             = $wordlift->get_entity_type_service();
		$this->entity_service                  = $wordlift->get_entity_service();
		$this->entity_post_to_jsonld_converter = $wordlift->get_entity_post_to_jsonld_converter();
		$this->jsonld_service                  = $wordlift->get_jsonld_service();

	}

	/**
	 * Test that the JSON-LD is an empty array when no URIs have been provided.
	 *
	 * @since 3.8.0
	 */
	public function test_jsonld_no_uris() {

		// Set up a default request
		$_GET['action'] = 'wl_jsonld';

		// Make the request
		try {
			$this->_handleAjax( 'wl_jsonld' );
		} catch ( WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		$jsonld = json_decode( $this->_last_response );
		$this->assertTrue( is_array( $jsonld ) );
		$this->assertCount( 0, $jsonld );

	}

	/**
	 * Test that the JSON-LD.
	 *
	 * @since 3.8.0
	 */
	public function test_jsonld() {

		// Create a location entity post and bind it to the location property.
		$name              = rand_str();
		$local_business_id = $this->factory->post->create( array(
			'post_title' => $name,
			'post_type'  => 'entity',
		) );
		$this->entity_type_service->set( $local_business_id, 'http://schema.org/LocalBusiness' );
		$local_business_uri = $this->entity_service->get_uri( $local_business_id );

		// Set the geo coordinates.
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_GEO_LATITUDE, 12.34 );
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_GEO_LONGITUDE, 1.23 );

		$email = rand_str();
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_EMAIL, $email );

		$phone = rand_str();
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_TELEPHONE, $phone );

		// Set a random sameAs.
		$same_as = 'http://example.org/aRandomSameAs';
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_SAME_AS, $same_as );

		$street_address = rand_str();
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_ADDRESS, $street_address );

		$po_box = rand_str();
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_ADDRESS_PO_BOX, $po_box );

		$postal_code = rand_str();
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_ADDRESS_POSTAL_CODE, $postal_code );

		$locality = rand_str();
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_ADDRESS_LOCALITY, $locality );

		$region = rand_str();
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_ADDRESS_REGION, $region );

		$country = rand_str();
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_ADDRESS_COUNTRY, $country );

		$person_id = $this->factory->post->create( array( 'post_type' => 'entity', ) );
		$this->entity_type_service->set( $person_id, 'http://schema.org/Person' );
		$person_uri = $this->entity_service->get_uri( $person_id );

		// Bind the person as author of the creative work.
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_FOUNDER, $person_id );

		// Set up a default request
		$_GET['action'] = 'wl_jsonld';
		$_GET['id']     = $local_business_id;

		// Make the request
		try {
			$this->_handleAjax( 'wl_jsonld' );
		} catch ( WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		$response = json_decode( $this->_last_response );

		$this->assertTrue( is_array( $response ) );
		$this->assertCount( 2, $response );

		$jsonld_1 = get_object_vars( $response[0] );

		$this->assertTrue( is_array( $jsonld_1 ) );
		$this->assertArrayHasKey( '@context', $jsonld_1 );
		$this->assertEquals( 'http://schema.org', $jsonld_1['@context'] );

		$this->assertArrayHasKey( '@id', $jsonld_1 );
		$this->assertEquals( $local_business_uri, $jsonld_1['@id'] );

		$this->assertArrayHasKey( '@type', $jsonld_1 );
		$this->assertEquals( 'LocalBusiness', $jsonld_1['@type'] );

		$this->assertArrayHasKey( 'name', $jsonld_1 );
		$this->assertEquals( $name, $jsonld_1['name'] );

		$this->assertArrayHasKey( 'url', $jsonld_1 );
		$this->assertEquals( get_permalink( $local_business_id ), $jsonld_1['url'] );

		$this->assertArrayHasKey( 'sameAs', $jsonld_1 );
		$this->assertEquals( $same_as, $jsonld_1['sameAs'] );

		$this->assertArrayHasKey( 'email', $jsonld_1 );
		$this->assertEquals( $email, $jsonld_1['email'] );

		$this->assertArrayHasKey( 'telephone', $jsonld_1 );
		$this->assertEquals( $phone, $jsonld_1['telephone'] );

		$this->assertArrayHasKey( 'geo', $jsonld_1 );

		$geo = get_object_vars( $jsonld_1['geo'] );
		$this->assertArrayHasKey( '@type', $geo );
		$this->assertEquals( 'GeoCoordinates', $geo['@type'] );

		$this->assertArrayHasKey( 'latitude', $geo );
		$this->assertEquals( 12.34, $geo['latitude'] );

		$this->assertArrayHasKey( 'longitude', $geo );
		$this->assertEquals( 1.23, $geo['longitude'] );


		$this->assertArrayHasKey( 'address', $jsonld_1 );

		$address = get_object_vars( $jsonld_1['address'] );
		$this->assertArrayHasKey( '@type', $address );
		$this->assertEquals( 'PostalAddress', $address['@type'] );

		$this->assertEquals( $street_address, $address['streetAddress'] );
		$this->assertEquals( $po_box, $address['postOfficeBoxNumber'] );
		$this->assertEquals( $postal_code, $address['postalCode'] );
		$this->assertEquals( $locality, $address['addressLocality'] );
		$this->assertEquals( $region, $address['addressRegion'] );
		$this->assertEquals( $country, $address['addressCountry'] );

		$this->assertArrayHasKey( 'founder', $jsonld_1 );

		$founder = get_object_vars( $jsonld_1['founder'] );
		$this->assertArrayHasKey( '@id', $founder );
		$this->assertEquals( $person_uri, $founder['@id'] );


		$jsonld_2 = get_object_vars( $response[1] );

		$this->assertTrue( is_array( $jsonld_2 ) );
		$this->assertArrayHasKey( '@context', $jsonld_2 );
		$this->assertEquals( 'http://schema.org', $jsonld_2['@context'] );

		$this->assertArrayHasKey( '@id', $jsonld_2 );
		$this->assertEquals( $person_uri, $jsonld_2['@id'] );

		$this->assertArrayHasKey( '@type', $jsonld_2 );
		$this->assertEquals( 'Person', $jsonld_2['@type'] );

		$this->assertArrayHasKey( 'name', $jsonld_2 );

		$this->assertArrayHasKey( 'url', $jsonld_2 );
		$this->assertEquals( get_permalink( $person_id ), $jsonld_2['url'] );

	}

}
