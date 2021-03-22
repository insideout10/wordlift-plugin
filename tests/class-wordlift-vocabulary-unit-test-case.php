<?php

use Wordlift\Vocabulary\Vocabulary_Loader;

abstract class Wordlift_Vocabulary_Unit_Test_Case  extends Wordlift_Unit_Test_Case {

	public function setUp() {
		parent::setUp();
		if ( ! taxonomy_exists('post_tag') ) {
			register_taxonomy('post_tag', 'post');
		}
		// Reset all global filters.
		global $wp_filter, $wp_scripts;
		$wp_filter = array();
		$wp_scripts = null;

		$loader = new Vocabulary_Loader();
		$loader->init_vocabulary();
	}
}