<?php
/**
 * This file provides the test for the {@link Jsonld_By_Id_Endpoint}.
 *
 * @since 3.26.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

use Wordlift\Jsonld\Jsonld_By_Id_Endpoint;

/**
 * Define the Jsonld_By_Id_Endpoint_Test class.
 *
 * @group mappings
 *
 * @since 3.26.0
 */
class Jsonld_By_Id_REST_Controller_Test extends WP_UnitTestCase {

	/**
	 * Our expected route for rest api.
	 */
	protected $route = '/wordlift/v1/jsonld';
	/**
	 * The {@link Jsonld_By_Id_Endpoint} instance to test.
	 *
	 * @since  3.26.0
	 * @access private
	 * @var Jsonld_By_Id_Endpoint $rest_instance The {@link Jsonld_By_Id_Endpoint} instance to test.
	 */
	private $rest_instance;
	/**
	 * @var WP_REST_Server
	 */
	private $server;

	/**
	 * @var wpdb
	 */
	private $wpdb;
	private $entity_service;

	/**
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();

		$this->entity_service = Wordlift_Entity_Service::get_instance();

		$jsonld_service      = Wordlift_Jsonld_Service::get_instance();
		$entity_uri_service  = Wordlift_Cached_Entity_Uri_Service::get_instance();
		$this->rest_instance = new Jsonld_By_Id_Endpoint( $jsonld_service, $entity_uri_service );

		global $wp_rest_server, $wpdb;

		$wp_rest_server = new WP_REST_Server();

		$this->server = $wp_rest_server;
		$this->wpdb   = $wpdb;
		do_action( 'rest_api_init' );

		// Set the dataset URI to allow WLP to generate URIs.
		Wordlift_Configuration_Service::get_instance()
		                              ->set_dataset_uri( 'http://data.example.org' );
	}

	/**
	 * Testing if instance is not null, check to determine this class is
	 * included.
	 */
	public function test_instance_not_null() {
		$this->assertNotNull( $this->rest_instance );
	}

