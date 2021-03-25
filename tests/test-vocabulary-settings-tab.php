<?php
/**
 * @since 3.30.0
 * @group vocabulary
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Vocabulary_Settings_Tab_Test extends \Wordlift_Vocabulary_Unit_Test_Case {


	public function test_should_have_admin_tab_registered_for_match_terms() {
		$tabs = apply_filters('wl_admin_page_tabs', array());
		$this->assertCount(1, $tabs);
		$match_terms_tab = $tabs[0];
		$this->assertArrayHasKey('slug', $match_terms_tab);
		$this->assertArrayHasKey('title', $match_terms_tab);
		$this->assertEquals('match-terms', $match_terms_tab['slug']);
		$this->assertEquals('Match Terms', $match_terms_tab['title']);
	}


	public function test_should_add_match_terms_key_to_admin_settings() {
		$settings = apply_filters('wl_admin_settings', array());
		$this->assertArrayHasKey('matchTerms', $settings);
		$match_terms_settings = $settings['matchTerms'];
		$this->assertArrayHasKey('baseUrl', $match_terms_settings);
		$this->assertArrayHasKey('nonce', $match_terms_settings);
	}


}