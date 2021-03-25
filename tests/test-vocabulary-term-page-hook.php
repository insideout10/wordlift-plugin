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
		$json_data = $this->get_data_from_js_variable( $wp_scripts );
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

	public function test_set_is_active_correctly_for_legacy_data() {
		global $wp_scripts;
		$term_id = $this->create_unmatched_tag( "foo" );
		do_action( 'post_tag_edit_form_fields', get_term( $term_id ) );
		$json_data = $this->get_data_from_js_variable( $wp_scripts );
		$term_data = $json_data['termData'];
		$entities = $term_data['entities'];
		// we should have isActive set for 1 entity since only one can be selected in legacy
		// code.


	}

	/**
	 * @param WP_Scripts $wp_scripts
	 *
	 * @return mixed
	 */
	private function get_data_from_js_variable( WP_Scripts $wp_scripts ) {
		$extra_data = $wp_scripts->registered[ Term_Page_Hook::HANDLE ]->extra;
		$this->assertNotNull( $extra_data );
		$this->assertArrayHaskey( "data", $extra_data );
		$json      = $extra_data["data"];
		$json      = str_replace( "var _wlVocabularyTermPageSettings =", "", $json );
		$json      = str_replace( "};", "}", $json );
		$json_data = json_decode( $json, true );

		return $json_data;
	}

}