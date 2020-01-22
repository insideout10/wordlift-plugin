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

	public function setUp() {
		parent::setUp();
		$this->edit_mappings_page_instance = new \Wordlift\Mappings\Pages\Edit_Mappings_Page();
	}

	/**
	 * Test check if the text items for settings are loaded correctly.
	 */
	public function test_correctly_loading_text_items_in_ui_array() {
		$ui_dependencies_array = \Wordlift\Mappings\Pages\Edit_Mappings_Page::get_ui_settings_array();
		$this->assertArrayHasKey( 'wl_add_mapping_text', $ui_dependencies_array );
		$this->assertArrayHasKey( 'wl_edit_mapping_text', $ui_dependencies_array );
		$this->assertArrayHasKey( 'wl_edit_mapping_no_item', $ui_dependencies_array );
	}

	/**
	 * This test should verify all the terms are loaded from the REST API, so taxonomy should have
	 * zero terms in their value.
	 */
	public function test_check_there_is_no_terms_in_taxonomy_array() {

	}

}