	/**
	 * Test is rest route exists for inserting/updating mapping item.
	 */
	public function test_rest_route_for_inserting_mapping_item() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( $this->route, $routes );
	}

	public function test_missing_id() {

		$request  = new WP_REST_Request( 'GET', $this->route );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status(), 'We expect an empty response.' );
		$this->assertEmpty( $response->get_data(), 'We expect an empty response.' );

	}

	public function test_invalid_id() {

		$request = new WP_REST_Request( 'GET', $this->route );
		$request->set_param( 'id', array( 'doesnt-start-with-http' ) );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 400, $response->get_status(), 'The route is activated only when the `id` parameter is passed.' );
		$data = $response->get_data();
		$this->assertEquals( 'rest_invalid_param', $data['code'], 'We expect an `rest_invalid_param` code.' );

	}

	public function test_one_item() {

		$taxonomy_service = new Wordlift_Entity_Type_Taxonomy_Service();
		$taxonomy_service->init();
		$this->assertTrue( taxonomy_exists( Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME ) );

		$install = new Wordlift_Install_1_0_0();
		$install->install();

		$terms = get_terms( array(
			'taxonomy'   => Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
			'hide_empty' => false,
		) );
		$this->assertGreaterThan( 0, count( $terms ), 'WordLift`s taxonomy must be initialized: ' . var_export( $terms, true ) );

		$post_id = $this->factory()->post->create( array(
			'post_type'    => 'entity',
			'post_title'   => 'Jsonld_By_Id_REST_Controller_Test->test_one_item title 1',
			'post_excerpt' => 'Jsonld_By_Id_REST_Controller_Test->test_one_item content 1'
		) );

		$post_uri = $this->entity_service->get_uri( $post_id );

		$request = new WP_REST_Request( 'GET', $this->route );
		$request->set_param( 'id', array( $post_uri ) );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status(), 'Except success, provided URI was: ' . $post_uri );

		$data_array = $response->get_data();

		$this->assertCount( 1, $data_array, 'Expect at least one item in the response.' );

		$data = $data_array[0];

		$this->assertArrayHasKey( '@context', $data, 'Response must contain the `@context`.' );
		$this->assertArrayHasKey( '@id', $data, 'Response must contain the `@id`.' );

		$this->assertArrayHasKey( '@type', $data, 'Response must contain the `@type`.' );

		$this->assertArrayHasKey( 'description', $data, 'Response must contain the `description`.' );
		$this->assertArrayHasKey( 'mainEntityOfPage', $data, 'Response must contain the `mainEntityOfPage`.' );
		$this->assertArrayHasKey( 'name', $data, 'Response must contain the `name`: ' . var_export( $data, true ) );
		$this->assertArrayHasKey( 'url', $data, 'Response must contain the `url`.' );

		if ( 'Thing' !== $data['@type'] ) {
			$this->markTestSkipped( 'Known issue with failing test due to entity type' );
		}

		$this->assertEquals( 'http://schema.org', $data['@context'] );
		$this->assertEquals( 'http://data.example.org/entity/jsonld_by_id_rest_controller_test-_gt_test_one_item_title_1', $data['@id'] );
		$this->assertEquals( 'Jsonld_By_Id_REST_Controller_Test->test_one_item content 1', $data['description'] );
		$this->assertEquals( 'http://example.org/?entity=jsonld_by_id_rest_controller_test-test_one_item-title-1', $data['mainEntityOfPage'] );
		$this->assertEquals( 'Jsonld_By_Id_REST_Controller_Test->test_one_item title 1', $data['name'] );
		$this->assertEquals( 'http://example.org/?entity=jsonld_by_id_rest_controller_test-test_one_item-title-1', $data['url'] );

	}

	public function test_two_items() {

		$post_id_1 = $this->factory()->post->create( array(
			'post_type'    => 'entity',
			'post_title'   => 'Jsonld_By_Id_REST_Controller_Test->test_two_items title 1',
			'post_excerpt' => 'Jsonld_By_Id_REST_Controller_Test->test_two_items content 1'
		) );

		$post_uri_1 = $this->entity_service->get_uri( $post_id_1 );

		$post_id_2 = $this->factory()->post->create( array(
			'post_type'    => 'entity',
			'post_title'   => 'Jsonld_By_Id_REST_Controller_Test->test_one_item title 2',
			'post_excerpt' => 'Jsonld_By_Id_REST_Controller_Test->test_one_item content 2'
		) );

		$post_uri_2 = $this->entity_service->get_uri( $post_id_2 );

		$request = new WP_REST_Request( 'GET', $this->route );
		$request->set_param( 'id', array( $post_uri_1, $post_uri_2 ) );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status(), 'Except success' );

		$data_array = $response->get_data();

		$this->assertCount( 2, $data_array, 'Expect 2 items in the response.' );

	}

	public function test_two_items_with_website() {

		$post_id_1 = $this->factory()->post->create( array(
			'post_type'    => 'entity',
			'post_title'   => 'Jsonld_By_Id_REST_Controller_Test->test_two_items title 1',
			'post_excerpt' => 'Jsonld_By_Id_REST_Controller_Test->test_two_items content 1'
		) );

		$post_uri_1 = $this->entity_service->get_uri( $post_id_1 );

		$post_id_2 = $this->factory()->post->create( array(
			'post_type'    => 'entity',
			'post_title'   => 'Jsonld_By_Id_REST_Controller_Test->test_one_item title 2',
			'post_excerpt' => 'Jsonld_By_Id_REST_Controller_Test->test_one_item content 2'
		) );

		$post_uri_2 = $this->entity_service->get_uri( $post_id_2 );

		$request = new WP_REST_Request( 'GET', $this->route );
		$request->set_param( 'id', array( $post_uri_1, $post_uri_2 ) );
		$request->set_param( 'website', true );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status(), 'Except success' );

		$data_array = $response->get_data();

		$this->assertCount( 3, $data_array, 'Expect 2 items in the response.' );

	}

}
