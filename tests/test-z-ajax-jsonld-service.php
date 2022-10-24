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
 * @group ajax
 */
class Wordlift_Ajax_Jsonld_Service_Test extends Wordlift_Ajax_Unit_Test_Case {

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

		$wordlift = new Wordlift_Test();

		$this->entity_service                  = Wordlift_Entity_Service::get_instance();
		$this->entity_post_to_jsonld_converter = $wordlift->get_entity_post_to_jsonld_converter();
		$this->jsonld_service                  = $wordlift->get_jsonld_service();

		add_filter( 'pre_http_request', array( $this, '_mock_api' ), 10, 3 );
	}

	function tearDown() {
		remove_filter( 'pre_http_request', array( $this, '_mock_api' ) );

		parent::tearDown();
	}

	function _mock_api( $response, $request, $url ) {

		if ( 'POST' === $request['method'] && preg_match( '@/datasets/key=key123/queries$@', $url )
		     && in_array( md5( $request['body'] ), array(
				'296514b093946381d9a187418eaa5114',
				'68d2d96e64bb1574a8e0bb945d4db956',
				'24f6e49c507ff2c1976b9fba1da77820',
				'21b9d8295d7672739af1da478d205d52',
				'0a8b35d947c2b0419bbbbb3c8aea7da6',
				'01c8c5c6205ba40f44f7cbf0eb9d5120',
				'c630ea8cbd5534272a0fe7def494d9e0',
				'feecdfb1995c54e21b3f9868326ead04',
				'cebd54c0310311bb0a0102f312a8fef8',
				'3c159302c89c70eaac8f88448d46cfba',
				'f934d227ea0458348a79b7175999620b',
				'4dce97983d0af505f87eaabb88dd708e',
				'5013cd6b57c2a10baee01d032343a28c'
			) )
		     || preg_match( '~^INSERT DATA { <https://data\.localdomain\.localhost/dataset/entity/(.+?)> <http://www\.w3\.org/2000/01/rdf-schema#label> ".+?"@en \. 
<https://data\.localdomain\.localhost/dataset/entity/\\1> <http://purl\.org/dc/terms/title> ".+?"@en \. 
<https://data\.localdomain\.localhost/dataset/entity/\\1> <http://schema\.org/name> ".+?"@en \. 
<https://data\.localdomain\.localhost/dataset/entity/\\1> <http://schema\.org/url> <http://example\.org/\?entity=.+?> \. 
<https://data\.localdomain\.localhost/dataset/entity/\\1> <http://schema\.org/description> ".+?"@en \. 
<https://data\.localdomain\.localhost/dataset/entity/\\1> <http://www\.w3\.org/1999/02/22-rdf-syntax-ns#type> <http://schema\.org/Thing> \.  };$~', $request['body'] )
		     || preg_match( '~^INSERT DATA { <https://data\.localdomain\.localhost/dataset/(page|post)/(.+?)> <http://schema\.org/headline> ".+?"@en \. 
<https://data\.localdomain\.localhost/dataset/\\1/\\2> <http://schema\.org/url> <http://example\.org/\?p(?:age_id)?=\d+> \. 
<https://data\.localdomain\.localhost/dataset/\\1/\\2> <http://www\.w3\.org/1999/02/22-rdf-syntax-ns#type> <http://schema\.org/Article> \.  };$~', $request['body'] ) ) {
			return array(
				'response' => array( 'code' => 200 ),
				'body'     => ''
			);
		}

		if ( 'POST' === $request['method'] && preg_match( '@/datasets/key=key123/index$@', $url ) ) {
			return array(
				'response' => array( 'code' => 200 ),
				'body'     => ''
			);
		}

		return $response;
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

		$dataset_uri = Wordlift_Configuration_Service::get_instance()->get_dataset_uri();
		$this->assertNotEmpty( $dataset_uri, 'Dataset URI can`t be empty.' );

		$local_business_term = get_term_by( 'slug', 'local-business', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
		$this->assertTrue( is_object( $local_business_term ), 'The `LocalBusiness` term must exist.' );

		// Create a location entity post and bind it to the location property.
		$name              = 'Test Ajax Json-Ld Service test_jsonld 1';
		$local_business_id = $this->factory()->post->create( array(
			'post_title'   => $name,
			'post_type'    => 'entity',
			'post_content' => "Post $name",
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $local_business_id, 'http://schema.org/LocalBusiness' );
		$local_business_type = Wordlift_Entity_Type_Service::get_instance()->get( $local_business_id );

		$this->assertEquals( 'http://schema.org/LocalBusiness', $local_business_type['uri'] );

		$local_business_uri = $this->entity_service->get_uri( $local_business_id );

		// Set the geo coordinates.
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_GEO_LATITUDE, 12.34 );
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_GEO_LONGITUDE, 1.23 );

		$email = 'john@localdomain.localhost';
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_EMAIL, $email );

		$phone = '+1 (555) 555 5555';
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_TELEPHONE, $phone );

		// Set a random sameAs.
		$same_as = 'http://example.org/aRandomSameAs';
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_SAME_AS, $same_as );

		$street_address = 'Duck Rd. 1';
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_ADDRESS, $street_address );

		$po_box = 'PO BOX 123';
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_ADDRESS_PO_BOX, $po_box );

		$postal_code = '12345';
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_ADDRESS_POSTAL_CODE, $postal_code );

		$locality = 'Here';
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_ADDRESS_LOCALITY, $locality );

		$region = 'There';
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_ADDRESS_REGION, $region );

		$country = 'US';
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_ADDRESS_COUNTRY, $country );

		$person_id = $this->factory()->post->create( array(
			'post_type'  => 'entity',
			'post_title' => 'Test Ajax Json-Ld Service test_jsonld'
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $person_id, 'http://schema.org/Person' );

		$person_type = Wordlift_Entity_Type_Service::get_instance()->get( $person_id );
		$this->assertEquals( 'http://schema.org/Person', $person_type['uri'] );

		$person_uri = $this->entity_service->get_uri( $person_id );

		// Bind the person as author of the creative work.
		add_post_meta( $local_business_id, Wordlift_Schema_Service::FIELD_FOUNDER, $person_id );

		// Set up a default request
		$_GET['action'] = 'wl_jsonld';
		$_GET['id']     = $local_business_id;

		Wordlift_Jsonld_Service::get_instance()->get_jsonld( $local_business_id );
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
		$this->assertEquals( $local_business_uri, $jsonld_1['@id'], "Expect {$jsonld_1['@id']}, the response was: " . var_export( $response, true ) );

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
		$name        = 'Test Ajax Json-Ld Service test_jsonld_website 2';
		$description = "Post $name";

		// Set publisher.
		$publisher = $this->entity_factory->create_and_get( array(
			'post_title' => 'Test Ajax Json-Ld Service test_jsonld_website'
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $publisher->ID, 'http://schema.org/Person' );
		$publisher_uri = $this->entity_service->get_uri( $publisher->ID );
		Wordlift_Configuration_Service::get_instance()->set_publisher_id( $publisher->ID );
		Wordlift_Configuration_Service::get_instance()->set_alternate_name( $description );

		// Create homepage
		$homepage_id = $this->factory()->post->create( array(
			'post_title'   => $name,
			'post_type'    => 'page',
			'post_content' => "Post $name",
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
		$name        = 'Test Ajax Json-Ld Service test_disable_jsonld_website 3';
		$description = "Post $name";

		// Create homepage
		$homepage_id = $this->factory()->post->create( array(
			'post_title' => $name,
			'post_type'  => 'page',
		) );

		// Get url
		$home_uri = $this->entity_service->get_uri( $homepage_id );

		// Set publisher.
		$publisher = $this->entity_factory->create_and_get( array(
			'post_title' => 'Test Ajax Json-Ld Service test_jsonld_website'
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $publisher->ID, 'http://schema.org/Organization' );
		$publisher_uri = $this->entity_service->get_uri( $publisher->ID );
		Wordlift_Configuration_Service::get_instance()->set_publisher_id( $publisher->ID );
		Wordlift_Configuration_Service::get_instance()->set_alternate_name( $description );

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

		/*
		 * When disabling the web site JSON-LD this should actually be 0 not 1 (as it was).
		 *
		 * @see <a href="https://github.com/insideout10/wordlift-plugin/issues/1002>#1002</a>
		 */
		$this->assertCount( 0, $response );

//		$jsonld = get_object_vars( $response[0] );
//
//		$this->assertTrue( is_array( $jsonld ) );
//
//		$this->assertArrayHasKey( '@context', $jsonld );
//		$this->assertEquals( 'http://schema.org', $jsonld['@context'] );
//
//		$this->assertArrayHasKey( '@id', $jsonld );
//		$this->assertEquals( $home_uri, $jsonld['@id'] );
//
//		$this->assertArrayHasKey( '@type', $jsonld );
//		$this->assertNotEquals( 'WebSite', $jsonld['@type'] );
//
//		$this->assertArrayHasKey( 'description', $jsonld );
//		$this->assertNotEquals( $description, $jsonld['description'] );
//
//		$this->assertArrayHasKey( 'headline', $jsonld );
//		$this->assertNotEquals( $headline, $jsonld['headline'] );
//
//		$publisher_2 = get_object_vars( $jsonld['publisher'] );
//
//		// Check the publisher.
//		$this->assertCount( 3, $publisher_2 );
//		$this->assertEquals( 'Organization', $publisher_2['@type'] );
//		$this->assertEquals( $publisher_uri, $publisher_2['@id'] );
//		$this->assertEquals( $publisher->post_title, $publisher_2['name'] );
	}

	/**
	 * Test that the JSON-LD WebSite works even without homepage.
	 *
	 * @since 3.14.0
	 */
	public function test_jsonld_website_without_homepage() {
		$description = 'A post';

		// Set our page as homepage & update the site description
		update_option( 'blogdescription', $description );

		// Set publisher.
		$publisher = $this->entity_factory->create_and_get( array(
			'post_title' => 'Test Ajax Json-Ld Service test_jsonld_website_without_homepage'
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $publisher->ID, 'http://schema.org/Organization' );
		$publisher_uri = $this->entity_service->get_uri( $publisher->ID );
		Wordlift_Configuration_Service::get_instance()->set_publisher_id( $publisher->ID );
		Wordlift_Configuration_Service::get_instance()->set_alternate_name( $description );

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
		$name        = 'Test Ajax Json-Ld Service test_change_jsonld_website_search_target 4';
		$description = "Post $name";

		// Create homepage
		$homepage_id = $this->factory()->post->create( array(
			'post_title' => $name,
			'post_type'  => 'page',
		) );

		// Set publisher.
		$publisher = $this->entity_factory->create_and_get( array(
			'post_title' => 'Test Ajax Json-Ld Service test_change_jsonld_website_search_target'
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $publisher->ID, 'http://schema.org/Person' );
		$publisher_uri = $this->entity_service->get_uri( $publisher->ID );
		Wordlift_Configuration_Service::get_instance()->set_publisher_id( $publisher->ID );
		Wordlift_Configuration_Service::get_instance()->set_alternate_name( $description );

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

		Wordlift_Configuration_Service::get_instance()->set_alternate_name( $description );

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

		Wordlift_Configuration_Service::get_instance()->set_alternate_name( $description );

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

		$post_id = $this->factory()->post->create( array(
			'type'         => 'post',
			'post_status'  => 'publish',
			'post_title'   => 'Title 1',
			'post_content' => 'Description 1'
		) );

		$recipe_post_id = $this->entity_factory->create( array(
			'post_status'  => 'publish',
			'post_title'   => 'Test Ajax Json-Ld Service test_post_mentioning_a_recipe',
			'post_content' => 'Post Test Ajax Json-Ld Service test_post_mentioning_a_recipe'
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $recipe_post_id, 'http://schema.org/Recipe' );

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

		$response = json_decode( $this->_last_response, true );

		$this->assertCount( 1, $response[0]['mentions'] );

	}

	/**
	 * Test for not allowing duplicate entries in the $references.
	 */
	public function test_should_not_allow_duplicate_references() {
		$post_id  = $this->factory()->post->create( array(
			'post_status'  => 'publish',
			'post_title'   => 'Post 1',
			'post_content' => 'Content 1',
		) );
		$entity_1 = $this->factory()->post->create( array(
			'post_type'    => 'entity',
			'post_title'   => 'Entity 2',
			'post_content' => 'Content 2',
		) );

		$entity_2 = $this->factory()->post->create( array(
			'post_type'    => 'entity',
			'post_title'   => 'Entity 3',
			'post_content' => 'Content 3',
		) );
		// add duplicates to references.
		$references = array( $entity_1, $entity_2, $entity_1 );

		add_filter( 'wl_post_jsonld_array', function ( $data, $post_id ) use ( $references ) {
			$jsonld = $data['jsonld'];

			return array(
				'jsonld'     => $jsonld,
				'references' => $references,
			);
		}, 10, 2 );

		$post_jsonld = Wordlift_Jsonld_Service::get_instance()
		                                      ->get_jsonld( false, $post_id );


		$this->assertCount( 3, $post_jsonld );

	}
}
