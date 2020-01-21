<?php
/**
 * Tests: Mappings Test.
 *
 * @since 3.25.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

use Wordlift\Mappings\Mappings_DBO;
use Wordlift\Mappings\Mappings_Validator;
use Wordlift\Mappings\Validators\Rule_Validators_Registry;
use Wordlift\Mappings\Validators\Taxonomy_Rule_Validator;

/**
 * Define the Wordlift_Mapping_Validator_Test class.
 *
 * @group mappings
 *
 * @since 3.25.0
 */
class Wordlift_Mapping_Validator_Test extends WP_UnitTestCase {

	/**
	 * The {@link Mappings_Validator} instance to test.
	 *
	 * @since  3.25.0
	 * @access private
	 * @var Mappings_Validator $validator The {@link Mappings_Validator} instance to test.
	 */
	private $validator;

	/**
	 * The {@link Mappings_DBO} instance to test.
	 *
	 * @since  3.25.0
	 * @access private
	 * @var Mappings_DBO $dbo The {@link Mappings_DBO} instance to test.
	 */
	private $dbo;

	/**
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();

		$this->dbo = new Mappings_DBO();
		$this->assertNotNull( $this->dbo, "Must be able to create a DBO instance." );

		$default_rule_validator   = new Taxonomy_Rule_Validator();
		$rule_validators_registry = new Rule_Validators_Registry( $default_rule_validator );
		$this->validator          = new Mappings_Validator( $this->dbo, $rule_validators_registry );
		$this->assertNotNull( $this->validator, "Must be able to create a validator instance." );

	}

	/** When the rules didnt match the post type it should return false */
	public function test_given_post_id_and_wrong_rules_should_return_false() {
		// Create a post with no post type.
		$post_id = $this->factory()->post->create( array( 'post_title' => 'Test Post' ) );
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
		$this->assertEmpty( $this->validator->validate( $post_id ) );
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
		$this->assertEmpty( $this->validator->validate( $post_id ) );

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
		$this->assertEmpty( $this->validator->validate( $post_id ) );
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
		$this->assertEmpty( $this->validator->validate( $post_id ) );

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
		$this->assertEmpty( $this->validator->validate( $post_id ) );
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
		$rule_id       = $this->dbo->insert_or_update_rule_item(
			array(
				'rule_field_one'   => 'post_type',
				'rule_logic_field' => '===',
				'rule_field_two'   => 'foo',
				'rule_group_id'    => $rule_group_id,
			)
		);
		$property_data = array(
			'property_name'      => 'foo',
			'field_type'         => 'bar',
			'field_name'         => 'foo',
			'transform_function' => 'foo',
			'mapping_id'         => $mapping_id,
		);
		$this->dbo->insert_or_update_property(
			$property_data
		);
		$property_data['property_status'] = Mappings_Validator::TRASH_CATEGORY;
		$this->dbo->insert_or_update_property(
			$property_data
		);
		// Should be true, since post type matches.
		$properties = $this->validator->validate( $post_id );
		$this->assertNotEmpty( $properties );
		// Should return only active properties.
		$this->assertEquals( 1, count( $properties ) );

	}

}
