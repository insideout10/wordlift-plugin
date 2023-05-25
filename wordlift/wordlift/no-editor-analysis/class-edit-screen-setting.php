<?php

namespace Wordlift\No_Editor_Analysis;

/**
 *  This file adds the settings needed by js on post edit screen.
 *
 * @since 3.32.6
 * @package  Wordlift\No_Editor_Analysis
 */
class Edit_Screen_Setting {

	public function add_setting() {
		add_filter( 'wl_admin_settings', array( $this, 'wl_admin_settings' ) );
	}

	public function wl_admin_settings( $settings ) {
		$settings['analysis']['isNoEditorAnalysisActive'] = No_Editor_Analysis_Feature::can_no_editor_analysis_be_used( get_the_ID() );
		return $settings;
	}

}
