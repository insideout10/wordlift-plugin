<?php
/**
 * Tests: Mappings Test.
 *
 * @since 3.25.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

use Wordlift\Mappings\Mappings_DBO;
use Wordlift\Mappings\Mappings_REST_Controller;

/**
 * Define the Mappings_REST_Controller_Test class.
 *
 * @group mappings
 *
 * @since 3.25.0
 */
class Mappings_REST_Controller_Test extends WP_UnitTestCase {

	/**
	 * The {@link Mappings_REST_Controller} instance to test.
	 *
	 * @since  3.25.0
	 * @access private
	 * @var Mappings_REST_Controller $rest_instance The {@link Mappings_REST_Controller} instance to test.
	 */
	private $rest_instance;

	/**
	 * Our expected route for rest api.
	 */
	protected $mapping_route = '/wordlift/v1/mappings';
	/**
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();	

		$this->rest_instance = new Mappings_REST_Controller();
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
	 * Test if rest route exists for inserting/updating mapping item.
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
		$request   = new WP_REST_Request( 'POST', $this->mapping_route );
		$request->set_header( 'content-type', 'application/json' );
		$request->set_body( $json_data );		
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
		$dbo = new Mappings_DBO();
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
		$dbo          = new Mappings_DBO();
		$mapping_id_1 = $dbo->insert_mapping_item( 'foo' );
		$mapping_id_2 = $dbo->insert_mapping_item( 'bar' );

		$request = new WP_REST_Request( 'DELETE', $this->mapping_route );
		$json_data = wp_json_encode(
			array(
				'mapping_items' => array(
					array(
						'mapping_id' => $mapping_id_1,
					),
					array(
						'mapping_id' => $mapping_id_2,
					),
				),
			)
		);
		$request->set_header( 'content-type', 'application/json' );
		$request->set_body( $json_data );
		$response = $this->server->dispatch( $request );
		// This request should return 200.
		$this->assertEquals( 200, $response->get_status() );
		// Now these items would be deleted, we wont have any mapping items left on db.
		$this->assertEquals( 0, count( $dbo->get_mappings() ) );
	}

	/** Test can get a single mapping item in correct format */
	public function test_single_mapping_item_should_return_correct_format() {
		// Create user with 'manage options' capability, only that user can delete this item.
		$user_id   = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		$mapping_id = $this->inject_mock_data_for_mapping_id();
		// We make a request to get info about single mapping item.
		$request = new WP_REST_Request(
			'GET',
			'/wordlift/v1/mappings/' . $mapping_id
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

	/** When list of mapping items posted to update endpoint, we need to update mapping items */
	public function test_multiple_mapping_item_posted_to_update_endpoint_should_update() {
		$user_id   = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );	
		$dbo = new Mappings_DBO();
		// Lets make 2 mapping items on db.
		$mapping_id_1 = $dbo->insert_mapping_item( 'foo' );
		$mapping_id_2 = $dbo->insert_mapping_item( 'bar' );
		// Lets make a post array.
		$post_array = array(
			'mapping_items' => array(
				array(
					'mapping_id'     => $mapping_id_1,
					'mapping_title'  => 'foo',
					'mapping_status' => 'trash',
				),
				array(
					'mapping_id'     => $mapping_id_2,
					'mapping_title'  => 'bar',
					'mapping_status' => '',
				),
			),
		);
		$mapping_table_name = $this->wpdb->prefix . WL_MAPPING_TABLE_NAME;
		$json_data = json_encode( $post_array );
		$request   = new WP_REST_Request( 'PUT', $this->mapping_route );
		$request->set_header( 'content-type', 'application/json' );
		$request->set_body( $json_data );		
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status() );
		$trash_count = $this->wpdb->get_var( "SELECT COUNT(mapping_id) as total FROM $mapping_table_name WHERE mapping_status='trash'" );
		$this->assertEquals( 1, $trash_count );
	}
	/**
	 * Inject some mock data used for testing.
	 * @return $mapping_id
	 */
	private function inject_mock_data_for_mapping_id() {
		$dbo = new Mappings_DBO();
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
			'property_name'   => 'foo',
			'field_type' => 'bar',
			'field_name'      => 'foo',
			'transform_function'  => 'foo',
		);
		$property_data['mapping_id'] = $mapping_id;
		// 2 properties inserted.
		$dbo->insert_or_update_property( $property_data );
		$dbo->insert_or_update_property( $property_data );
		return $mapping_id;	
	}

	/** Test can clone a mapping item */
	public function test_given_mapping_id_can_create_clone() {
		// Create user with 'manage options' capability, only that user can delete this item.
		$user_id    = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		$mapping_id = $this->inject_mock_data_for_mapping_id();
		$post_array = array(
			'mapping_items' => array(
				array(
					'mapping_id'     => $mapping_id,
					'mapping_title'  => 'foo',
					'mapping_status' => 'trash',
				),
			),
		);
		$json_data = json_encode( $post_array );
		// Some data present for the mapping id, we are going to clone this mapping id.
		// We make a request to get info about single mapping item.
		$request = new WP_REST_Request(
			'POST',
			'/wordlift/v1/mappings/clone'
		);
		$request->set_header( 'content-type', 'application/json' );
		$request->set_body( $json_data );
		$response = $this->server->dispatch( $request );
		$dbo = new Mappings_DBO();

		$this->assertEquals( 200, $response->get_status() );
		// we should have 2 mapping items in db.
		$this->assertEquals( 2, count( $dbo->get_mappings() ) );
		$rule_group_table_name = $this->wpdb->prefix . WL_RULE_GROUP_TABLE_NAME;
		$rule_table_name = $this->wpdb->prefix . WL_RULE_TABLE_NAME;
		$rule_group_count = $this->wpdb->get_var( "SELECT COUNT(DISTINCT rule_group_id) as total FROM $rule_group_table_name" );
		$rule_count = $this->wpdb->get_var( "SELECT COUNT(DISTINCT rule_group_id) as total FROM $rule_table_name" );
		$property_table_name = $this->wpdb->prefix . WL_PROPERTY_TABLE_NAME;
		$property_count      = $this->wpdb->get_var( "SELECT COUNT(property_id) as total FROM $property_table_name" );
		// we should have 4 rule groups and 4 rules.
		$this->assertEquals( 4, $rule_group_count );
		// we should have 4 rules in the rule table.
		$this->assertEquals( 4, $rule_count );
		// we should have 4 properties in the property table.
		$this->assertEquals( 4, $property_count );
	}
	/** Test when the property is not posted it should be deleted  */
	public function test_when_property_is_not_posted_should_delete_property() {
		// Create user with 'manage options' capability, only that user can delete this item.
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		$mapping_id = $this->inject_mock_data_for_mapping_id();
		$post_array = array(
			'mapping_id'     => $mapping_id,
			'mapping_title'  => 'foo',
			'mapping_status' => 'active',
			'property_list' => array(),
			'rule_group_list' => array(),
		);
		$request   = new WP_REST_Request( 'POST', $this->mapping_route );
		$request->set_header( 'content-type', 'application/json' );
		$request->set_body( json_encode( $post_array ) );		
		$response  = $this->server->dispatch( $request );
		// Request should return 200.
		$this->assertEquals( 200, $response->get_status() );
		// We didnt post the property data, so it should be removed.
		$property_table_name = $this->wpdb->prefix . WL_PROPERTY_TABLE_NAME;
		$property_count      = $this->wpdb->get_var( "SELECT COUNT(property_id) as total FROM $property_table_name" );
		$this->assertEquals( 0, $property_count );		
	}

	/** Test when rule group is not posted, then it should be deleted */
	public function test_when_rule_group_not_posted_should_be_deleted() {
		// Create user with 'manage options' capability, only that user can delete this item.
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		$mapping_id = $this->inject_mock_data_for_mapping_id();
		$post_array = array(
			'mapping_id'     => $mapping_id,
			'mapping_title'  => 'foo',
			'mapping_status' => 'active',
			'property_list' => array(),
			'rule_group_list' => array(),
		);
		$rule_group_table_name = $this->wpdb->prefix . WL_RULE_GROUP_TABLE_NAME;
		$rule_table_name       = $this->wpdb->prefix . WL_RULE_TABLE_NAME;
		$rule_group_count      = $this->wpdb->get_var( "SELECT COUNT(*) as total FROM $rule_group_table_name" );
		$this->assertEquals( 2, $rule_group_count );
		$request   = new WP_REST_Request( 'POST', $this->mapping_route );
		$request->set_header( 'content-type', 'application/json' );
		$request->set_body( json_encode( $post_array ) );		
		$response  = $this->server->dispatch( $request );
		// var_dump( $response );
		// // Request should return 200.
		// $this->assertEquals( 200, $response->get_status() );
		// We didnt post the rule group data, so it should be removed.
		$rule_group_count      = $this->wpdb->get_var( "SELECT COUNT(*) as total FROM $rule_group_table_name" );
		$rule_count            = $this->wpdb->get_var( "SELECT COUNT(*) as total FROM $rule_table_name" );
		$this->assertEquals( 0, $rule_group_count );
		$this->assertEquals( 0, $rule_count );
	}


	public function test_can_get_terms_by_posting_the_taxonomy() {
		// Create a taxonomy and some terms.
		register_taxonomy('foo', 'post' );
		wp_insert_term( 'foo term 1', 'foo' );
		wp_insert_term( 'foo term 2', 'foo' );
		wp_insert_term( 'foo term 3', 'foo' );
		// When we request the endpoint for terms it should get the terms.
		$user_id = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		// Construct the post array.
		$post_array = array(
			'taxonomy' => 'foo'
		);
		$request   = new WP_REST_Request(
			'POST',
			'/wordlift/v1/mappings/get_terms'
		);
		$request->set_header( 'content-type', 'application/json' );
		$request->set_body( json_encode( $post_array ) );
		$response  = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status() );
		// we have 3 terms on the data.
		$this->assertEquals( 3, count( $response->get_data() ) );
	}
}
