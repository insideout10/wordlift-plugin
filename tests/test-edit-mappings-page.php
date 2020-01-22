<?php
/**
 * Tests: Edit mapping page
 *
 * This file defines tests for the {@link \Wordlift\Mappings\Pages\Edit_Mappings_Page} class.
 *
 * @since   3.25.0
 * @package Wordlift
 */

class Edit_Mapping_Page_Test extends Wordlift_Unit_Test_Case {

	/**
	 * @var \Wordlift\Mappings\Pages\Edit_Mappings_Page $edit_mappings_page_instance
	 * The instance of {@link \Wordlift\Mappings\Pages\Edit_Mappings_Page }
	 */
	private $edit_mappings_page_instance;

	/**
	 * @var array Ui settings array in the edit mappings page.
	 */
	private $ui_settings_array;
	public function setUp() {
		parent::setUp();
		$this->edit_mappings_page_instance = new \Wordlift\Mappings\Pages\Edit_Mappings_Page(
			new \Wordlift\Mappings\Mappings_Transform_Functions_Registry()
		);
		$this->ui_settings_array = $this->edit_mappings_page_instance->get_ui_settings_array();
	}

	/**
	 * Test check if the text items for settings are loaded correctly.
	 */
	public function test_correctly_loading_text_items_in_ui_array() {
		$this->assertArrayHasKey( 'wl_add_mapping_text', $this->ui_settings_array );
		$this->assertArrayHasKey( 'wl_edit_mapping_text', $this->ui_settings_array );
		$this->assertArrayHasKey( 'wl_edit_mapping_no_item', $this->ui_settings_array );
	}

	/**
	 * This test should verify all the terms are loaded from the REST API, so taxonomy should have
	 * zero terms in their value.
	 */
	public function test_check_there_is_no_terms_in_taxonomy_array() {
		$this->assertArrayHasKey( 'wl_rule_field_one_options',  $this->ui_settings_array, 'Taxonomy options should be present');
		$this->assertArrayHasKey( 'wl_rule_field_two_options',  $this->ui_settings_array, 'Taxonomy term options should be present');
		$taxonomy_options = $this->ui_settings_array['wl_rule_field_one_options'];
		$term_options = $this->ui_settings_array['wl_rule_field_two_options'];
		// we are getting only items which are not post type.
		$filtered_term_options = array_filter( $term_options, function ( $item ) {
			return $item['taxonomy'] !== 'post_type';
		});
		$this->assertCount( 0, $filtered_term_options );
	}

	/**
	 * This test should verify that the dependencies are provided via constructor
	 */
	public function test_if_dependencies_are_provided_via_constructor() {
		$this->assertNotNull( $this->edit_mappings_page_instance->transform_function_registry, '
		Registry service must be provided via constructor' );
	}

}