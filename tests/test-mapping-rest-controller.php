<?php
/**
 * Tests: Mappings Test.
 *
 * @since 3.25.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Wordlift_Mapping_REST_Controller_Test class.
 *
 * @since 3.25.0
 */
class Wordlift_Mapping_REST_Controller_Test extends WP_UnitTestCase {

	/**
	 * The {@link Wordlift_Mapping_REST_Controller} instance to test.
	 *
	 * @since  3.25.0
	 * @access private
	 * @var \Wordlift_Mapping_REST_Controller $rest_instance The {@link Wordlift_Mapping_REST_Controller} instance to test.
	 */
	private $rest_instance;

	/**
	 * Our expected route for rest api.
	 */
	protected $mapping_route = '/wordlift/v1/sync-mappings/mapping';
	/**
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();	

		$this->rest_instance = new Wordlift_Mapping_REST_Controller();
		$this->rest_instance->register_routes();

		global $wp_rest_server;
		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;
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
	 * Test is rest route exists for inserting/updating mapping item.
	 */
	public function test_rest_route_for_inserting_mapping_item() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( $this->mapping_route, $routes );
	}

	/**
	 * Test post mapping item to rest api endpoint returns 200 status code.
	 */
	public function test_post_to_insert_mapping_item_endpoint_returns_200() {
		// Only roles with manage_options permission can post to the url.
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		$user    = wp_set_current_user( $user_id );

		$request  = new WP_REST_Request( 'POST', $this->mapping_route );
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status() );
	}

	/**
	 * Test posting fake json data, should save properly
	 */
	public function test_post_fake_json_to_insert_mapping_item_endpoint() {
		// Only roles with manage_options permission can post to the url.
		$user_id   = $this->factory->user->create( array( 'role' => 'administrator' ) );
		$user      = wp_set_current_user( $user_id );		
		$json_data = file_get_contents( __DIR__ . '/assets/fake_sync_mappings_create_edit_item.json' );
		$post_array = json_decode( $json_data, true );
		$request   = new WP_REST_Request( 'POST', $this->mapping_route );
		$request->set_body_params( $post_array );
		$response  = $this->server->dispatch( $request );

		// We are now going to assert against database to
		// to check if everything is correctly saved.
		global $wpdb;
		$mapping_table_name = $wpdb->prefix . WL_MAPPING_TABLE_NAME;
		$rule_group_table_name = $wpdb->prefix . WL_RULE_GROUP_TABLE_NAME;
		$rule_table_name = $wpdb->prefix . WL_RULE_TABLE_NAME;
		$property_table_name = $wpdb->prefix . WL_PROPERTY_TABLE_NAME;
		// 1 mapping item is posted, even though it is not in db, it should be saved
		$mapping_row_count = $wpdb->get_var( "SELECT COUNT(mapping_id) as total FROM $mapping_table_name" );
		$this->assertEquals( 1, $mapping_row_count );

		// 2 rule groups posted without rule group id, see assets/fake_sync_mappings_create_edit_item.json
		// for more details,
		$rule_group_count = $wpdb->get_var( "SELECT COUNT(DISTINCT rule_group_id) as total FROM $rule_group_table_name" );
		$this->assertEquals( 2, $rule_group_count );

		// 4 rules posted, so checking if 4 rules are saved
		$rule_count = $wpdb->get_var( "SELECT COUNT(DISTINCT rule_id) as total FROM $rule_table_name" );
		$this->assertEquals( 4, $rule_count );

		// 2 properties posted, so expect 2 properties in database.
		$property_count = $wpdb->get_var( "SELECT COUNT(property_id) as total FROM $property_table_name" );
		$this->assertEquals( 2, $property_count );
	}
}
