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
	 * A {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service A {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		$wordlift = new Wordlift_Test();

		$this->entity_type_service             = $wordlift->get_entity_type_service();
		$this->entity_service                  = $wordlift->get_entity_service();
		$this->entity_post_to_jsonld_converter = $wordlift->get_entity_post_to_jsonld_converter();
		$this->jsonld_service                  = $wordlift->get_jsonld_service();
		$this->configuration_service           = $wordlift->get_configuration_service();

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
		$local_business_id = $this->factory()->post->create( array(
			'post_title' => $name,
			'post_type'  => 'entity',
		) );
		$this->entity_type_service->set( $local_business_id, 'http://schema.org/LocalBusiness' );
		$local_business_type = $this->entity_type_service->get( $local_business_id );
		$this->assertEquals( 'http://schema.org/LocalBusiness', $local_business_type['uri'] );

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

		$person_id = $this->factory()->post->create( array( 'post_type' => 'entity', ) );
		$this->entity_type_service->set( $person_id, 'http://schema.org/Person' );

		$person_type = $this->entity_type_service->get( $person_id );
		$this->assertEquals( 'http://schema.org/Person', $person_type['uri'] );

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
		$this->assertCount( 2, $response, "Expected a `LocalBusiness` and a `Person`." );

		$jsonld_1 = get_object_vars( $response[0] );

		$this->assertTrue( is_string( $jsonld_1['url'] )
			, "URL must be a string, maybe the response is corrupted:\n"
			  . var_export( $response[0], true ) );

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
		$this->assertEquals( get_permalink( $local_business_id )
			, $jsonld_1['url']
			, "Expected:\n"
			  . var_export( $jsonld_1['url'], true )
			  . "Received:\n"
			  . var_export( get_permalink( $local_business_id ), true ) );

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

	/**
	 * Test JSON-LD WebSite.
	 *
	 * @since 3.14.0
	 */
	public function test_jsonld_website() {
		$name        = rand_str();
		$description = rand_str();

		// Set publisher.
		$publisher = $this->entity_factory->create_and_get();
		$this->entity_type_service->set( $publisher->ID, 'http://schema.org/Person' );
		$publisher_uri = $this->entity_service->get_uri( $publisher->ID );
		$this->configuration_service->set_publisher_id( $publisher->ID );

		// Create homepage
		$homepage_id = $this->factory->post->create( array(
			'post_title' => $name,
			'post_type'  => 'page',
		) );

		// Set our page as homepage & update the site description
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $homepage_id );
		update_option( 'blogdescription', $description );

		// Get site info
		$name           = get_bloginfo( 'name' );
		$alternate_name = get_bloginfo( 'description' );
		$url            = home_url( '/' );
		$target         = $url . '?s={search_term_string}';

		// Set up a default request
		$_GET['action']   = 'wl_jsonld';
		$_GET['id']       = $homepage_id;
		$_GET['homepage'] = 'true';

		// Make the request
		try {
			$this->_handleAjax( 'wl_jsonld' );
		} catch ( WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		// Get response
		$response = json_decode( $this->_last_response, 1 );


		$this->assertTrue( is_array( $response ) );

		$this->assertArrayHasKey( '@context', $response );
		$this->assertEquals( 'http://schema.org', $response['@context'] );

		$this->assertArrayHasKey( '@type', $response );
		$this->assertEquals( 'WebSite', $response['@type'] );

		$this->assertArrayHasKey( 'alternateName', $response );
		$this->assertEquals( $alternate_name, $response['alternateName'] );

		$this->assertArrayHasKey( 'name', $response );
		$this->assertEquals( $name, $response['name'] );

		$this->assertArrayHasKey( 'url', $response );
		$this->assertEquals( $url, $response['url'] );

		// Get potentital action
		$potential_action = $response['potentialAction'];

		$this->assertArrayHasKey( '@type', $potential_action );
		$this->assertEquals( 'SearchAction', $potential_action['@type'] );

		$this->assertArrayHasKey( 'target', $potential_action );
		$this->assertEquals( $target, $potential_action['target'] );

		// Check the publisher.
		$this->assertCount( 3, $response['publisher'] );
		$this->assertEquals( 'Person', $response['publisher']['@type'] );
		$this->assertEquals( $publisher_uri, $response['publisher']['@id'] );
		$this->assertEquals( $publisher->post_title, $response['publisher']['name'] );

	}

	/**
	 * Test that disable website filter is working.
	 *
	 * @since 3.14.0
	 */
	public function test_disable_jsonld_website() {
		$name        = rand_str();
		$description = rand_str();

		// Create homepage
		$homepage_id = $this->factory->post->create( array(
			'post_title' => $name,
			'post_type'  => 'page',
		) );

		// Get url
		$home_uri = $this->entity_service->get_uri( $homepage_id );

		// Set publisher.
		$publisher = $this->entity_factory->create_and_get();
		$this->entity_type_service->set( $publisher->ID, 'http://schema.org/Organization' );
		$publisher_uri = $this->entity_service->get_uri( $publisher->ID );
		$this->configuration_service->set_publisher_id( $publisher->ID );

		// Set our page as homepage & update the site description
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $homepage_id );
		update_option( 'blogdescription', $description );

		// Get site info
		$headline    = get_bloginfo( 'name' );
		$description = get_bloginfo( 'description' );
		$url         = home_url( '/' );
		$target      = $url . '?s={search_term_string}';

		// Set up a default request
		$_GET['action']   = 'wl_jsonld';
		$_GET['id']       = $homepage_id;
		$_GET['homepage'] = 'true';

		// Diable WebSite schema
		add_filter( 'wordlift_disable_website_json_ld', '__return_true' );

		// Make the request
		try {
			$this->_handleAjax( 'wl_jsonld' );
		} catch ( WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		$response = json_decode( $this->_last_response );

		$this->assertTrue( is_array( $response ) );

		$this->assertCount( 1, $response );

		$jsonld = get_object_vars( $response[0] );

		$this->assertTrue( is_array( $jsonld ) );

		$this->assertArrayHasKey( '@context', $jsonld );
		$this->assertEquals( 'http://schema.org', $jsonld['@context'] );

		$this->assertArrayHasKey( '@id', $jsonld );
		$this->assertEquals( $home_uri, $jsonld['@id'] );

		$this->assertArrayHasKey( '@type', $jsonld );
		$this->assertNotEquals( 'WebSite', $jsonld['@type'] );

		$this->assertArrayHasKey( 'description', $jsonld );
		$this->assertNotEquals( $description, $jsonld['description'] );

		$this->assertArrayHasKey( 'headline', $jsonld );
		$this->assertNotEquals( $headline, $jsonld['headline'] );

		$publisher_2 = get_object_vars( $jsonld['publisher'] );

		// Check the publisher.
		$this->assertCount( 3, $publisher_2 );
		$this->assertEquals( 'Organization', $publisher_2['@type'] );
		$this->assertEquals( $publisher_uri, $publisher_2['@id'] );
		$this->assertEquals( $publisher->post_title, $publisher_2['name'] );
	}

	/**
	 * Test that the JSON-LD WebSite works even without homepage.
	 *
	 * @since 3.14.0
	 */
	public function test_jsonld_website_without_homepage() {
		$description = rand_str();

		// Set our page as homepage & update the site description
		update_option( 'blogdescription', $description );

		// Set publisher.
		$publisher = $this->entity_factory->create_and_get();
		$this->entity_type_service->set( $publisher->ID, 'http://schema.org/Organization' );
		$publisher_uri = $this->entity_service->get_uri( $publisher->ID );
		$this->configuration_service->set_publisher_id( $publisher->ID );

		// Get site info
		$headline    = get_bloginfo( 'name' );
		$description = get_bloginfo( 'description' );
		$url         = home_url( '/' );
		$target      = $url . '?s={search_term_string}';

		$_GET['action']   = 'wl_jsonld';
		$_GET['homepage'] = 'true';

		// Make the request
		try {
			$this->_handleAjax( 'wl_jsonld' );
		} catch ( WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		$response = json_decode( $this->_last_response, 1 );

		$this->assertTrue( is_array( $response ) );

		$this->assertArrayHasKey( '@context', $response );
		$this->assertEquals( 'http://schema.org', $response['@context'] );

		$this->assertArrayHasKey( '@type', $response );
		$this->assertEquals( 'WebSite', $response['@type'] );

		$this->assertArrayHasKey( 'alternateName', $response );
		$this->assertEquals( $description, $response['alternateName'] );

		$this->assertArrayHasKey( 'name', $response );
		$this->assertEquals( $headline, $response['name'] );

		$this->assertArrayHasKey( 'url', $response );
		$this->assertEquals( $url, $response['url'] );

		$potential_action = $response['potentialAction'];

		$this->assertArrayHasKey( '@type', $potential_action );
		$this->assertEquals( 'SearchAction', $potential_action['@type'] );

		$this->assertArrayHasKey( 'target', $potential_action );
		$this->assertEquals( $target, $potential_action['target'] );

		$this->assertArrayHasKey( 'target', $potential_action );
		$this->assertEquals( $target, $potential_action['target'] );

		// Check the publisher.
		$this->assertCount( 3, $response['publisher'] );
		$this->assertEquals( 'Organization', $response['publisher']['@type'] );
		$this->assertEquals( $publisher_uri, $response['publisher']['@id'] );
		$this->assertEquals( $publisher->post_title, $response['publisher']['name'] );
	}

	/**
	 * Test that the JSON-LD WebSite search_url filter is working.
	 *
	 * @since 3.14.0
	 */
	public function test_change_jsonld_website_search_target() {
		$name        = rand_str();
		$description = rand_str();

		// Create homepage
		$homepage_id = $this->factory->post->create( array(
			'post_title' => $name,
			'post_type'  => 'page',
		) );

		// Set publisher.
		$publisher = $this->entity_factory->create_and_get();
		$this->entity_type_service->set( $publisher->ID, 'http://schema.org/Person' );
		$publisher_uri = $this->entity_service->get_uri( $publisher->ID );
		$this->configuration_service->set_publisher_id( $publisher->ID );

		// Set our page as homepage & update the site description
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $homepage_id );
		update_option( 'blogdescription', $description );

		// Get site info
		$headline    = get_bloginfo( 'name' );
		$description = get_bloginfo( 'description' );
		$url         = home_url( '/' );
		$target      = $url . '?s={search_term_string}';

		// Change the search target
		$modified_target = str_replace( '{search_term_string}', '', $target );

		$_GET['action']   = 'wl_jsonld';
		$_GET['id']       = $homepage_id;
		$_GET['homepage'] = true;

		add_filter( 'wl_jsonld_search_url', array(
			$this,
			'change_search_url',
		) );

		// Make the request
		try {
			$this->_handleAjax( 'wl_jsonld' );
		} catch ( WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		$response = json_decode( $this->_last_response, 1 );

		$this->assertTrue( is_array( $response ) );

		$this->assertArrayHasKey( '@context', $response );
		$this->assertEquals( 'http://schema.org', $response['@context'] );

		$this->assertArrayHasKey( '@type', $response );
		$this->assertEquals( 'WebSite', $response['@type'] );

		$this->assertArrayHasKey( 'alternateName', $response );
		$this->assertEquals( $description, $response['alternateName'] );

		$this->assertArrayHasKey( 'name', $response );
		$this->assertEquals( $headline, $response['name'] );

		$this->assertArrayHasKey( 'url', $response );
		$this->assertEquals( $url, $response['url'] );

		$potential_action = $response['potentialAction'];

		$this->assertArrayHasKey( '@type', $potential_action );
		$this->assertEquals( 'SearchAction', $potential_action['@type'] );

		$this->assertArrayHasKey( 'target', $potential_action );
		$this->assertNotEquals( $target, $potential_action['target'] );

		$this->assertArrayHasKey( 'target', $potential_action );
		$this->assertEquals( $modified_target, $potential_action['target'] );

		// Check the publisher.
		$this->assertCount( 3, $response['publisher'] );
		$this->assertEquals( 'Person', $response['publisher']['@type'] );
		$this->assertEquals( $publisher_uri, $response['publisher']['@id'] );
		$this->assertEquals( $publisher->post_title, $response['publisher']['name'] );
	}

	public function test_jsonld_with_html_entities() {
		$name        = 'Wordlift\'s blog';
		$description = 'Sample description" with\'s single\'s quote';

		// Set our page as homepage & update the site description
		update_option( 'blogdescription', $description );
		update_option( 'blogname', $name );

		// Set up a default request
		$_GET['action']   = 'wl_jsonld';
		$_GET['homepage'] = 'true';

		// Make the request
		try {
			$this->_handleAjax( 'wl_jsonld' );
		} catch ( WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		$response = json_decode( $this->_last_response, 1 );

		$this->assertArrayHasKey( 'name', $response );
		$this->assertEquals( $name, $response['name'] );

		$this->assertArrayHasKey( 'alternateName', $response );
		$this->assertEquals( $description, $response['alternateName'] );

	}

	public function test_jsonld_with_diacritics() {
		$name        = 'àüé';
		$description = 'Sample description" with\'s single\'s quote';

		// Set our page as homepage & update the site description
		update_option( 'blogdescription', $description );
		update_option( 'blogname', $name );

		// Set up a default request
		$_GET['action']   = 'wl_jsonld';
		$_GET['homepage'] = 'true';

		// Make the request
		try {
			$this->_handleAjax( 'wl_jsonld' );
		} catch ( WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		$response = json_decode( $this->_last_response, 1 );

		$this->assertArrayHasKey( 'name', $response );
		$this->assertEquals( $name, $response['name'] );

		$this->assertArrayHasKey( 'alternateName', $response );
		$this->assertEquals( $description, $response['alternateName'] );

	}

	/**
	 * Test the filter `wl_jsonld_search_url`.
	 *
	 * @param string $url The default URL.
	 *
	 * @return string A modified URL.
	 * @since 3.14.0
	 *
	 */
	public function change_search_url( $url ) {
		return str_replace( '{search_term_string}', '', $url );
	}

	/**
	 * Test a recipe mentioned in a post.
	 *
	 * @since 3.14.1
	 */
	public function test_post_mentioning_a_recipe() {

		$post_id = $this->factory->post->create( array(
			'type'        => 'post',
			'post_status' => 'publish',
		) );

		$recipe_post_id = $this->entity_factory->create( array(
			'post_status' => 'publish',
		) );
		$this->entity_type_service->set( $recipe_post_id, 'http://schema.org/Recipe' );

		wl_core_add_relation_instance( $post_id, WL_WHAT_RELATION, $recipe_post_id );

		// Set up a default request
		$_GET['action'] = 'wl_jsonld';
		$_GET['id']     = $post_id;

		// Make the request
		try {
			$this->_handleAjax( 'wl_jsonld' );
		} catch ( WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		$response = json_decode( $this->_last_response );

		// Check that the recipe is there.
		$instances = array_filter( $response, function ( $item ) {
			return 'Recipe' === $item->{'@type'};
		} );

		$this->assertCount( 1, $instances );

	}

}
