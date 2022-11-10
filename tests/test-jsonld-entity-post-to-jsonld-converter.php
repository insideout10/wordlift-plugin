<?php
/**
 * Tests: Entity Post to JSON-LD Converter Test.
 *
 * This file defines tests for the {@link Wordlift_Entity_Post_To_Jsonld_Converter} class.
 *
 * @since   3.8.
 * @package Wordlift
 */

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;
use Wordlift\Jsonld\Reference;

/**
 * Test the {@link Wordlift_Entity_Post_To_Jsonld_Converter} class.
 *
 * @since   3.8.0
 * @package Wordlift
 * @group jsonld
 */
class Wordlift_Jsonld_Entity_Post_To_Jsonld_Converter_Test extends Wordlift_Unit_Test_Case {

	/**
	 * A {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var Wordlift_Entity_Service $entity_service A {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * The {@link Wordlift_Entity_Post_To_Jsonld_Converter} to test.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var Wordlift_Entity_Post_To_Jsonld_Converter $entity_post_to_jsonld_converter A {@link Wordlift_Entity_Post_To_Jsonld_Converter} instance.
	 */
	private $entity_post_to_jsonld_converter;

	/**
	 * A {@link Wordlift_Postid_To_Jsonld_Converter} instance to test.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var \Wordlift_Postid_To_Jsonld_Converter $postid_to_jsonld_converter A {@link Wordlift_Postid_To_Jsonld_Converter} instance.
	 */
	private $postid_to_jsonld_converter;


	public function convert( $item ) {
		if ( $item instanceof Reference ) {
			return $item->get_id();
		}
		return $item;
	}

	public function convert_references( $references ) {
		return array_map( function ( $reference ) {
			return $this->convert( $reference );
		}, $references );
	}


	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		// Disable sending SPARQL queries, since we don't need it.
		Wordlift_Unit_Test_Case::turn_off_entity_push();

		$this->entity_service = Wordlift_Entity_Service::get_instance();

		$property_getter          = Wordlift_Property_Getter_Factory::create();
		$post_to_jsonld_converter = new Wordlift_Post_To_Jsonld_Converter(
			Wordlift_Entity_Type_Service::get_instance(),
			Wordlift_User_Service::get_instance(),
			Wordlift_Attachment_Service::get_instance()
		);

		$this->entity_post_to_jsonld_converter = new Wordlift_Entity_Post_To_Jsonld_Converter(
			Wordlift_Entity_Type_Service::get_instance(),
			Wordlift_User_Service::get_instance(),
			Wordlift_Attachment_Service::get_instance(),
			$property_getter,
			Wordlift_Schemaorg_Property_Service::get_instance(),
			$post_to_jsonld_converter
		);

