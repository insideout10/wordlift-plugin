<?php

use Wordlift\Vocabulary\Api\Entity_Rest_Endpoint;
use Wordlift\Vocabulary\Data\Term_Data\Term_Data_Factory;
use Wordlift\Vocabulary\Hooks\Term_Page_Hook;

/**
 * @since 3.30.0
 * @group vocabulary
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Vocabulary_Term_page extends \Wordlift_Vocabulary_Unit_Test_Case {


	public function test_should_load_entity_match_on_term_page_screen() {
		global $wp_filter;
		$this->assertArrayHaskey( 'post_tag_edit_form_fields', $wp_filter );
	}

	public function test_should_load_correct_script_and_css() {
		global $wp_scripts, $wp_styles;
		$term_id = $this->create_unmatched_tag( "foo" );
		do_action( 'post_tag_edit_form_fields', get_term( $term_id ) );
		$this->assertArrayHaskey( Term_Page_Hook::HANDLE, $wp_scripts->registered );
		$this->assertArrayHaskey( Term_Page_Hook::HANDLE, $wp_styles->registered );
	}


	public function test_should_verify_location_of_loaded_scripts_and_styles() {
		global $wp_scripts, $wp_styles;
		$term_id = $this->create_unmatched_tag( "foo" );

		do_action( 'post_tag_edit_form_fields', get_term( $term_id ) );
		$script_source = $wp_scripts->registered[ Term_Page_Hook::HANDLE ]->src;
		$this->assertTrue( strpos( $script_source, "wp-content/plugins/app/src/js/dist/vocabulary-term-page", 0 ) !== false );
		$style_source = $wp_styles->registered[ Term_Page_Hook::HANDLE ]->src;
		$this->assertTrue( strpos( $style_source, "wp-content/plugins/app/src/js/dist/vocabulary-term-page.full.css", 0 ) !== false );
	}


	public function test_should_pass_the_matched_entities_in_localized_script() {
		global $wp_scripts;
		$term_id = $this->create_unmatched_tag( "foo" );
		do_action( 'post_tag_edit_form_fields', get_term( $term_id ) );
		$extra_data = $wp_scripts->registered[ Term_Page_Hook::HANDLE ]->extra;
		$this->assertNotNull( $extra_data );
		$this->assertArrayHaskey( "data", $extra_data );
		$json      = $extra_data["data"];
		$json      = str_replace( "var _wlVocabularyTermPageSettings =", "", $json );
		$json      = str_replace( "};", "}", $json );
		$json_data = json_decode( $json, true );
		$this->assertArrayHasKey( 'termData', $json_data );
		$this->assertTrue( is_array( $json_data['termData'] ) );
		$term_data = $json_data['termData'];
		$this->assertArrayHasKey( 'tagId', $term_data );
		$this->assertArrayHasKey( 'tagName', $term_data );
		$this->assertArrayHasKey( 'tagDescription', $term_data );
		$this->assertArrayHasKey( 'tagLink', $term_data );
		$this->assertArrayHasKey( 'entities', $term_data );
		$this->assertArrayHasKey( 'apiConfig', $json_data );
		$api_config = $json_data['apiConfig'];
		$this->assertArrayHasKey( 'baseUrl', $api_config );
		$this->assertArrayHasKey( 'nonce', $api_config );
	}


	public function test_should_not_render_widget_if_tag_is_already_matched() {
		global $wp_scripts, $wp_styles;
		$term    = wp_insert_term( "foo", "post_tag" );
		$term_id = $term["term_id"];
		update_term_meta( $term_id, Entity_Rest_Endpoint::IGNORE_TAG_FROM_LISTING, 1 );
		do_action( 'post_tag_edit_form_fields', get_term( $term_id ) );
		$this->assertNull( $wp_scripts );
		$this->assertNull( $wp_styles );
	}

}