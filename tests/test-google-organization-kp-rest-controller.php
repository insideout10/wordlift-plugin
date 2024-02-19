<?php

use Wordlift\Modules\Google_Organization_Kp\Rest_Controller;
use Wordlift\Modules\Google_Organization_Kp\Publisher_Service;
use Wordlift\Modules\Google_Organization_Kp\Page_Service;

class Google_Organization_KG_Rest_Controller_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Routes.
	 *
	 * @var string[]
	 */
	protected $routes = [
		'get_countries'  => '/wordlift/v1/wl-google-organization-kp/countries',
		'get_pages'      => '/wordlift/v1/wl-google-organization-kp/pages',
		'get_form_data'  => '/wordlift/v1/wl-google-organization-kp/data',
		'post_form_data' => '/wordlift/v1/wl-google-organization-kp/data'
	];

	/**
	 * The {@link Rest_Controller} instance to test.
	 *
	 * @var Rest_Controller $rest_controller_instance The {@link Rest_Controller} instance to test.
	 */
	private $rest_controller_instance;

	/**
	 * A {@link Wordlift_Entity_Type_Service} instance.
	 *
	 * @var \Wordlift_Entity_Type_Service $entity_type_service A {@link Wordlift_Entity_Type_Service} instance.
	 */
	private $entity_type_service;

	/**
	 * Set Up.
	 */
	public function setUp() {
		parent::setUp();

		$this->rest_controller_instance = new Rest_Controller(
			new Wordlift_Countries,
			new Publisher_Service(
				Wordlift_Publisher_Service::get_instance(),
				Wordlift_Entity_Type_Service::get_instance(),
				Wordlift_Configuration_Service::get_instance()
			),
			new Page_Service
		);

		$this->entity_type_service = Wordlift_Entity_Type_Service::get_instance();

		global $wp_rest_server, $wpdb;

		$wp_rest_server = new WP_REST_Server();

		$this->server   = $wp_rest_server;
		$this->wpdb     = $wpdb;

		do_action( 'rest_api_init' );
	}

	/**
	 * Testing if instance is not null, check to determine this class is included.
	 */
	public function test_instance_not_null() {
		$this->assertNotNull( $this->rest_controller_instance );
		$this->assertNotNull( $this->entity_type_service );
	}

	/**
	 * Test get countries route exists.
	 */
	public function test_get_countries_route_exists() {
//		fwrite(STDERR, print_r($routes, TRUE));
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( $this->routes['get_countries'], $routes );
	}

	/**
	 * Test get pages route exists
	 */
	public function test_get_pages_route_exists() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( $this->routes['get_pages'], $routes );
	}

	/**
	 * Test update post match route exists.
	 */
	public function test_get_form_data_exists() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( $this->routes['get_form_data'], $routes );
	}

	/**
	 * Test update post match route exists.
	 */
	public function test_post_form_data_exists() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( $this->routes['post_form_data'], $routes );
	}

	/**
	 * Test rest route for get countries without permission returns rest forbidden.
	 */
	public function test_rest_route_for_get_countries_without_permission_returns_rest_forbidden() {
		$request = new WP_REST_Request(
			'GET',
			$this->routes['get_countries']
		);

		$response = $this->server->dispatch( $request );

		$data = array(
			'code'    => 'rest_forbidden',
			'message' => 'Sorry, you are not allowed to do that.',
			'data'    => array(
				'status' => 401,
			),
		);

		$this->assertEquals( $data, $response->get_data() );
	}

	/**
	 * Test rest route for get countries with permission returns data.
	 */
	public function test_rest_route_for_get_countries_returns_data() {
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		$request = new WP_REST_Request(
			'GET',
			$this->routes['get_countries']
		);

		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );

		$countries = $response->get_data();

		$this->assert_key_has_string_value( 'name', $countries[0] );
		$this->assert_key_has_string_value( 'code', $countries[0] );
	}

	/**
	 * Test rest route for post matches without permission returns rest forbidden.
	 */
	public function test_rest_route_for_get_pages_without_permission_returns_rest_forbidden() {
		$request = new WP_REST_Request(
			'GET',
			$this->routes['get_pages']
		);

		$response = $this->server->dispatch( $request );

		$data = array(
			'code'    => 'rest_forbidden',
			'message' => 'Sorry, you are not allowed to do that.',
			'data'    => array(
				'status' => 401,
			),
		);

		$this->assertEquals( $data, $response->get_data() );
	}

	/**
	 * Test rest route for get pages with permission returns data.
	 */
	public function test_rest_route_for_get_pages_returns_data() {
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		// Create published page
		$post1_id = $this->factory->post->create(array(
			'post_title' => 'ABC',
			'post_content' => 'Lorem ipsum',
			'post_status' => 'publish',
			'post_type' => 'page'
		));

		// Create second published page
		$post2_id = $this->factory->post->create(array(
			'post_title' => '123',
			'post_content' => 'Dolor sit',
			'post_status' => 'publish',
			'post_type' => 'page'
		));

		// Create a draft post
		$post3_id = $this->factory->post->create(array(
			'post_title' => 'rgb',
			'post_content' => 'Lorem ipsum',
			'post_status' => 'draft',
			'post_type' => 'page'
		));

		/*
		 * Test pages were correctly created
		 */

		$post_ids = array(
			$post1_id,
			$post2_id,
			$post3_id
		);

		foreach( $post_ids as $post_id ) {
			$post = get_post( $post_id );
			$this->assertNotNull( $post );
		}

		// Define pages request
		$request = new WP_REST_Request(
			'GET',
			$this->routes['get_pages']
		);

		// Expected response for page 1
		$expected_page1 = array(
			'id' => $post1_id,
			'title' => 'ABC'
		);

		// Expected response for page 2
		$expected_page2	= array(
			'id' => $post2_id,
			'title' => '123'
		);

		/*
		 * Test unfiltered pages result
		 */

		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );

		$pages = $response->get_data();

		// Check the pages response
		$this->assert_key_has_string_value( 'id', $pages[0] );
		$this->assert_key_has_string_value( 'title', $pages[0] );
		$this->assertIsArray( $pages, 'pages response should be array' );
		$this->assertCount( 2, $pages, 'only two pages were expected' );
		$this->assertContains( $expected_page1, $pages, 'pages result did not match the expected result' );
		$this->assertContains( $expected_page2, $pages, 'pages result did not match the expected result' );

		/*
		 * Test filtered pages result
		 */

		$request->set_query_params(array(
			'title_starts_with' => "A"
		));

		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );

		$pages = $response->get_data();

		// Check the pages response
		$this->assert_key_has_string_value( 'id', $pages[0] );
		$this->assert_key_has_string_value( 'title', $pages[0] );
		$this->assertIsArray( $pages, 'pages response should be object' );
		$this->assertCount( 1, $pages, 'only two pages were expected' );
		$this->assertContains( $expected_page1, $pages, 'pages result did not match the expected result' );
		$this->assertNotContains( $expected_page2, $pages, 'filtered page should not be returned' );
	}

	/**
	 * Test rest route for get form data without permission returns rest forbidden.
	 */
	public function test_rest_route_for_get_form_data_without_permission_returns_rest_forbidden() {
		$request = new WP_REST_Request(
			'GET',
			$this->routes['get_form_data']
		);

		$response = $this->server->dispatch( $request );

		$data = array(
			'code'    => 'rest_forbidden',
			'message' => 'Sorry, you are not allowed to do that.',
			'data'    => array(
				'status' => 401,
			),
		);

		$this->assertEquals( $data, $response->get_data() );
	}

	public function test_rest_route_for_get_form_data_with_configuration() {
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		$request = new WP_REST_Request(
			'GET',
			$this->routes['get_form_data']
		);

		$response = $this->server->dispatch( $request );
		$data = $response->get_data();

		$expected_data_keys = array(
			'page',
			'type',
			'name',
			'alt_name',
			'legal_name',
			'description',
			'image',
			'url',
			'same_as',
			'address',
			'locality',
			'region',
			'country',
			'postal_code',
			'telephone',
			'email',
			'no_employees',
			'founding_date',
			'iso_6523',
			'naics',
			'global_loc_no',
			'vat_id',
			'tax_id'
		);

		foreach( $expected_data_keys as $key ) {
			$this->assertArrayHasKey( $key, $data );
		}
	}

	/**
	 * Test rest route for post form data without permission returns rest forbidden.
	 */
	public function test_rest_route_for_post_form_data_without_permission_returns_rest_forbidden() {
		$request = new WP_REST_Request(
			'GET',
			$this->routes['post_form_data']
		);

		$response = $this->server->dispatch( $request );

		$data = array(
			'code'    => 'rest_forbidden',
			'message' => 'Sorry, you are not allowed to do that.',
			'data'    => array(
				'status' => 401,
			),
		);

		$this->assertEquals( $data, $response->get_data() );
	}

	/**
	 * Test rest route for post form data with no parameters throws exception.
	 */
	public function test_rest_route_for_post_form_data_empty_parameters() {
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		$request = new WP_REST_Request(
			'POST',
			$this->routes['post_form_data']
		);

		// Expect no parameters error
		$this->expectExceptionMessage('No parameters provided');
		$this->server->dispatch( $request );
	}

	/**
	 * Test rest route for post form data with empty image throws exception
	 */
	public function test_rest_route_for_post_form_data_image_empty() {
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		$request = new WP_REST_Request(
			'POST',
			$this->routes['post_form_data']
		);

		$params = array(
			'image' => array(
				'file' => 'abc'
			)
		);

		$request->set_file_params( $params );

		$data = array(
			'code'    => '400',
			'message' => 'File mime type is not set.',
			'data'    => array(
				'status' => 400,
			),
		);

		$response = $this->server->dispatch( $request );
		$this->assertEquals( $data, $response->get_data() );
	}

	/**
	 * Test rest route for post form data with incorrect file type throws exception
	 */
	public function test_rest_route_for_post_form_data_image_wrong_file_type() {
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		$request = new WP_REST_Request(
			'POST',
			$this->routes['post_form_data']
		);

		$params = array(
			'image' => array(
				'file' => 'abc',
				'type' => 'text'
			)
		);

		$request->set_file_params( $params );

		$data = array(
			'code'    => '400',
			'message' => 'Only image files are supported.',
			'data'    => array(
				'status' => 400,
			),
		);

		$response = $this->server->dispatch( $request );
		$this->assertEquals( $data, $response->get_data() );
	}

	/**
	 * Test rest route for post form data with required parameters
	 */
	public function test_rest_route_for_post_form_data_required_params() {
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		/*
		 * Set initial Publisher values and check correctly set
		 */

		$publisher = $this->entity_factory->create_and_get( array(
			'post_title' => 'ACME Inc.',
		) );

		$this->entity_type_service->set( $publisher->ID, 'http://schema.org/Organization' );

		Wordlift_Configuration_Service::get_instance()->set_publisher_id( $publisher->ID );

		$publisher_post = get_post( $publisher->ID );
		$publisher_entity = $this->entity_type_service->get( $publisher->ID );

		$this->assertEquals( 'ACME Inc.', $publisher_post->post_title );
		$this->assertEquals( 'Organization', $publisher_entity['label'] );

		/*
		 * Update the Publisher
		 */

		$request = new WP_REST_Request(
			'POST',
			$this->routes['post_form_data']
		);

		$request->set_query_params( array(
			'name' => 'Nicholas Cage',
			'type' => 'Person'
		) );

		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );

		/*
		 * Check updated Publisher values
		 */

		$publisher_id = Wordlift_Configuration_Service::get_instance()->get_publisher_id();

		$publisher_post = get_post( $publisher_id );
		$publisher_entity = $this->entity_type_service->get( $publisher_id );

		$this->assertEquals( 'Nicholas Cage', $publisher_post->post_title );
		$this->assertEquals( 'Person', $publisher_entity['label'] );
	}

	/**
	 * Assert Key Has String Value.
	 *
	 * @param $key
	 * @param $data
	 */
	private function assert_key_has_string_value( $key, $data ) {
		$this->assertArrayHasKey( $key, $data );
		$this->assertNotEmpty( $data[ $key ] );
		$this->assertIsString( $data[ $key ] );
	}
}
