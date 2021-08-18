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


