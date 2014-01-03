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

function wordlift_load_admin_css() {
		// TODO Parametrizzare la directory corrente del plugin
        wp_register_style( 'wordlift_wp_admin_css', '/wp-content/plugins/wordlift/css/wordlift-admin.css', false, '1.0.0' );
        wp_enqueue_style( 'wordlift_wp_admin_css' );
}

add_action( 'admin_enqueue_scripts', 'wordlift_load_admin_css' );

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



// Ajax Admin Section

add_action( 'wp_ajax_wordlift_analyze', 'wordlift_ajax_analyze_action' );

// Analyze a text
function wordlift_ajax_analyze_action() {
	if ( ( current_user_can('edit_posts') || current_user_can('edit_pages') ) && get_user_option('rich_editing') ) {
   
	global $wpdb; // this is how you get access to the database
/*
    $api_key = 'XXXXX';
    $api_analysis_chain = 'YYYY';
    $api_url = "https://api.redlink.io/1.0-ALPHA/analysis/$api_analysis_chain/enhance?key=$api_key"

	$response = wp_remote_post( $url, array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'body' => array( 'username' => 'bob', 'password' => '1234xyz' ),
			'cookies' => array()
    	)
	);

if ( is_wp_error( $response ) ) {
   $error_message = $response->get_error_message();
   echo "Something went wrong: $error_message";
} else {
   echo 'Response:<pre>';
   print_r( $response );
   echo '</pre>';
}
*/
    echo 'foo';
	die(); // this is required to return a proper result
	
	
}
}