<?php
/**
 * Tests: Mappings Test.
 *
 * @since 3.25.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Wordlift_Mapping_Validator_Test class.
 *
 * @since 3.25.0
 */
class Wordlift_Mapping_Validator_Test extends WP_UnitTestCase {

	/**
	 * The {@link Wordlift_Mapping_Validator} instance to test.
	 *
	 * @since  3.25.0
	 * @access private
	 * @var \Wordlift_Mapping_Validator $validator The {@link Wordlift_Mapping_Validator} instance to test.
	 */
	private $validator;

	/**
	 * The {@link Wordlift_Mapping_DBO} instance to test.
	 *
	 * @since  3.25.0
	 * @access private
	 * @var \Wordlift_Mapping_DBO $dbo The {@link Wordlift_Mapping_DBO} instance to test.
	 */
	private $dbo;

	/**
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();	
		$this->dbo       = new Wordlift_Mapping_DBO();
		$this->validator = new Wordlift_Mapping_Validator();
	}
	/** Check if validator class can be initalised */
	public function test_can_initialize_validator() {
		$this->assertNotNull( new Wordlift_Mapping_Validator() );
	}

	/** When the rules didnt match the post type it should return false */
	public function test_given_post_id_and_wrong_rules_should_return_false() {
		// Create a post with no post type.
		$post_id = $this->factory->post->create( array( 'post_title' => 'Test Post' ) );
		// Create a mapping item with single rule group and rule.
		$mapping_id = $this->dbo->insert_mapping_item( 'foo' );
		// Create a rule group.
		$rule_group_id = $this->dbo->insert_rule_group( $mapping_id );
		// Create a rule to match post type foo.
		$rule_id = $this->dbo->insert_or_update_rule_item(
			array(
				'rule_field_one'   => 'post_type',
				'rule_logic_field' => '===',
				'rule_field_two'   => 'foo',
				'rule_group_id'    => $rule_group_id,
			)
		);
		// Since this post didnt have that post type, it should return false.
		$this->assertFalse( $this->validator->validate( $post_id ) );
	}

	/** When the rules didmatch the post type it should return true */
	public function test_given_post_id_and_correct_rules_should_return_true() {
		// Create a post with no post type.
		$post_id = $this->factory->post->create(
			array(
				'post_title' => 'Test Post',
				'post_type'  => 'foo',
			)
		);
		// Create a mapping item with single rule group and rule.
		$mapping_id = $this->dbo->insert_mapping_item( 'foo' );
		// Create a rule group.
		$rule_group_id = $this->dbo->insert_rule_group( $mapping_id );
		// Create a rule to match post type foo.
		$rule_id = $this->dbo->insert_or_update_rule_item(
			array(
				'rule_field_one'   => 'post_type',
				'rule_logic_field' => '===',
				'rule_field_two'   => 'foo',
				'rule_group_id'    => $rule_group_id,
			)
		);
		// Since this post didnt have that post type, it should return false.
		$this->assertTrue( $this->validator->validate( $post_id ) );
		// Add another rule group contradicting the first rule, it should
		// return false now.
		$this->dbo->insert_or_update_rule_item(
			array(
				'rule_field_one'   => 'post_type',
				'rule_logic_field' => '!==',
				'rule_field_two'   => 'foo',
				'rule_group_id'    => $rule_group_id,
			)
		);
		$this->assertFalse( $this->validator->validate( $post_id ) );
	}
}
