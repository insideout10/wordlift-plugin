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


	public static function remove_all_whitespaces( $string ) {
		$string = str_replace(" ", "", $string);
		$string = str_replace("\n", "", $string);
		$string = str_replace("\t", "", $string);
		$string = str_replace("\r", "", $string);
		return $string;
	}
}