		$this->postid_to_jsonld_converter = new Wordlift_Postid_To_Jsonld_Converter(
			$this->entity_post_to_jsonld_converter,
			$post_to_jsonld_converter
		);
	}

	/**
	 * Test the conversion of an event entity {@link WP_Post} to a JSON-LD array.
	 *
	 * @since 3.8.0
	 */
	public function test_event_conversion() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		// Create an entity post and assign it the Event type.
		$name     = 'Test Entity Post to Json-Ld conversion test_event_conversion ' . rand_str();
		$event_id = $this->factory->post->create(
			array(
				'post_title' => $name,
				'post_type'  => 'entity',
			)
		);
		Wordlift_Entity_Type_Service::get_instance()->set( $event_id, 'http://schema.org/Event' );
		$event_uri = $this->entity_service->get_uri( $event_id );

		// Set the start date.
		$start_date = date( 'Y/m/d', 1576800000 );
		add_post_meta( $event_id, Wordlift_Schema_Service::FIELD_DATE_START, $start_date );

		// Set the end date.
		$end_date = date( 'Y/m/d', 3153600000 );
		add_post_meta( $event_id, Wordlift_Schema_Service::FIELD_DATE_END, $end_date );

		// Set a random sameAs.
		$same_as = 'http://example.org/aRandomSameAs';
		add_post_meta( $event_id, Wordlift_Schema_Service::FIELD_SAME_AS, $same_as );

		// Create a location entity post and bind it to the location property.
		$place_id = $this->factory->post->create( array( 'post_type' => 'entity' ) );
		Wordlift_Entity_Type_Service::get_instance()->set( $place_id, 'http://schema.org/Place' );
		$place_uri = $this->entity_service->get_uri( $place_id );

		// Bind the place to the location property.
		add_post_meta( $event_id, Wordlift_Schema_Service::FIELD_LOCATION, $place_id );

		// Create a person entity post and bind it to the performer property.
		$performer_id = $this->factory->post->create( array( 'post_type' => 'entity' ) );
		Wordlift_Entity_Type_Service::get_instance()->set( $performer_id, 'http://schema.org/Person' );
		$performer_uri = $this->entity_service->get_uri( $performer_id );

		// Bind the person to the performer property.
		add_post_meta( $event_id, Wordlift_Schema_Service::FIELD_PERFORMER, $performer_id );

		// Create a offer entity post and bind it to the offers property.
		$offer_id = $this->factory->post->create( array( 'post_type' => 'entity' ) );
		Wordlift_Entity_Type_Service::get_instance()->set( $offer_id, 'http://schema.org/Offer' );
		$offer_uri = $this->entity_service->get_uri( $offer_id );

		// Bind the offer to the offers property.
		add_post_meta( $event_id, Wordlift_Schema_Service::FIELD_OFFERS, $offer_id );

		$post       = get_post( $event_id );
		$references = array();
		$jsonld     = $this->entity_post_to_jsonld_converter->convert( $post->ID, $references );

		$this->assertTrue( is_array( $jsonld ) );
		$this->assertArrayHasKey( '@context', $jsonld );
		$this->assertEquals( 'http://schema.org', $jsonld['@context'] );

		$this->assertArrayHasKey( '@id', $jsonld );
		$this->assertEquals( $event_uri, $jsonld['@id'] );

		$this->assertArrayHasKey( '@type', $jsonld );
		$this->assertEquals( 'Event', $jsonld['@type'] );

		$this->assertArrayHasKey( 'name', $jsonld );
		$this->assertEquals( $name, $jsonld['name'] );

		$this->assertArrayHasKey( 'url', $jsonld );
		$this->assertEquals( get_permalink( $event_id ), $jsonld['url'] );

		$this->assertArrayHasKey( 'startDate', $jsonld );
		$this->assertEquals( $start_date, $jsonld['startDate'] );

		$this->assertArrayHasKey( 'endDate', $jsonld );
		$this->assertEquals( $end_date, $jsonld['endDate'] );

		$this->assertArrayHasKey( 'sameAs', $jsonld );
		$this->assertEquals( $same_as, $jsonld['sameAs'] );

		$this->assertArrayHasKey( 'performer', $jsonld );
		$this->assertArrayHasKey( '@id', $jsonld['performer'] );
		$this->assertEquals( $performer_uri, $jsonld['performer']['@id'] );

		$this->assertArrayHasKey( 'offers', $jsonld );
		$this->assertArrayHasKey( '@id', $jsonld['offers'] );
		$this->assertEquals( $offer_uri, $jsonld['offers']['@id'] );

		$this->assertArrayHasKey( 'location', $jsonld );
		$this->assertArrayHasKey( '@id', $jsonld['location'] );
		$this->assertEquals( $place_uri, $jsonld['location']['@id'] );

		$references = array_map( array( $this, 'convert'), $references );



		$this->assertContains( $place_id, $references );

		$this->assertFalse( isset( $jsonld['alternateName'] ) );

		$references_2 = array();
		$this->assertEquals( $jsonld, $this->postid_to_jsonld_converter->convert( $event_id, $references_2 ) );
		$references_2 = array_map(
			array( $this, 'convert'),
			$references_2
		);
		$this->assertEquals( $references, $references_2 );

	}

	/**
	 * Test the conversion of an place entity {@link WP_Post} to a JSON-LD array.
	 *
	 * @since 3.8.0
	 */
	public function test_place_conversion() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		// Create a location entity post and bind it to the location property.
		$name     = rand_str();
		$place_id = $this->factory->post->create(
			array(
				'post_title' => $name,
				'post_type'  => 'entity',
			)
		);
		Wordlift_Entity_Type_Service::get_instance()->set( $place_id, 'http://schema.org/Place' );
		$place_uri = $this->entity_service->get_uri( $place_id );

		// Set a random sameAs.
		$same_as = 'http://example.org/aRandomSameAs';
		add_post_meta( $place_id, Wordlift_Schema_Service::FIELD_SAME_AS, $same_as );

		// Set the geo coordinates.
		add_post_meta( $place_id, Wordlift_Schema_Service::FIELD_GEO_LATITUDE, 12.34 );
		add_post_meta( $place_id, Wordlift_Schema_Service::FIELD_GEO_LONGITUDE, 1.23 );

		$address = rand_str();
		add_post_meta( $place_id, Wordlift_Schema_Service::FIELD_ADDRESS, $address );

		$po_box = rand_str();
		add_post_meta( $place_id, Wordlift_Schema_Service::FIELD_ADDRESS_PO_BOX, $po_box );

		$postal_code = rand_str();
		add_post_meta( $place_id, Wordlift_Schema_Service::FIELD_ADDRESS_POSTAL_CODE, $postal_code );

		$locality = rand_str();
		add_post_meta( $place_id, Wordlift_Schema_Service::FIELD_ADDRESS_LOCALITY, $locality );

		$region = rand_str();
		add_post_meta( $place_id, Wordlift_Schema_Service::FIELD_ADDRESS_REGION, $region );

		$country = rand_str();
		add_post_meta( $place_id, Wordlift_Schema_Service::FIELD_ADDRESS_COUNTRY, $country );

		// Set the alternative names.
		$alternate_labels = array( rand_str(), rand_str() );
		$this->entity_service->set_alternative_labels( $place_id, $alternate_labels );

		$post       = get_post( $place_id );
		$references = array();
		$jsonld     = $this->entity_post_to_jsonld_converter->convert( $post->ID, $references );

		$this->assertTrue( is_array( $jsonld ) );
		$this->assertArrayHasKey( '@context', $jsonld );
		$this->assertEquals( 'http://schema.org', $jsonld['@context'] );

		$this->assertArrayHasKey( '@id', $jsonld );
		$this->assertEquals( $place_uri, $jsonld['@id'] );

		$this->assertArrayHasKey( '@type', $jsonld );
		$this->assertEquals( 'Place', $jsonld['@type'] );

		$this->assertArrayHasKey( 'name', $jsonld );
		$this->assertEquals( $name, $jsonld['name'] );

		$this->assertArrayHasKey( 'url', $jsonld );
		$this->assertEquals( get_permalink( $place_id ), $jsonld['url'] );

		$this->assertArrayHasKey( 'sameAs', $jsonld );
		$this->assertEquals( $same_as, $jsonld['sameAs'] );

		$this->assertArrayHasKey( 'geo', $jsonld );

		$this->assertArrayHasKey( '@type', $jsonld['geo'] );
		$this->assertEquals( 'GeoCoordinates', $jsonld['geo']['@type'] );

		$this->assertArrayHasKey( 'latitude', $jsonld['geo'] );
		$this->assertEquals( 12.34, $jsonld['geo']['latitude'] );

		$this->assertArrayHasKey( 'longitude', $jsonld['geo'] );
		$this->assertEquals( 1.23, $jsonld['geo']['longitude'] );

		$this->assertArrayHasKey( 'address', $jsonld );

		$this->assertArrayHasKey( '@type', $jsonld['address'] );
		$this->assertEquals( 'PostalAddress', $jsonld['address']['@type'] );

		$this->assertEquals( $address, $jsonld['address']['streetAddress'] );
		$this->assertEquals( $po_box, $jsonld['address']['postOfficeBoxNumber'] );
		$this->assertEquals( $postal_code, $jsonld['address']['postalCode'] );
		$this->assertEquals( $locality, $jsonld['address']['addressLocality'] );
		$this->assertEquals( $region, $jsonld['address']['addressRegion'] );
		$this->assertEquals( $country, $jsonld['address']['addressCountry'] );

		$this->assertEquals( $alternate_labels, $jsonld['alternateName'] );

		$references_2 = array();
		$this->assertEquals( $jsonld, $this->postid_to_jsonld_converter->convert( $place_id, $references_2 ) );
		$this->assertEquals( $references, $references_2 );

	}

	/**
	 * Test the conversion of an creative work entity {@link WP_Post} to a JSON-LD array.
	 *
	 * @since 3.8.0
	 */
	public function test_create_work_conversion() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		// Create a location entity post and bind it to the location property.
		$name           = rand_str();
		$create_work_id = $this->factory->post->create(
			array(
				'post_title' => $name,
				'post_type'  => 'entity',
			)
		);
		Wordlift_Entity_Type_Service::get_instance()->set( $create_work_id, 'http://schema.org/CreativeWork' );
		$create_work_uri = $this->entity_service->get_uri( $create_work_id );

		// Set a random sameAs.
		$same_as = 'http://example.org/aRandomSameAs';
		add_post_meta( $create_work_id, Wordlift_Schema_Service::FIELD_SAME_AS, $same_as );

		$person_id = $this->factory->post->create( array( 'post_type' => 'entity' ) );
		Wordlift_Entity_Type_Service::get_instance()->set( $person_id, 'http://schema.org/Person' );
		$person_uri = $this->entity_service->get_uri( $person_id );

		// Bind the person as author of the creative work.
		add_post_meta( $create_work_id, Wordlift_Schema_Service::FIELD_AUTHOR, $person_id );

		$post       = get_post( $create_work_id );
		$references = array();
		$jsonld     = $this->entity_post_to_jsonld_converter->convert( $post->ID, $references );

		$this->assertTrue( is_array( $jsonld ) );
		$this->assertArrayHasKey( '@context', $jsonld );
		$this->assertEquals( 'http://schema.org', $jsonld['@context'] );

		$this->assertArrayHasKey( '@id', $jsonld );
		$this->assertEquals( $create_work_uri, $jsonld['@id'] );

		$this->assertArrayHasKey( '@type', $jsonld );
		$this->assertEquals( 'CreativeWork', $jsonld['@type'] );

		$this->assertArrayHasKey( 'name', $jsonld );
		$this->assertEquals( $name, $jsonld['name'] );

		$this->assertArrayHasKey( 'url', $jsonld );
		$this->assertEquals( get_permalink( $create_work_id ), $jsonld['url'] );

		$this->assertArrayHasKey( 'sameAs', $jsonld );
		$this->assertEquals( $same_as, $jsonld['sameAs'] );

		$this->assertArrayHasKey( 'author', $jsonld );

		$this->assertArrayHasKey( '@id', $jsonld['author'] );
		$this->assertEquals( $person_uri, $jsonld['author']['@id'] );

		$this->assertContains( $person_id, $this->convert_references( $references ) );

		$references_2 = array();
		$this->assertEquals( $jsonld, $this->postid_to_jsonld_converter->convert( $create_work_id, $references_2 ) );
		$this->assertEquals( $this->convert_references( $references ), $this->convert_references( $references_2 ) );

	}

	/**
	 * Test the conversion of an organization entity {@link WP_Post} to a JSON-LD array.
	 *
	 * @since 3.8.0
	 */
	public function test_organization_conversion() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		// Create a location entity post and bind it to the location property.
		$name            = rand_str();
		$organization_id = $this->factory->post->create(
			array(
				'post_title' => $name,
				'post_type'  => 'entity',
			)
		);
		Wordlift_Entity_Type_Service::get_instance()->set( $organization_id, 'http://schema.org/Organization' );
		$organization_uri = $this->entity_service->get_uri( $organization_id );

		$email = rand_str();
		add_post_meta( $organization_id, Wordlift_Schema_Service::FIELD_EMAIL, $email );

		$phone = rand_str();
		add_post_meta( $organization_id, Wordlift_Schema_Service::FIELD_TELEPHONE, $phone );

		// Set a random sameAs.
		$same_as = 'http://example.org/aRandomSameAs';
		add_post_meta( $organization_id, Wordlift_Schema_Service::FIELD_SAME_AS, $same_as );

		$address = rand_str();
		add_post_meta( $organization_id, Wordlift_Schema_Service::FIELD_ADDRESS, $address );

		$po_box = rand_str();
		add_post_meta( $organization_id, Wordlift_Schema_Service::FIELD_ADDRESS_PO_BOX, $po_box );

		$postal_code = rand_str();
		add_post_meta( $organization_id, Wordlift_Schema_Service::FIELD_ADDRESS_POSTAL_CODE, $postal_code );

		$locality = rand_str();
		add_post_meta( $organization_id, Wordlift_Schema_Service::FIELD_ADDRESS_LOCALITY, $locality );

		$region = rand_str();
		add_post_meta( $organization_id, Wordlift_Schema_Service::FIELD_ADDRESS_REGION, $region );

		$country = rand_str();
		add_post_meta( $organization_id, Wordlift_Schema_Service::FIELD_ADDRESS_COUNTRY, $country );

		$person_id = $this->factory->post->create( array( 'post_type' => 'entity' ) );
		Wordlift_Entity_Type_Service::get_instance()->set( $person_id, 'http://schema.org/Person' );
		$person_uri = $this->entity_service->get_uri( $person_id );

		// Bind the person as author of the creative work.
		add_post_meta( $organization_id, Wordlift_Schema_Service::FIELD_FOUNDER, $person_id );

		$post       = get_post( $organization_id );
		$references = array();
		$jsonld     = $this->entity_post_to_jsonld_converter->convert( $post->ID, $references );

		$this->assertTrue( is_array( $jsonld ) );
		$this->assertArrayHasKey( '@context', $jsonld );
		$this->assertEquals( 'http://schema.org', $jsonld['@context'] );

		$this->assertArrayHasKey( '@id', $jsonld );
		$this->assertEquals( $organization_uri, $jsonld['@id'] );

		$this->assertArrayHasKey( '@type', $jsonld );
		$this->assertEquals( 'Organization', $jsonld['@type'] );

		$this->assertArrayHasKey( 'name', $jsonld );
		$this->assertEquals( $name, $jsonld['name'] );

		$this->assertArrayHasKey( 'url', $jsonld );
		$this->assertEquals( get_permalink( $organization_id ), $jsonld['url'] );

		$this->assertArrayHasKey( 'sameAs', $jsonld );
		$this->assertEquals( $same_as, $jsonld['sameAs'] );

		$this->assertArrayHasKey( 'email', $jsonld );
		$this->assertEquals( $email, $jsonld['email'] );

		$this->assertArrayHasKey( 'telephone', $jsonld );
		$this->assertEquals( $phone, $jsonld['telephone'] );

		$this->assertArrayHasKey( 'address', $jsonld );

		$this->assertArrayHasKey( '@type', $jsonld['address'] );
		$this->assertEquals( 'PostalAddress', $jsonld['address']['@type'] );

		$this->assertEquals( $address, $jsonld['address']['streetAddress'] );
		$this->assertEquals( $po_box, $jsonld['address']['postOfficeBoxNumber'] );
		$this->assertEquals( $postal_code, $jsonld['address']['postalCode'] );
		$this->assertEquals( $locality, $jsonld['address']['addressLocality'] );
		$this->assertEquals( $region, $jsonld['address']['addressRegion'] );
		$this->assertEquals( $country, $jsonld['address']['addressCountry'] );

		$this->assertArrayHasKey( 'founder', $jsonld );

		$this->assertArrayHasKey( '@id', $jsonld['founder'] );
		$this->assertEquals( $person_uri, $jsonld['founder']['@id'] );

		$this->assertContains( $person_id, $this->convert_references( $references ) );

		$references_2 = array();
		$this->assertEquals( $jsonld, $this->postid_to_jsonld_converter->convert( $organization_id, $references_2 ) );
		$this->assertEquals( $this->convert_references( $references ),  $this->convert_references( $references_2 ) );

	}

	/**
	 * Test the conversion of a person entity {@link WP_Post} to a JSON-LD array.
	 *
	 * @since 3.8.0
	 */
	public function test_person_conversion() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		// Create an entity post and assign it the Event type.
		$name      = rand_str();
		$person_id = $this->factory->post->create(
			array(
				'post_title' => $name,
				'post_type'  => 'entity',
			)
		);
		Wordlift_Entity_Type_Service::get_instance()->set( $person_id, 'http://schema.org/Person' );
		$person_uri = $this->entity_service->get_uri( $person_id );

		$email = rand_str();
		add_post_meta( $person_id, Wordlift_Schema_Service::FIELD_EMAIL, $email );

		// Set the start date.
		$birth_date = date( 'Y/m/d', 1576800000 );
		add_post_meta( $person_id, Wordlift_Schema_Service::FIELD_BIRTH_DATE, $birth_date );

		// Set a random sameAs.
		$same_as = 'http://example.org/aRandomSameAs';
		add_post_meta( $person_id, Wordlift_Schema_Service::FIELD_SAME_AS, $same_as );

		// Create a location entity post and bind it to the location property.
		$place_id = $this->factory->post->create( array( 'post_type' => 'entity' ) );
		Wordlift_Entity_Type_Service::get_instance()->set( $place_id, 'http://schema.org/Place' );
		$place_uri = $this->entity_service->get_uri( $place_id );

		// Bind the place to the birth place property.
		add_post_meta( $person_id, Wordlift_Schema_Service::FIELD_BIRTH_PLACE, $place_id );

		// Create a knows connection.
		$knows_id_1 = $this->factory->post->create( array( 'post_type' => 'entity' ) );
		Wordlift_Entity_Type_Service::get_instance()->set( $knows_id_1, 'http://schema.org/Person' );
		$knows_uri_1 = $this->entity_service->get_uri( $knows_id_1 );

		// Bind the knows to the person.
		add_post_meta( $person_id, Wordlift_Schema_Service::FIELD_KNOWS, $knows_id_1 );

		// Create a knows connection.
		$knows_id_2 = $this->factory->post->create( array( 'post_type' => 'entity' ) );
		Wordlift_Entity_Type_Service::get_instance()->set( $knows_id_2, 'http://schema.org/Person' );
		$knows_uri_2 = $this->entity_service->get_uri( $knows_id_2 );

		// Bind the knows to the person.
		add_post_meta( $person_id, Wordlift_Schema_Service::FIELD_KNOWS, $knows_id_2 );

		// Create a knows connection.
		$organization_id = $this->factory->post->create( array( 'post_type' => 'entity' ) );
		Wordlift_Entity_Type_Service::get_instance()->set( $organization_id, 'http://schema.org/Organization' );
		$organization_id = $this->entity_service->get_uri( $organization_id );

		// Bind the knows to the person.
		add_post_meta( $person_id, Wordlift_Schema_Service::FIELD_AFFILIATION, $organization_id );

		$post       = get_post( $person_id );
		$references = array();
		$jsonld     = $this->entity_post_to_jsonld_converter->convert( $post->ID, $references );

		$this->assertTrue( is_array( $jsonld ) );
		$this->assertArrayHasKey( '@context', $jsonld );
		$this->assertEquals( 'http://schema.org', $jsonld['@context'] );

		$this->assertArrayHasKey( '@id', $jsonld );
		$this->assertEquals( $person_uri, $jsonld['@id'] );

		$this->assertArrayHasKey( '@type', $jsonld );
		$this->assertEquals( 'Person', $jsonld['@type'] );

		$this->assertArrayHasKey( 'name', $jsonld );
		$this->assertEquals( $name, $jsonld['name'] );

		$this->assertArrayHasKey( 'url', $jsonld );
		$this->assertEquals( get_permalink( $person_id ), $jsonld['url'] );

		$this->assertArrayHasKey( 'birthDate', $jsonld );
		$this->assertEquals( $birth_date, $jsonld['birthDate'] );

		$this->assertArrayHasKey( 'email', $jsonld );
		$this->assertEquals( $email, $jsonld['email'] );

		$this->assertArrayHasKey( 'sameAs', $jsonld );
		$this->assertEquals( $same_as, $jsonld['sameAs'] );

		$this->assertArrayHasKey( 'birthPlace', $jsonld );
		$this->assertArrayHasKey( '@id', $jsonld['birthPlace'] );
		$this->assertEquals( $place_uri, $jsonld['birthPlace']['@id'] );

		$this->assertContains( $place_id, $this->convert_references( $references ) );

		$this->assertCount( 2, $jsonld['knows'] );

		$this->assertContains( array( '@id' => $knows_uri_1 ), $jsonld['knows'] );
		$this->assertContains( array( '@id' => $knows_uri_2 ), $jsonld['knows'] );

		$references_2 = array();
		$this->assertEquals( $jsonld, $this->postid_to_jsonld_converter->convert( $person_id, $references_2 ) );
		$this->assertEquals( $this->convert_references( $references ), $this->convert_references( $references_2 ) );

	}

	/**
	 * Test the conversion of a local business entity {@link WP_Post} to a JSON-LD array.
	 *
	 * @since 3.8.0
	 */
	public function test_local_business_conversion() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		// Create a location entity post and bind it to the location property.
		$name              = rand_str();
		$local_business_id = $this->factory->post->create(
			array(
				'post_title' => $name,
				'post_type'  => 'entity',
			)
		);
		Wordlift_Entity_Type_Service::get_instance()->set( $local_business_id, 'http://schema.org/LocalBusiness' );
		$local_business_type = Wordlift_Entity_Type_Service::get_instance()->get( $local_business_id );
		$this->assertEquals( 'http://schema.org/LocalBusiness', $local_business_type['uri'], 'Entity type must be http://schema.org/Person.' );

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

		$address = rand_str();
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_ADDRESS, $address );

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

		$person_id = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		Wordlift_Entity_Type_Service::get_instance()->set( $person_id, 'http://schema.org/Person' );
		$person_uri  = $this->entity_service->get_uri( $person_id );
		$person_type = Wordlift_Entity_Type_Service::get_instance()->get( $person_id );
		$this->assertEquals( 'http://schema.org/Person', $person_type['uri'], 'Entity type must be http://schema.org/Person.' );

		// Bind the person as author of the creative work.
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_FOUNDER, $person_id );

		$post       = get_post( $local_business_id );
		$references = array();
		$jsonld     = $this->entity_post_to_jsonld_converter->convert( $post->ID, $references );

		$this->assertTrue( is_array( $jsonld ) );
		$this->assertArrayHasKey( '@context', $jsonld );
		$this->assertEquals( 'http://schema.org', $jsonld['@context'] );

		$this->assertArrayHasKey( '@id', $jsonld );
		$this->assertEquals( $local_business_uri, $jsonld['@id'] );

		$this->assertArrayHasKey( '@type', $jsonld );
		$this->assertEquals( 'LocalBusiness', $jsonld['@type'] );

		$this->assertArrayHasKey( 'name', $jsonld );
		$this->assertEquals( $name, $jsonld['name'] );

		$this->assertArrayHasKey( 'url', $jsonld );
		$this->assertEquals( get_permalink( $local_business_id ), $jsonld['url'] );

		$this->assertArrayHasKey( 'sameAs', $jsonld );
		$this->assertEquals( $same_as, $jsonld['sameAs'] );

		$this->assertArrayHasKey( 'email', $jsonld );
		$this->assertEquals( $email, $jsonld['email'] );

		$this->assertArrayHasKey( 'telephone', $jsonld );
		$this->assertEquals( $phone, $jsonld['telephone'] );

		$this->assertArrayHasKey( 'geo', $jsonld );

		$this->assertArrayHasKey( '@type', $jsonld['geo'] );
		$this->assertEquals( 'GeoCoordinates', $jsonld['geo']['@type'] );

		$this->assertArrayHasKey( 'latitude', $jsonld['geo'] );
		$this->assertEquals( 12.34, $jsonld['geo']['latitude'] );

		$this->assertArrayHasKey( 'longitude', $jsonld['geo'] );
		$this->assertEquals( 1.23, $jsonld['geo']['longitude'] );

		$this->assertArrayHasKey( 'address', $jsonld );

		$this->assertArrayHasKey( '@type', $jsonld['address'] );
		$this->assertEquals( 'PostalAddress', $jsonld['address']['@type'] );

		$this->assertEquals( $address, $jsonld['address']['streetAddress'] );
		$this->assertEquals( $po_box, $jsonld['address']['postOfficeBoxNumber'] );
		$this->assertEquals( $postal_code, $jsonld['address']['postalCode'] );
		$this->assertEquals( $locality, $jsonld['address']['addressLocality'] );
		$this->assertEquals( $region, $jsonld['address']['addressRegion'] );
		$this->assertEquals( $country, $jsonld['address']['addressCountry'] );

		$this->assertArrayHasKey( 'founder', $jsonld );

		$this->assertArrayHasKey( '@id', $jsonld['founder'] );
		$this->assertEquals( $person_uri, $jsonld['founder']['@id'] );

		$this->assertContains( $person_id, $this->convert_references( $references ) );

		$references_2 = array();
		$this->assertEquals( $jsonld, $this->postid_to_jsonld_converter->convert( $local_business_id, $references_2 ) );
		$this->assertEquals( $this->convert_references( $references ), $this->convert_references( $references_2 ) );

	}

	/**
	 * Test the conversion of an offer entity {@link WP_Post} to a JSON-LD array.
	 *
	 * @since 3.18.0
	 *
	 */
	public function test_offer_conversion() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		// Create an entity post and assign it the Offer type.
		$name     = rand_str();
		$offer_id = $this->factory->post->create(
			array(
				'post_title' => $name,
				'post_type'  => 'entity',
			)
		);
		Wordlift_Entity_Type_Service::get_instance()->set( $offer_id, 'http://schema.org/Offer' );
		$offer_uri = $this->entity_service->get_uri( $offer_id );

		$thing_id = $this->factory->post->create( array( 'post_type' => 'entity' ) );
		Wordlift_Entity_Type_Service::get_instance()->set( $thing_id, 'http://schema.org/Thing' );

		// Create the property values.
		$availability_start_date = date( 'Y/m/d', 1576800000 );
		$availability_end_date   = date( 'Y/m/d', 3153600000 );
		$valid_from_date         = date( 'Y/m/d', 1953600000 );
		$valid_until_date        = date( 'Y/m/d', 2253600000 );
		$availability            = 'InStock';
		$price                   = 18;
		$inventory               = 3;
		$currency                = 'EUR';
		$thing_uri               = $this->entity_service->get_uri( $thing_id );

		// Update post metas.
		add_post_meta( $offer_id, Wordlift_Schema_Service::FIELD_AVAILABILITY, $availability );
		add_post_meta( $offer_id, Wordlift_Schema_Service::FIELD_AVAILABILITY_STARTS, $availability_start_date );
		add_post_meta( $offer_id, Wordlift_Schema_Service::FIELD_AVAILABILITY_ENDS, $availability_end_date );
		add_post_meta( $offer_id, Wordlift_Schema_Service::FIELD_INVENTORY_LEVEL, $inventory );
		add_post_meta( $offer_id, Wordlift_Schema_Service::FIELD_PRICE, $price );
		add_post_meta( $offer_id, Wordlift_Schema_Service::FIELD_PRICE_CURRENCY, $currency );
		add_post_meta( $offer_id, Wordlift_Schema_Service::FIELD_VALID_FROM, $valid_from_date );
		add_post_meta( $offer_id, Wordlift_Schema_Service::FIELD_PRICE_VALID_UNTIL, $valid_until_date );
		add_post_meta( $offer_id, Wordlift_Schema_Service::FIELD_ITEM_OFFERED, $thing_uri );

		// Set a random sameAs.
		$same_as = 'http://example.org/aRandomSameAs';
		add_post_meta( $offer_id, Wordlift_Schema_Service::FIELD_SAME_AS, $same_as );

		$post       = get_post( $offer_id );
		$references = array();
		$jsonld     = $this->entity_post_to_jsonld_converter->convert( $post->ID, $references );

		$this->assertTrue( is_array( $jsonld ) );
		$this->assertArrayHasKey( '@context', $jsonld );
		$this->assertEquals( 'http://schema.org', $jsonld['@context'] );

		$this->assertArrayHasKey( '@id', $jsonld );
		$this->assertEquals( $offer_uri, $jsonld['@id'] );

		$this->assertArrayHasKey( '@type', $jsonld );
		$this->assertEquals( 'Offer', $jsonld['@type'] );

		$this->assertArrayHasKey( 'name', $jsonld );
		$this->assertEquals( $name, $jsonld['name'] );

		$this->assertArrayHasKey( 'availability', $jsonld );
		$this->assertEquals( $availability, $jsonld['availability'] );

		$this->assertArrayHasKey( 'price', $jsonld );
		$this->assertEquals( $price, $jsonld['price'] );

		$this->assertArrayHasKey( 'priceCurrency', $jsonld );
		$this->assertEquals( $currency, $jsonld['priceCurrency'] );

		$this->assertArrayHasKey( 'availabilityStarts', $jsonld );
		$this->assertEquals( $availability_start_date, $jsonld['availabilityStarts'] );

		$this->assertArrayHasKey( 'availabilityEnds', $jsonld );
		$this->assertEquals( $availability_end_date, $jsonld['availabilityEnds'] );

		$this->assertArrayHasKey( 'inventoryLevel', $jsonld );
		$this->assertEquals( $inventory, $jsonld['inventoryLevel'] );

		$this->assertArrayHasKey( 'validFrom', $jsonld );
		$this->assertEquals( $valid_from_date, $jsonld['validFrom'] );

		$this->assertArrayHasKey( 'priceValidUntil', $jsonld );
		$this->assertEquals( $valid_until_date, $jsonld['priceValidUntil'] );

		$this->assertArrayHasKey( 'url', $jsonld );
		$this->assertEquals( get_permalink( $offer_id ), $jsonld['url'] );

		$this->assertArrayHasKey( 'sameAs', $jsonld );
		$this->assertEquals( $same_as, $jsonld['sameAs'] );

		$this->assertArrayHasKey( 'itemOffered', $jsonld );
		$this->assertEquals( $thing_uri, $jsonld['itemOffered'] );

	}

	public function test_should_add_mentions_to_post_jsonld() {

		$referenced_entity     = $this->factory()->post->create(
			array(
				'post_type'  => 'entity',
				'post_title' => 'Linux',
			)
		);
		$referenced_entity_uri = Wordpress_Content_Service::get_instance()
											  ->get_entity_id( Wordpress_Content_Id::create_post( $referenced_entity ) );
		$post_content          = <<<EOF
		<span itemid="$referenced_entity_uri">test</span>
EOF;

		$parent_entity = $this->factory()->post->create(
			array(
				'post_type'    => 'entity',
				'post_title'   => 'Windows',
				'post_content' => $post_content,
			)
		);

		// set entity to creative work.
		Wordlift_Entity_Type_Service::get_instance()->set( $parent_entity, 'http://schema.org/CreativeWork' );
		$jsonld = Wordlift_Jsonld_Service::get_instance()->get_jsonld(
			false,
			$parent_entity
		);

		$this->assertTrue( array_key_exists( 'mentions', $jsonld[0] ), 'Entity since its descendant of CreativeWork should have mentions property' );
		$this->assertCount( 1, $jsonld[0]['mentions'] );
		$this->assertEquals( array( '@id' => $referenced_entity_uri ), $jsonld[0]['mentions'][0] );
	}

	public function test_when_entity_is_in_about_should_not_be_in_mentions() {

		$referenced_entity_1 = $this->factory()->post->create(
			array(
				'post_type'  => 'entity',
				'post_title' => 'Linux',
			)
		);

		$referenced_entity_2 = $this->factory()->post->create(
			array(
				'post_type'  => 'entity',
				'post_title' => 'Windows',
			)
		);

		$referenced_entity_uri_1 = Wordpress_Content_Service::get_instance()
										->get_entity_id( Wordpress_Content_Id::create_post( $referenced_entity_1 ) );

		$referenced_entity_uri_2 = Wordpress_Content_Service::get_instance()
										->get_entity_id( Wordpress_Content_Id::create_post( $referenced_entity_2 ) );

		$post_content          = <<<EOF
		<span itemid="$referenced_entity_uri_1">test</span>
		<span itemid="$referenced_entity_uri_2">test</span>
EOF;

		$parent_entity = $this->factory()->post->create(
			array(
				'post_type'    => 'post',
				'post_title'   => 'Windows test',
				'post_content' => $post_content,
			)
		);

		// set entity to creative work.
		Wordlift_Entity_Type_Service::get_instance()->set( $parent_entity, 'http://schema.org/Article' );
		$jsonld = Wordlift_Jsonld_Service::get_instance()->get_jsonld(
			false,
			$parent_entity
		);

		$this->assertCount( 1, $jsonld[0]['mentions'], 'One entity needs to be present on the mentions instead of two' );
		$this->assertCount( 1, $jsonld[0]['about'], 'One entity needs to be present on the about' );
	}

	/**
	 * Test the `convert` function using the post properties introduced with #835.
	 *
	 * @see https://github.com/insideout10/wordlift-plugin/issues/835
	 *
	 * @since 3.20.0
	 */
	public function test_convert_835() {

		# Wordlift_Configuration_Service::get_instance()->set_dataset_uri( 'http://data.example.org/data/' );

		$post_id = $this->factory()->post->create(
			array(
				'post_type' => 'entity',
			)
		);

		$_wl_prop_ = Wordlift_Schemaorg_Property_Service::PREFIX;
		add_post_meta( $post_id, "{$_wl_prop_}propA_1_type", 'Text' );
		add_post_meta( $post_id, "{$_wl_prop_}propA_1_language", 'en' );
		add_post_meta( $post_id, "{$_wl_prop_}propA_1_value", 'Value A 1' );

		add_post_meta( $post_id, "{$_wl_prop_}propA_2_type", 'Text' );
		add_post_meta( $post_id, "{$_wl_prop_}propA_2_language", 'en' );
		add_post_meta( $post_id, "{$_wl_prop_}propA_2_value", 'Value A 2' );

		add_post_meta( $post_id, "{$_wl_prop_}propB_1_type", 'Text' );
		add_post_meta( $post_id, "{$_wl_prop_}propB_1_language", 'en' );
		add_post_meta( $post_id, "{$_wl_prop_}propB_1_value", 'Value B 1' );

		$json = $this->entity_post_to_jsonld_converter->convert( $post_id );

		$this->assertArrayHasKey( '@context', $json, 'Expect the `@context` key.' );
		$this->assertArrayHasKey( '@id', $json, 'Expect the `@id` key.' );

		$this->assertArrayHasKey( '@type', $json, 'Expect the `@type` key.' );
		$this->assertEquals( 'Thing', $json['@type'], 'Expect the `@type` to be `Thing` since no type has been assigned.' );

		$this->assertArrayHasKey( 'description', $json, 'Expect the `description` key.' );
		$this->assertArrayHasKey( 'mainEntityOfPage', $json, 'Expect the `mainEntityOfPage` key.' );
		$this->assertArrayHasKey( 'name', $json, 'Expect the `name` key.' );
		$this->assertArrayHasKey( 'url', $json, 'Expect the `url` key.' );

		$this->assertArrayHasKey( 'propA', $json, 'Expect the `propA` key.' );
		$this->assertCount( 2, $json['propA'], 'Expect `propA` to have 2 items.' );
		$this->assertContains( 'Value A 1', $json['propA'], 'Expect `propA` to contain `Value A 1`.' );
		$this->assertContains( 'Value A 2', $json['propA'], 'Expect `propA` to contain `Value A 2`.' );

		$this->assertArrayHasKey( 'propB', $json, 'Expect the `propB` key.' );
		$this->assertEquals( 'Value B 1', $json['propB'], 'Expect `propB` to contain `Value B 1`.' );

	}

}
