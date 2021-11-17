<?php

use Wordlift\Features\Features_Registry;
use Wordlift\Vocabulary_Terms\Entity_Type;
use Wordlift\Vocabulary_Terms\Term_Metabox;

/**
 * Test to make the fields appear on the custom taxonomies which are registered on the `init` hook.
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/1398
 *
 * @since 3.32.5
 * @group no-vocabulary-terms
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class No_Vocbulary_Terms_Ui_Test extends \Wordlift_Vocabulary_Terms_Unit_Test_Case {
	/**
	 * @var string
	 */
	private $custom_taxonomy_name = 'no_vocabulary_terms';

	/**
	 * Test to check if a public custom taxonomy is registered, then we need to show the fields.
	 */
	public function test_when_custom_taxonomy_is_registered_the_entity_types_box_should_be_displayed_on_the_page() {
		$this->reset_filters();
		new Entity_Type();
		$this->reset_except_init_hook();
		global $wp_filter;

		do_action( 'init' );

		$this->assertTrue(
			array_key_exists( $this->custom_taxonomy_name . '_edit_form_fields', $wp_filter ),
			'Entity types ui should render on the custom taxonomy page'
		);
		$this->assertTrue(
			array_key_exists( 'edited_' . $this->custom_taxonomy_name, $wp_filter ),
			'Entity types save hook should be added on the custom taxonomy page'
		);
	}

	public function test_when_custom_taxonomy_is_registered_custom_fields_should_be_displayed_on_the_page() {
		$this->reset_filters();
		new Term_Metabox();
		$this->reset_except_init_hook();
		global $wp_filter;
		do_action( 'init' );
		$this->assertTrue(
			array_key_exists( $this->custom_taxonomy_name . '_edit_form', $wp_filter ),
			'Custom fields ui should render on the custom taxonomy page'
		);
		$this->assertTrue(
			array_key_exists( 'edited_' . $this->custom_taxonomy_name, $wp_filter ),
			'Custom fields save hook should be added on the custom taxonomy page'
		);
	}

	private function reset_filters() {
		/**
		 * Reset all the filters and hooks to isolate and test the vocabulary terms feature.
		 */
		global $wp_filter, $wp_scripts, $wp_styles;
		$wp_filter = $wp_scripts = $wp_styles = array();

	}

	private function reset_except_init_hook() {
		global $wp_filter;
		// Reset all the hooks and test if we run them on init hook.
		$init_hooks        = $wp_filter['init'];
		$wp_filter         = array();
		$wp_filter['init'] = $init_hooks;
	}

}
