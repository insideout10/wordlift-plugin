<?php
/*
Plugin Name: WordLift
Plugin URI: http://wordlift.it
Description: Supercharge your WordPress Site with Smart Tagging and #Schemaorg support - a brand new way to write, organise and publish your contents to the Linked Data Cloud.
Version: 3.0.0-SNAPSHOT
Author: InSideOut10
Author URI: http://www.insideout.io
License: APL
*/


function wordlift_buttonhooks() {
   // Only add hooks when the current user has permissions AND is in Rich Text editor mode
   if ( ( current_user_can('edit_posts') || current_user_can('edit_pages') ) && get_user_option('rich_editing') ) {
     add_filter("mce_external_plugins", "wordlift_register_tinymce_javascript");
     add_filter('mce_buttons', 'wordlift_register_buttons');
   }
}
 
function wordlift_register_buttons($buttons) {
	array_push($buttons, 'wordlift');
   return $buttons;
}
 
// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
function wordlift_register_tinymce_javascript($plugin_array) {
   // $plugin_array['wordlift'] = plugins_url('/js/wordlift-tinymce-plugin.js',__file__);
   $plugin_array['wordlift'] = '/wp-content/plugins/wordlift/js/wordlift-tinymce-plugin.js';
   
   return $plugin_array;
}
 
// init process for button control
add_action('init', 'wordlift_buttonhooks');