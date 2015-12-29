<?php

function mce_add_io_semantic_lift_plugin($plugin_array) {
	$plugin_array["semanticLift"] = plugins_url('/scripts/ioio.ikswp.tinymce.plugin-0.0.1.js',__FILE__);
	return $plugin_array;
}

function mce_register_io_semantic_lift_button($buttons) {
	array_push ( $buttons, "separator", "io_semantic_lift_button" );
	return $buttons;
}

//! This method is hooked to the W/P init action
function wlp_addbuttons() {
	
	// Don't bother doing this stuff if the current user lacks permissions
	if (! current_user_can ( 'edit_posts' ) && ! current_user_can ( 'edit_pages' ))
		return;
	
	// register the external plug-in 
	add_filter ( "mce_external_plugins", "mce_add_io_semantic_lift_plugin" );
	// and register the button
	add_filter ( "mce_buttons", "mce_register_io_semantic_lift_button" );
	
	/*
	// Add only in Rich Editor mode
	if (get_user_option ( 'rich_editing' ) == 'true') {
		add_filter ( "mce_external_plugins", "add_myplugin_tinymce_plugin" );
		add_filter ( 'mce_buttons', 'register_myplugin_button' );
	}
	*/
}

// init process for button control
add_action ( 'init', 'wlp_addbuttons' );
?>