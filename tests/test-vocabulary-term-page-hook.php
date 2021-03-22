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
		var_dump($wp_scripts->registered);
		$this->assertArrayHaskey( Term_Page_Hook::HANDLE, $wp_scripts->registered );
		$this->assertArrayHaskey( Term_Page_Hook::HANDLE, $wp_styles->registered );
	}


}