<?php
/**
 *  This file provides the loader for the no editor analysis feature.
 *
 * @since 3.32.6
 * @package  Wordlift\No_Editor_Analysis
 */

namespace Wordlift\No_Editor_Analysis;

use Wordlift\Common\Loader\Default_Loader;

class Loader extends Default_Loader {

	public function init_all_dependencies() {
		$edit_screen_setting = new Edit_Screen_Setting();
		$edit_screen_setting->add_setting();
		$meta_box = new Meta_Box();
		$meta_box->init();
		$edit_post_scripts = new Edit_Post_Scripts();
		$edit_post_scripts->init();

	}

	/**
	 * Return the feature slug.
	 *
	 * @return string
	 */
	protected function get_feature_slug() {
		return 'no-editor-analysis';
	}

	/**
	 * Return if the feature needs to be on by default.
	 *
	 * @return false
	 */
	protected function get_feature_default_value() {
		return false;
	}
}


