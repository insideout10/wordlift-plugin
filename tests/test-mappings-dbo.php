<?php
/**
 * Tests: Mappings Test.
 *
 * @since 3.25.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

use Wordlift\Mappings\Mappings_DBO;

/**
 * Define the Mappings_REST_Controller_Test class.
 *
 * @group mappings
 *
 * @since 3.25.0
 */
class Wordlift_Mapping_DBO_Test extends WP_UnitTestCase {

	/**
	 * The {@link Mappings_REST_Controller} instance to test.
	 *
	 * @since  3.25.0
	 * @access private
	 * @var Mappings_DBO $dbo_instance The {@link Mappings_DBO} instance to test.
	 */
	private $dbo_instance;

	/**
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		global $wpdb;
		$this->wpdb         = $wpdb;
		$this->dbo_instance = new Mappings_DBO();
	}

	/**
	 * Testing if instance is not null, check to determine this class is
	 * included.
	 */
	public function test_instance_not_null() {
		$this->assertNotNull( $this->dbo_instance );
	}

	/** When mapping item is given can insert the item */
	public function test_given_mapping_title_can_insert_mapping_item() {
		$this->dbo_instance->insert_mapping_item( 'some title' );
		$mapping_table_name = WL_MAPPING_TABLE_NAME;
		// we have inserted a mapping item, so count should be 1.
		$count = $this->wpdb->get_var( "SELECT COUNT(mapping_id) as total FROM {$this->wpdb->prefix}$mapping_table_name" );
		$this->assertEquals( 1, $count );
	}

	/** When mapping item is given can update the item.*/
	public function test_given_mapping_id_and_title_update_mapping_item() {
		$mapping_id         = $this->dbo_instance->insert_mapping_item( "foo title" );
		$mapping_table_name = WL_MAPPING_TABLE_NAME;
		// Update this title.
		$this->dbo_instance->insert_or_update_mapping_item(
			array(
				'mapping_id'    => $mapping_id,
				'mapping_title' => "foo"
			)
		);
		// Count all titles with string foo, it should be 1.
		$count = $this->wpdb->get_var( "SELECT COUNT(mapping_id) as total FROM {$this->wpdb->prefix}$mapping_table_name WHERE mapping_title='foo'" );
		$this->assertEquals( 1, $count );
	}

	/** When rule item is given should insert it to db.*/
	public function test_given_rule_fields_should_insert_rule() {
		$rule_table_name       = $this->wpdb->prefix . WL_RULE_TABLE_NAME;
		$rule_group_table_name = $this->wpdb->prefix . WL_RULE_GROUP_TABLE_NAME;
		$mapping_id            = $this->dbo_instance->insert_mapping_item( "foo title" );
		$rule_group_id         = $this->dbo_instance->insert_rule_group( $mapping_id );
		$this->dbo_instance->insert_or_update_rule_item(
			array(
				'rule_group_id'    => $rule_group_id,
				'rule_field_one'   => 'foo',
				'rule_logic_field' => '>',
				'rule_field_two'   => 'bar'
			)
		);
		// we have inserted a rule item, so count should be 1.
		$count = $this->wpdb->get_var( "SELECT COUNT(rule_id) as total FROM $rule_table_name" );
		$this->assertEquals( 1, $count );
		// When inserting/updating rule, there should be row created at rule group table.
		$rule_group_count = $this->wpdb->get_var( "SELECT COUNT(rule_group_id) as total FROM $rule_group_table_name" );
		$this->assertEquals( 1, $rule_group_count );
	}

	/** Delete a rule along with its rule group entry */
	public function test_able_to_delete_rule_item() {
		$rule_table_name = $this->wpdb->prefix . WL_RULE_TABLE_NAME;
		$mapping_id      = $this->dbo_instance->insert_mapping_item( "foo title" );
		$rule_group_id   = $this->dbo_instance->insert_rule_group( $mapping_id );
		$rule_id         = $this->dbo_instance->insert_or_update_rule_item(
			array(
				'rule_group_id'    => $rule_group_id,
				'rule_field_one'   => 'foo',
				'rule_logic_field' => '>',
				'rule_field_two'   => 'bar'
			)
		);
		$this->dbo_instance->delete_rule_item( $rule_id );
		// The item should be deleted from rule table.
		$rule_table_count = $this->wpdb->get_var( "SELECT COUNT(rule_field_one) as total FROM $rule_table_name" );
		$this->assertEquals( 0, $rule_table_count );
	}

