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
class Wordlift_Mapping_DBO_Test extends WP_UnitTestCase {

	/**
	 * The {@link Wordlift_Mapping_REST_Controller} instance to test.
	 *
	 * @since  3.25.0
	 * @access private
	 * @var \Wordlift_Mapping_DBO $dbo_instance The {@link Wordlift_Mapping_DBO} instance to test.
	 */
	private $dbo_instance;

	/**
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		global $wpdb;
		$this->wpdb         = $wpdb;
		$this->dbo_instance = new Wordlift_Mapping_DBO();
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
		$mapping_id = $this->dbo_instance->insert_mapping_item( "foo title" );
		$mapping_table_name = WL_MAPPING_TABLE_NAME;
		// Update this title.
		$this->dbo_instance->update_mapping_item( $mapping_id, "foo" );
		// Count all titles with string foo, it should be 1.
		$count = $this->wpdb->get_var( "SELECT COUNT(mapping_id) as total FROM {$this->wpdb->prefix}$mapping_table_name WHERE mapping_title='foo'" );
		$this->assertEquals( 1, $count );
	}

	/** When rule item is given should insert it to db.*/
	public function test_given_rule_fields_should_insert_rule() {
		$rule_table_name = $this->wpdb->prefix . WL_RULE_TABLE_NAME;
		$rule_group_table_name = $this->wpdb->prefix . WL_RULE_GROUP_TABLE_NAME;
		$mapping_id = $this->dbo_instance->insert_mapping_item( "foo title" );
		$this->dbo_instance->insert_rule_item( $mapping_id, 'foo', '>', 'bar' );
		// we have inserted a rule item, so count should be 1.
		$count = $this->wpdb->get_var( "SELECT COUNT(rule_id) as total FROM $rule_table_name" );
		$this->assertEquals( 1, $count );
		// When inserting/updating rule, there should be row created at rule group table.
		$rule_group_count = $this->wpdb->get_var( "SELECT COUNT(rule_id) as total FROM $rule_group_table_name" );
		$this->assertEquals( 1, $rule_group_count );		
	}


	/** When rule id is given should update it in db. */
	public function test_given_rule_id_should_update_rule() {
		$rule_table_name = $this->wpdb->prefix . WL_RULE_TABLE_NAME;
		$mapping_id = $this->dbo_instance->insert_mapping_item( "foo title" );
		$rule_id         = $this->dbo_instance->insert_rule_item( $mapping_id, 'foo', '>', 'bar' );
		$this->dbo_instance->update_rule_item(
			array(
				'rule_field_one' => 'bar',
				'rule_id'        => $rule_id,
				'mapping_id'     => $mapping_id,
			)
		);
		// Count all rule field one with value bar.
		$count = $this->wpdb->get_var( "SELECT COUNT(rule_field_one) as total FROM $rule_table_name WHERE rule_field_one='bar'" );
		$this->assertEquals( 1, $count );
		$rule_group_table_name = $this->wpdb->prefix . WL_RULE_GROUP_TABLE_NAME;
		// When inserting/updating rule, there should be row created at rule group table.
		$rule_group_count = $this->wpdb->get_var( "SELECT COUNT(rule_id) as total FROM $rule_group_table_name" );
		$this->assertEquals( 1, $rule_group_count );
	}

}
