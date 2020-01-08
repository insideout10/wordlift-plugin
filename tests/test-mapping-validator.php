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

	/** When the rules did match the post type it should return true */
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

	/** Test when given correct taxonomy, should return true */
	public function test_given_correct_taxonomy_should_return_true() {
		$post_id = $this->factory->post->create(
			array(
				'post_title' => 'Test Post',
			)
		);
		register_taxonomy( 'foo', 'post' );
		// Make sure the taxonomy exists.
		$this->assertTrue(
			taxonomy_exists(
				'foo'
			)
		);
		// Add terms to taxonomy.
		$term_id = wp_insert_term( 'bar', 'foo' );
		// Add taxonomy term to post.
		wp_set_object_terms( $post_id, array( 'bar' ), 'foo', true );
		// Create a mapping item with single rule group and rule.
		$mapping_id = $this->dbo->insert_mapping_item( 'foo' );
		// Create a rule group.
		$rule_group_id = $this->dbo->insert_rule_group( $mapping_id );
		// Create a rule to match the tax type foo.
		$rule_id = $this->dbo->insert_or_update_rule_item(
			array(
				'rule_field_one'   => 'foo',
				'rule_logic_field' => '===',
				'rule_field_two'   => (string) $term_id['term_id'],
				'rule_group_id'    => $rule_group_id,
			)
		);
		// Since this post have correct taxonomy term, should return true.
		$this->assertTrue( $this->validator->validate( $post_id ) );
		// Lets insert another rule which says not equal to the term id.
		$this->dbo->insert_or_update_rule_item(
			array(
				'rule_field_one'   => 'foo',
				'rule_logic_field' => '!==',
				'rule_field_two'   => (string) $term_id['term_id'],
				'rule_group_id'    => $rule_group_id,
			)
		);
		// The above rule should make validator return false.
		$this->assertFalse( $this->validator->validate( $post_id ) );
	}

	/** For a valid mapping item should return properties */
	public function test_given_valid_rule_return_properties() {
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
		$property_data = array(
			'property_name'   => 'foo',
			'field_type' => 'bar',
			'field_name'      => 'foo',
			'transform_function'  => 'foo',
			'mapping_id'           => $mapping_id,
		);
		$this->dbo->insert_or_update_property(
			$property_data
		);
		$property_data['property_status'] = Wordlift_Mapping_Validator::TRASH_CATEGORY;
		$this->dbo->insert_or_update_property(
			$property_data
		);
		// Should be true, since post type matches.
		$this->assertTrue( $this->validator->validate( $post_id ) );
		$properties = $this->validator->get_valid_properties();
		// Should return only active properties.
		$this->assertEquals( 1, count( $properties ) );
	}
}
