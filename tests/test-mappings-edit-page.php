<?php
/**
 * Tests: Edit mapping page
 *
 * This file defines tests for the {@link \Wordlift\Mappings\Pages\Edit_Mappings_Page} class.
 *
 * @since   3.25.0
 * @package Wordlift
 */

use Wordlift\Features\Features_Registry;
use Wordlift\Mappings\Mappings_Transform_Functions_Registry;
use Wordlift\Mappings\Pages\Edit_Mappings_Page;

/**
 * Class Edit_Mapping_Page_Test
 * @group mappings
 */
class Edit_Mapping_Page_Test extends Wordlift_Unit_Test_Case {

	/**
	 * @var Edit_Mappings_Page $edit_mappings_page_instance
	 * The instance of {@link \Wordlift\Mappings\Pages\Edit_Mappings_Page }
	 */
	private $edit_mappings_page_instance;

	/**
	 * @var array Ui settings array in the edit mappings page.
	 */
	private $ui_settings_array;

	public function setUp() {
		parent::setUp();
		/**
		 * Reset filters to prevent filter duplication
		 */
		global $wp_filter;
		$features_registry = Features_Registry::get_instance();
		$features_registry->clear_all();

		$wp_filter = array();
		$wordlift  = new Wordlift();
		$wordlift->run();
		$features_registry->initialize_all_features();

		$this->edit_mappings_page_instance = new Edit_Mappings_Page(
			new Mappings_Transform_Functions_Registry()
		);
		$this->ui_settings_array           = $this->edit_mappings_page_instance->get_ui_settings_array();
	}

	/**
	 * Test check if the text items for settings are loaded correctly.
	 */
	public function test_correctly_loading_text_items_in_ui_array() {
		$this->assertArrayHasKey( 'wl_add_mapping_text', $this->ui_settings_array );
		$this->assertArrayHasKey( 'wl_edit_mapping_text', $this->ui_settings_array );
		$this->assertArrayHasKey( 'wl_edit_mapping_no_item', $this->ui_settings_array );
		$this->assertArrayHasKey( 'page', $this->ui_settings_array );
	}

	/**
	 * Test check if the rest params are loaded
	 */
	public function test_rest_params_are_loaded() {
		$this->assertArrayHasKey( 'wl_edit_mapping_rest_nonce', $this->ui_settings_array );
		$this->assertArrayHasKey( 'rest_url', $this->ui_settings_array );
	}

	/**
	 * This test should verify all the terms are loaded from the REST API, so taxonomy should have
	 * zero terms in their value.
	 */
	public function test_check_there_is_no_terms_in_taxonomy_array() {
		$this->assertFalse( is_admin() );

		$this->assertArrayHasKey( 'wl_rule_field_one_options', $this->ui_settings_array, 'Taxonomy options should be present' );
		$this->assertArrayHasKey( 'wl_rule_field_two_options', $this->ui_settings_array, 'Taxonomy term options should be present' );
		$taxonomy_options = $this->ui_settings_array['wl_rule_field_one_options'];
		$term_options     = $this->ui_settings_array['wl_rule_field_two_options'];
		// we are getting only items which are not post type and taxonomy.
		$filtered_term_options = array_filter( $term_options, function ( $item ) {
			return $item['parent_value'] !== 'post_type' && $item['parent_value'] !== 'taxonomy';
		} );

		$this->assertCount( 0, $filtered_term_options, 'This is what I got: ' . var_export( array(
				'ui_settings_array'     => $this->ui_settings_array,
				'filtered_term_options' => $filtered_term_options
			), true ) );
	}

