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

	function wl_admin_settings( $settings ) {
		$settings['wl_no_editor_analysis'] = post_type_supports( get_post_type( get_the_ID() ), 'editor' );
		return $settings;
	}


}