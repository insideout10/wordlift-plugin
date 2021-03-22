<?php

use Wordlift\Vocabulary\Hooks\Term_Page_Hook;

/**
 * @since 3.30.0
 * @group vocabulary
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Vocabulary_Term_page extends \Wordlift_Vocabulary_Unit_Test_Case {


	public function test_should_load_entity_match_on_term_page_screen() {
		global $wp_filter;
		$this->assertArrayHaskey( 'edit_post_tag_form_fields', $wp_filter );
	}

	public function test_should_load_correct_script_and_css() {
		global $wp_scripts, $wp_styles;

		do_action( 'edit_post_tag_form_fields' );
		$this->assertArrayHaskey( Term_Page_Hook::HANDLE, $wp_scripts->registered );
		$this->assertArrayHaskey( Term_Page_Hook::HANDLE, $wp_styles->registered );
	}


	public function test_should_verify_location_of_loaded_scripts_and_styles() {
		global $wp_scripts, $wp_styles;
		do_action( 'edit_post_tag_form_fields' );
		$script_source = $wp_scripts->registered[ Term_Page_Hook::HANDLE ]->src;
		$this->assertTrue( strpos( $script_source, "wp-content/plugins/app/src/js/dist/vocabulary-term-page", 0 ) !== false );
		$style_source = $wp_styles->registered[ Term_Page_Hook::HANDLE ]->src;
		$this->assertTrue( strpos( $style_source, "wp-content/plugins/app/src/js/dist/vocabulary-term-page.full.css", 0 ) !== false );
	}


	public function test_should_pass_the_matched_entities_in_localized_script() {
		global $wp_scripts, $wp_styles;
		$localized_data = $wp_scripts->get_data( Term_Page_Hook::HANDLE, Term_Page_Hook::LOCALIZED_KEY );
		$this->assertNotFalse( $localized_data );
	}

}