	/**
	 * This test should verify that the dependencies are provided via constructor
	 */
	public function test_if_dependencies_are_provided_via_constructor() {
		$this->assertNotNull( $this->edit_mappings_page_instance->transform_function_registry, '
		Registry service must be provided via constructor' );
	}


	/**
	 * This test checks if the logic options are loaded
	 */
	public function test_logic_field_options_should_be_loaded() {
		$this->assertArrayHasKey( 'wl_logic_field_options', $this->ui_settings_array, 'Logic field options should be present.' );
		// there should be 2 logic field options.
		$this->assertCount( 2, $this->ui_settings_array['wl_logic_field_options'] );
		$expected_logic_field_options = array(
			array(
				'label' => __( 'is equal to', 'wordlift' ),
				'value' => '===',
			),
			array(
				'label' => __( 'is not equal to', 'wordlift' ),
				'value' => '!==',
			),
		);
		$this->assertEquals( $expected_logic_field_options, $this->ui_settings_array['wl_logic_field_options'] );
	}

	/**
	 * Test when the nonce is posted, should have the mapping_id in the settings.
	 */
	public function test_when_nonce_is_posted_then_valid_mapping_id_should_be_present() {
		$_REQUEST['wl_edit_mapping_id']     = 2;
		$_REQUEST['_wl_edit_mapping_nonce'] = wp_create_nonce( 'wl-edit-mapping-nonce' );
		$ui_settings_array                  = $this->edit_mappings_page_instance->get_ui_settings_array();
		$this->assertArrayHasKey( 'wl_edit_mapping_id', $ui_settings_array );
		$this->assertEquals( $ui_settings_array['wl_edit_mapping_id'], 2 );
	}

	/**
	 * Test whether the taxonomy and taxonomy options are loaded correctly
	 */
	public function test_whether_the_taxonomy_options_are_loaded_correctly() {
		register_taxonomy( 'foo', 'post' );
		register_post_type( 'foo_post_type' );
		$ui_settings_array = $this->edit_mappings_page_instance->get_ui_settings_array();
		// we have added taxonomy and post type, lets make sure they are in the options.
		$this->assertArrayHasKey( 'wl_rule_field_one_options', $ui_settings_array );
		$this->assertArrayHasKey( 'wl_rule_field_two_options', $ui_settings_array );
		// we need to have this post type in the wl_rule_field_one_options
		$expected_post_type = array_filter( $ui_settings_array['wl_rule_field_one_options'], function ( $item ) {
			return $item['value'] === 'post_type' || $item['value'] === 'foo';
		} );
		// 1 post type should be present
		$this->assertCount( 2, $expected_post_type );
		$expected_taxonomy = array_filter( $ui_settings_array['wl_rule_field_two_options'], function ( $item ) {
			return $item['value'] === 'foo_post_type';
		} );
		$this->assertCount( 1, $expected_taxonomy );

	}

	/**
	 * Test if the field type options are correctly loaded.
	 */
	public function test_check_if_field_type_options_loaded() {
		$ui_settings_array = $this->ui_settings_array;
		$this->assertArrayHasKey( 'wl_field_type_options', $ui_settings_array );
		$this->assertArrayHasKey( 'wl_field_name_options', $ui_settings_array );
		// Text and custom field should be present since acf wont be loaded.
		$this->assertCount( 2, $ui_settings_array['wl_field_type_options'] );
		$this->assertCount( 2, $ui_settings_array['wl_field_name_options'] );

	}

	/**
	 * Check if the taxonomy term options are loaded correctly
	 */
	public function test_on_edit_mappings_page_taxonomy_option_should_be_loaded() {
		register_taxonomy( 'foo', 'post' );
		// Expect taxonomy options should have foo in the list.
		$settings               = $this->edit_mappings_page_instance->get_ui_settings_array();
		$rule_field_one_options = $settings['wl_rule_field_one_options'];
		// We should have taxonomy in the rule field one options.
		$result = array_filter( $rule_field_one_options, function ( $item ) {
			return $item['label'] === 'Taxonomy' && $item['value'] === 'taxonomy';
		} );
		$this->assertCount( 1, $result, 'Taxonomy option should be present in rule field one options, instead this is what I got: ' . var_export( $result, true ) );
	}

	public function test_on_edit_mappings_page_all_taxonomy_options_should_be_loaded_on_rule_field_two() {
		$settings               = $this->edit_mappings_page_instance->get_ui_settings_array();
		$rule_field_two_options = $settings['wl_rule_field_two_options'];
		// get all registered taxonomies
		$expected_count = count( get_object_taxonomies( 'post' ) );
		$current        = array_filter( $rule_field_two_options, function ( $item ) {
			return array_key_exists( 'parent_value', $item ) && $item['parent_value'] === 'taxonomy';
		} );
		$current_count  = count( $current );
		$this->assertEquals( $expected_count, $current_count, 'This is what I got: ' . var_export( $current, true ) );
	}
}
