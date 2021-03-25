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
	}


}