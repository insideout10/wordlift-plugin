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

/**
 * Get the URL of the specified physical file.
 * @param string $file The path to the file from the plugin root folder.
 * @return string The URL to the file.
 */
function wordlift_get_url($file)
{

    // if WordLift is set into development mode, then provide a static URL, as development is done with symbolic link.
    if (defined('WORDLIFT_DEVELOPMENT')) {
        return '/wp-content/plugins/wordlift' . $file;
    }

    // use standard WP methods in production mode.
    return plugins_url($file, __FILE__);
}

/**
 * Load stylesheets for the administrative interface.
 */
function wordlift_load_admin_css()
{
    wp_register_style('wordlift_wp_admin_css', wordlift_get_url('/css/wordlift-admin.min.css'), false, '1.0.0');
    wp_enqueue_style('wordlift_wp_admin_css');
}

add_action('admin_enqueue_scripts', 'wordlift_load_admin_css');

/**
 * Add buttons hook for the TinyMCE editor. This method is called by the WP init hook.
 */
function wordlift_buttonhooks()
{

    // Only add hooks when the current user has permissions AND is in Rich Text editor mode
    if ((current_user_can('edit_posts') || current_user_can('edit_pages')) && get_user_option('rich_editing')) {
        add_filter('mce_external_plugins', 'wordlift_register_tinymce_javascript');
        add_filter('mce_buttons',          'wordlift_register_buttons');
    }
}

/**
 * Register the TinyMCE buttons. This method is called by the WP mce_buttons hook.
 * @param array $buttons The existing buttons array.
 * @return array The modified buttons array.
 */
function wordlift_register_buttons($buttons)
{
    // push the wordlift button the array.
    array_push($buttons, 'wordlift');
    return $buttons;
}

/**
 * Load the TinyMCE plugin. This method is called by the WP mce_external_plugins hook.
 * @param array $plugin_array The existing plugins array.
 * @return array The modified plugins array.
 */
function wordlift_register_tinymce_javascript($plugin_array)
{
    // add the wordlift plugin.
    $plugin_array['wordlift'] = wordlift_get_url('/js/wordlift-tinymce-plugin.min.js');
    return $plugin_array;
}

// init process for button control
add_action('init', 'wordlift_buttonhooks');


// Ajax Admin Section

add_action('wp_ajax_wordlift_analyze', 'wordlift_ajax_analyze_action');

// Analyze a text
function wordlift_ajax_analyze_action()
{
    if ((current_user_can('edit_posts') || current_user_can('edit_pages')) && get_user_option('rich_editing')) {

        global $wpdb; // this is how you get access to the database
        
            $api_key = '5VnRvvkRyWCN5IWUPhrH7ahXfGCBV8N0197dbccf';
            $api_analysis_chain = 'wordlift';
            $api_url = "https://api.redlink.io/1.0-ALPHA/analysis/$api_analysis_chain/enhance?key=$api_key";

            $response = wp_remote_post($api_url, array(
                    'method' => 'POST',
                    'timeout' => 45,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => array(
                    	'Accept' => 'application/json',
                    	'Content-type' => 'text/plain',
                    	),
                    'body' => file_get_contents("php://input"),
                    'cookies' => array()
                )
            );

        if ( is_wp_error( $response ) ) {
           $error_message = $response->get_error_message();
           echo "Something went wrong: $error_message";
           die();
        } else {
           echo $response['body'];
           die();
        }
        



    }
}

/**
 * Register additional scripts for the admin UI.
 */
function wordlift_admin_enqueue_scripts() {
    wp_enqueue_script( 'angularjs', wordlift_get_url('/bower_components/angular/angular.min.js') );
}
add_action('admin_enqueue_scripts', 'wordlift_admin_enqueue_scripts');

// add editor related methods.
require_once('wordlift_editor.php');
// add configuratiokn-related methods.
require_once('wordlift_configuration.php');
// add the WordLift admin bar.
require_once('wordlift_admin_bar.php');

// load languages.
// TODO: the following call gives for granted that the plugin is in the wordlift directory.
load_plugin_textdomain('wordlift', false, '/wordlift/languages' );