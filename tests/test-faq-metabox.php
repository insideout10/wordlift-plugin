<?php

/**
 * Tests: Tests the FAQ meta box register script.
 * @since 3.26.0
 * @package wordlift
 * @subpackage wordlift/tests
 *
 */

class FAQ_Metabox_test extends Wordlift_Unit_Test_Case {

	public function test_faq_metabox_should_be_valid_class() {
		$this->assertNotNull( new FAQ_Metabox() );
	}


}