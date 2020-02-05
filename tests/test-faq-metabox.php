<?php

use Wordlift\FAQ\FAQ_Metabox;

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

	public function test_when_class_is_initialized_faq_metabox_registered() {
		new FAQ_Metabox();
		//do_action('admin_init');
		// check if the meta box is present for FAQ
		global $wp_meta_boxes;
		// the metaboxes should have the FAQ meta box.
		$this->assertArrayHasKey(FAQ_Metabox::FAQ_METABOX_ID, $wp_meta_boxes['post']['advanced']['default'] );
	}

}