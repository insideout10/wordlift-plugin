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
	protected $mapping_route = '/wordlift/v1/sync-mappings/mappings';
	/**
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();	

		$this->rest_instance = new Wordlift_Mapping_REST_Controller();
		$this->rest_instance->register_routes();
		global $wp_rest_server, $wpdb;

		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;
		$this->wpdb = $wpdb;
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
		$mapping_table_name = $this->wpdb->prefix . WL_MAPPING_TABLE_NAME;
		$rule_group_table_name = $this->wpdb->prefix . WL_RULE_GROUP_TABLE_NAME;
		$rule_table_name = $this->wpdb->prefix . WL_RULE_TABLE_NAME;
		$property_table_name = $this->wpdb->prefix . WL_PROPERTY_TABLE_NAME;
		// 1 mapping item is posted, even though it is not in db, it should be saved
		$mapping_row_count = $this->wpdb->get_var( "SELECT COUNT(mapping_id) as total FROM $mapping_table_name" );
		$this->assertEquals( 1, $mapping_row_count );

		// 2 rule groups posted without rule group id, see assets/fake_sync_mappings_create_edit_item.json
		// for more details,
		$rule_group_count = $this->wpdb->get_var( "SELECT COUNT(DISTINCT rule_group_id) as total FROM $rule_group_table_name" );
		$this->assertEquals( 2, $rule_group_count );

		// 4 rules posted, so checking if 4 rules are saved
		$rule_count = $this->wpdb->get_var( "SELECT COUNT(DISTINCT rule_id) as total FROM $rule_table_name" );
		$this->assertEquals( 4, $rule_count );
	
		// 2 properties posted, so expect 2 properties in database.
		$property_count = $this->wpdb->get_var( "SELECT COUNT(property_id) as total FROM $property_table_name" );
		$this->assertEquals( 2, $property_count );

	}
	/** Test get list of mapping items from end point */
	public function test_get_list_mapping_items() {
		// Create user with 'manage options' capability.
		$user_id   = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		$dbo = new Wordlift_Mapping_DBO();
		$dbo->insert_mapping_item( 'foo' );
		// Lets insert a item.
		$request   = new WP_REST_Request( 'GET', $this->mapping_route );
		$response  = $this->server->dispatch( $request );
		// Get 200 code.
		$this->assertEquals( 200, $response->get_status() );
		// We have 1 mapping item in db inserted in this test.
		$this->assertEquals( 1, count( $response->get_data() ) );
	}
	/** Test can delete a list of mapping items */
	public function test_delete_mapping_item() {
		// Create user with 'manage options' capability, only that user can delete this item.
		$user_id   = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		$dbo          = new Wordlift_Mapping_DBO();
		$mapping_id_1 = $dbo->insert_mapping_item( 'foo' );
		$mapping_id_2 = $dbo->insert_mapping_item( 'bar' );

		$request = new WP_REST_Request( 'DELETE', $this->mapping_route );
		$request->set_body_params(
			array(
				'mapping_ids' => array( $mapping_id_1, $mapping_id_2 ),
			)
		);
		$response = $this->server->dispatch( $request );
		// This request should return 200.
		$this->assertEquals( 200, $response->get_status() );
		// Now these items would be deleted, we wont have any mapping items left on db.
		$this->assertEquals( 0, count( $dbo->get_mapping_items() ) );
	}

	/** Test can get a single mapping item in correct format */
	public function test_single_mapping_item_should_return_correct_format() {
		// Create user with 'manage options' capability, only that user can delete this item.
		$user_id   = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		$dbo        = new Wordlift_Mapping_DBO();
		// We create a mapping item.
		$mapping_id = $dbo->insert_mapping_item( 'foo' );
		// We insert 2 rule groups for this mapping item.
		$rule_group_1 = $dbo->insert_rule_group( $mapping_id );
		$rule_group_2 = $dbo->insert_rule_group( $mapping_id );

		// We insert 1 rule for each rule group.
		$rule_1 = $dbo->insert_or_update_rule_item(
			array(
				'rule_group_id'    => $rule_group_1,
				'rule_field_one'   => 'foo',
				'rule_field_two'   => 'bar',
				'rule_logic_field' => '>',
			)
		);
		$rule_2 = $dbo->insert_or_update_rule_item(
			array(
				'rule_group_id'    => $rule_group_2,
				'rule_field_one'   => 'foo',
				'rule_field_two'   => 'bar',
				'rule_logic_field' => '>',
			)
		);

		// We insert 2 properties.
		$property_data       = array(
			'property_help_text'   => 'foo',
			'field_type_help_text' => 'bar',
			'field_help_text'      => 'foo',
			'transform_help_text'  => 'foo',
		);
		// 2 properties inserted.
		$dbo->insert_or_update_property( $mapping_id, $property_data );
		$dbo->insert_or_update_property( $mapping_id, $property_data );

		// We make a request to get info about single mapping item.
		$request = new WP_REST_Request(
			'GET',
			'/wordlift/v1/sync-mappings/mappings/' . $mapping_id
		);

		$response = $this->server->dispatch( $request );

		// This request should return 200.
		$this->assertEquals( 200, $response->get_status() );
		$response_data = $response->get_data();
		// Expect Two rule groups.
		$this->assertEquals( 2, count( $response_data['rule_group_list'] ) );
		// Expect two rules.
		$this->assertEquals( 2, count( $response_data['property_list'] ) );
		// Expect title to be foo.
		$this->assertEquals( 'foo', $response_data['mapping_title'] );
		// Expect correct mapping id.
		$this->assertEquals( $mapping_id, $response_data['mapping_id'] );
	}

}
