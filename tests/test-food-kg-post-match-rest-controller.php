<?php

use Wordlift\Modules\Dashboard\Post_Entity_Match\Post_Entity_Match_Rest_Controller;

/**
 * @group food-kg
 */
class Food_Kg_Post_Match_REST_Controller_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Routes.
	 *
	 * @var string[]
	 */
	protected $routes = [
		'get_post_matches'  => '/wordlift/v1/post-matches',
		'create_post_match' => '/wordlift/v1/post-matches/(?P<post_id>\d+)/matches',
		'update_post_match' => '/wordlift/v1/post-matches/(?P<post_id>\d+)/matches/(?P<match_id>\d+)',
	];

	/**
	 * The {@link Post_Entity_Match_Rest_Controller} instance to test.
	 *
	 * @since  3.25.0
	 * @access private
	 * @var Post_Entity_Match_Rest_Controller $rest_instance The {@link Post_Entity_Match_Rest_Controller} instance to test.
	 */
	private $rest_instance;

	/**
	 * Data to be used for testing.
	 */
	private $data;

	/**
	 * Match Service.
	 */
	private $match_service;

	/**
	 * Set Up.
	 */
	public function setUp() {
		$this->data = array(
			array(
				'post_types' => 'wprm_recipe',
			),
		);

		$this->match_service = $this->getMockBuilder( 'Wordlift\Modules\Dashboard\Post_Entity_Match\Post_Entity_Match_Service' )
			->disableOriginalConstructor()
			->getMock();

		$this->rest_instance = new Post_Entity_Match_Rest_Controller( $this->match_service );
		$this->rest_instance->register_hooks();

		global $wp_rest_server, $wpdb;

		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;
		$this->wpdb     = $wpdb;

		do_action( 'rest_api_init' );
	}

	/**
	 * Testing if instance is not null, check to determine this class is
	 * included.
	 */
	public function test_instance_not_null() {
		$this->assertNotNull( $this->rest_instance );
	}

	/**
	 * Test get post matches route exists.
	 */
	public function test_get_post_matches_route_exists() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( $this->routes['get_post_matches'], $routes );
	}

	/**
	 * Test create post match route exists.
	 */
	public function test_create_post_match_route_exists() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( $this->routes['create_post_match'], $routes );
	}

	/**
	 * Test update post match route exists.
	 */
	public function test_update_post_match_route_exists() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( $this->routes['update_post_match'], $routes );
	}

	/**
	 * Test rest route for get matches without permission returns rest forbidden.
	 */
	public function test_rest_route_for_get_matches_without_permission_returns_rest_forbidden() {
		$request = new WP_REST_Request(
			'GET',
			$this->routes['get_post_matches']
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
	 * Test rest route for post matches returns 200.
	 */
	public function test_rest_route_for_post_matches_returns_200() {
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		$request = new WP_REST_Request(
			'GET',
			$this->routes['get_post_matches']
		);

		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );
	}

	/**
	 * Test if rest route for getting ingredients returns data.
	 */
	public function test_rest_route_for_post_matches_returns_data() {
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		$this->factory()->post->create_many( 7 );

		$request = new WP_REST_Request(
			'GET',
			$this->routes['get_post_matches']
		);

		$response    = $this->server->dispatch( $request );
		$data_object = $response->get_data();

		$data = $data_object->jsonSerialize();

		$this->assertKeyHasStringValue('self', $data);
        $this->assertKeyHasStringValue('first', $data);
        $this->assertKeyHasStringValue('last', $data);

        $this->assertArrayHasKey('items', $data);
        $this->assertNotEmpty($data['items']);
	}

	/**
	 * Test rest route for post matches without permission returns rest forbidden.
	 */
	public function test_rest_route_for_post_matches_without_permission_returns_rest_forbidden() {
		$post_id = $this->factory()->post->create();

		$request = new WP_REST_Request(
			'POST',
			str_replace( '(?P<post_id>\d+)', $post_id, $this->routes['create_post_match'] )
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

	public function test_rest_route_for_put_matches_without_permission_returns_rest_forbidden() {
		$post_id = $this->factory()->post->create();

		$request = new WP_REST_Request(
			'PUT',
			str_replace( array( '(?P<post_id>\d+)', '(?P<match_id>\d+)' ), array( $post_id, 1 ), $this->routes['update_post_match'] )
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
	 * Assert Key Has String Value.
	 *
	 * @param $key
	 * @param $data
	 */
	protected function assertKeyHasStringValue( $key, $data ) {
		$this->assertArrayHasKey( $key, $data );
		$this->assertNotEmpty( $data[ $key ] );
		$this->assertIsString( $data[ $key ] );
	}
}
