<?php

use Wordlift\Videoobject\Loader;

abstract class Wordlift_Videoobject_Unit_Test_Case  extends Wordlift_Unit_Test_Case {

	public function setUp() {
		parent::setUp();
		if ( ! taxonomy_exists( 'post_tag' ) ) {
			register_taxonomy( 'post_tag', 'post' );
		}
		// Reset all global filters.
		global $wp_filter, $wp_scripts, $wp_styles;
		$wp_filter  = array();
		$wp_scripts = null;
		$wp_styles  = null;
		$loader     = new Loader();
		$loader->init_all_dependencies();
	}
}