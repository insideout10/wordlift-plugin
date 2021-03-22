<?php

use Wordlift\Vocabulary\Vocabulary_Loader;

abstract class Wordlift_Vocabulary_Unit_Test_Case  extends Wordlift_Unit_Test_Case {

	public function setUp() {
		parent::setUp();
		if ( ! taxonomy_exists('post_tag') ) {
			register_taxonomy('post_tag', 'post');
		}
		global $wp_filter;
		$wp_filter = array();
		$loader = new Vocabulary_Loader();
		$loader->init_vocabulary();
	}
}