	/** Able to insert property */
	public function test_given_property_should_insert_property() {
		$property_table_name         = $this->wpdb->prefix . WL_PROPERTY_TABLE_NAME;
		$property_data               = array(
			'property_name'      => 'foo',
			'field_type'         => 'bar',
			'field_name'         => 'foo',
			'transform_function' => 'foo',
		);
		$mapping_id                  = $this->dbo_instance->insert_mapping_item( "foo title" );
		$property_data['mapping_id'] = $mapping_id;
		$this->dbo_instance->insert_or_update_property( $property_data );
		$property_table_count = $this->wpdb->get_var( "SELECT COUNT(mapping_id) as total FROM $property_table_name" );
		$this->assertEquals( 1, $property_table_count );
	}

	/** Able to delete property */
	public function test_given_property_id_should_delete_property() {
		$property_table_name         = $this->wpdb->prefix . WL_PROPERTY_TABLE_NAME;
		$property_data               = array(
			'property_name'      => 'foo',
			'field_type'         => 'bar',
			'field_name'         => 'foo',
			'transform_function' => 'foo',
		);
		$mapping_id                  = $this->dbo_instance->insert_mapping_item( "foo title" );
		$property_data['mapping_id'] = $mapping_id;
		$property_id                 = $this->dbo_instance->insert_or_update_property( $property_data );
		$this->dbo_instance->delete_property( $property_id );
		$property_table_count = $this->wpdb->get_var( "SELECT COUNT(mapping_id) as total FROM $property_table_name" );
		$this->assertEquals( 0, $property_table_count );
	}

	/** Able to list mapping items */
	public function test_get_mapping_items() {
		// Lets insert a mapping item.
		$this->dbo_instance->insert_mapping_item( 'foo' );
		// we will have 1 item in db.
		$this->assertEquals( count( $this->dbo_instance->get_mappings() ), 1 );
	}


	/** Able to delete mapping items */
	public function test_delete_mapping_item() {
		// Lets insert a mapping item.
		$mapping_id = $this->dbo_instance->insert_mapping_item( 'foo' );
		$this->dbo_instance->delete_mapping_item( $mapping_id );
		$this->assertEquals( 0, count( $this->dbo_instance->get_mappings() ) );
	}

	/** Able to get properties for a mapping id */
	public function test_can_get_property_items() {
		// Lets insert a mapping item.
		$mapping_id    = $this->dbo_instance->insert_mapping_item( 'foo' );
		$property_data = array(
			'property_name'      => 'foo',
			'field_type'         => 'bar',
			'field_name'         => 'foo',
			'transform_function' => 'foo',
		);
		// 2 properties inserted.
		$property_data['mapping_id'] = $mapping_id;
		$this->dbo_instance->insert_or_update_property( $property_data );
		$this->dbo_instance->insert_or_update_property( $property_data );
		// 2 properties should be returned.
		$property_rows = $this->dbo_instance->get_properties( $mapping_id );
		$this->assertEquals( count( $property_rows ), 2 );
	}

	/** Test can get rule group items with rules by mapping id */
	public function test_can_get_rule_group_with_rules() {
		// Lets insert a mapping item.
		$mapping_id = $this->dbo_instance->insert_mapping_item( 'foo' );
		// Lets insert some rule groups
		// We insert 2 rule groups for this mapping item.
		$rule_group_1 = $this->dbo_instance->insert_rule_group( $mapping_id );
		$rule_group_2 = $this->dbo_instance->insert_rule_group( $mapping_id );
		// We insert 1 rule for each rule group.
		$rule_1           = $this->dbo_instance->insert_or_update_rule_item(
			array(
				'rule_group_id'    => $rule_group_1,
				'rule_field_one'   => 'foo',
				'rule_field_two'   => 'bar',
				'rule_logic_field' => '>',
			)
		);
		$rule_2           = $this->dbo_instance->insert_or_update_rule_item(
			array(
				'rule_group_id'    => $rule_group_2,
				'rule_field_one'   => 'foo',
				'rule_field_two'   => 'bar',
				'rule_logic_field' => '>',
			)
		);
		$rule_groups_data = $this->dbo_instance->get_rule_groups_by_mapping( $mapping_id );
		$this->assertEquals( count( $rule_groups_data ), 2 );
		$this->assertEquals( count( $rule_groups_data[0]['rules'] ), 1 );
		$this->assertEquals( count( $rule_groups_data[1]['rules'] ), 1 );
	}

	/** Given mapping id returns correct mapping title */
	public function test_get_mapping_item_data() {
		// Lets insert a mapping item.
		$mapping_id       = $this->dbo_instance->insert_mapping_item( 'foo' );
		$mapping_row_data = $this->dbo_instance->get_mapping_item_data( $mapping_id );
		$this->assertEquals( 'foo', $mapping_row_data['mapping_title'] );
	}
}
