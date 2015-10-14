<?php

/*
 Plugin Name: WordLift
 Plugin URI: http://www.linkedin.com/company/insideout10/wordlift-327348/product
 Description: WordLift is a WordPress Plug-In to enrich any user-created text (a blog post, article or web page) with HTML microdata to improve content findability. WordLift "reads" the text, "understands" all contextual content and enriches the original text by adding the most relevant information from the semantic web. All the information retrieved can be manually edited by the author and it is used to mark-up the page in a way all major search providers (Google, Bing and Yahoo!) recognized. Through a simple Plug-In all your contents will be instantly compliant with schema.org specifications for a better SEO.
 Version: 1.0
 Author: InsideOut10
 Author URI: http://www.linkedin.com/company/insideout10
 License: APL
 */

/*  Copyright 2011 [author], [company], [company_url]

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?><?php

// some definition we will use
define('WLP_PUGIN_NAME', 'WordLift Plugin');
define('WLP_PLUGIN_DIRECTORY', 'wordlift');
define('WLP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WLP_CURRENT_VERSION', '0.1');
define('WLP_CURRENT_BUILD', '1');
define('WLP_LOGPATH', str_replace('\\', '/', WP_CONTENT_DIR) . '/logs/wlp/');
define('WLP_DEBUG', false);  # never use debug mode on productive systems
// i18n plugin domain for language files
define('EMU2_I18N_DOMAIN', 'wlp');

// how to handle log files, don't load them if you don't log
require_once('wlp_logfilehandling.php');

// contains the init action
require_once( 'wlp_init.php' );

// load language files
function wlp_set_lang_file() {
	# set the language file
	$currentLocale = get_locale();
	if (!empty($currentLocale)) {
		$moFile = dirname(__FILE__) . "/lang/" . $currentLocale . ".mo";
		if (@file_exists($moFile) && is_readable($moFile)) {
			load_textdomain(EMU2_I18N_DOMAIN, $moFile);
		}
	}
}

wlp_set_lang_file();

// create custom plugin settings menu
add_action('admin_menu', 'wlp_create_menu');

register_activation_hook(__FILE__, 'wlp_activate');
register_deactivation_hook(__FILE__, 'wlp_deactivate');
register_uninstall_hook(__FILE__, 'wlp_uninstall');

// activating the default values
function wlp_activate() {
	add_option('wlp_stanbol_engines_url', 'http://stanbol.insideout.io/engines/');
}

// deactivating
function wlp_deactivate() {
	// needed for proper deletion of every option
	delete_option('wlp_stanbol_engines_url');
}

// uninstalling
function wlp_uninstall() {
	# delete all data stored
	delete_option('wlp_stanbol_engines_url');
	// delete log files and folder only if needed
	if (function_exists('wlp_deleteLogFolder'))
	wlp_deleteLogFolder();
}

function wlp_create_menu() {

	/*
	// create new top-level menu
	add_menu_page(
	__('Plugin Name', EMU2_I18N_DOMAIN), __('Plugin Name', EMU2_I18N_DOMAIN), 0, WLP_PLUGIN_DIRECTORY . '/wlp_settings_page.php', '', plugins_url('/images/icon.png', __FILE__));


	add_submenu_page(
	WLP_PLUGIN_DIRECTORY . '/wlp_settings_page.php', __("Plugin Name", EMU2_I18N_DOMAIN), __("Settings", EMU2_I18N_DOMAIN), 0, WLP_PLUGIN_DIRECTORY . '/wlp_settings_page.php'
	);
	*/
}

function my_plugin_admin_styles() {
	/*
	 * It will be called only on your plugin admin page, enqueue our script here
	 */
	wp_enqueue_script('myPluginScript');
}

function wlp_register_settings() {
	//register settings
	register_setting('wlp-settings-group', 'wlp_stanbol_engines_url');

	// we require the following style-sheets:
	//  1. jquery-ui
	wp_register_style( 'ui-lightness', plugins_url('/css/ui-lightness/jquery-ui-1.8.12.custom.css',__FILE__), null, false, false );
	wp_enqueue_style( 'ui-lightness' );

	wp_register_style( 'ioio-ikswp', plugins_url('/styles/ioio.ikswp.css',__FILE__), null, false, false );
	wp_enqueue_style( 'ioio-ikswp' );

	// we require the following scripts:
	//  1. jquery: it is provided by WordPress
	//  2. jquery-rdf-rules
	//  3. underscore
	//  4. backbone
	//  5. io.enhancement_context
	//  6. io.rdf_vocabulary_content

	wp_register_script('ba-debug','http://github.com/cowboy/javascript-debug/raw/v0.4/ba-debug.min.js');
	wp_register_script('jquery-json', plugins_url('/scripts/jquery.json-2.2.js', __FILE__), array('jquery'), false, true);
	wp_register_script('jquery-rdf-rules', plugins_url('/scripts/jquery.rdfquery.rules-1.0.js', __FILE__), array('jquery','jquery-json'), false, true);
	wp_register_script('ioio-ikswp', plugins_url('/scripts/ioio.ikswp-0.0.2.js', __FILE__), array('ba-debug','jquery-rdf-rules','jquery-ui-core','jquery-ui-selectable'), false, true);


	// this script will load all the dependencies
	wp_enqueue_script('ioio-ikswp');

	wp_register_script('connector', plugins_url('/scripts/ioio.ikswp.connector-0.0.1.js', __FILE__), array('jquery','jquery-ui-core','jquery-ui-dialog','ioio-ikswp'), false, true);
	wp_enqueue_script('connector');
}

function ikswp_print_styles() {
	wp_register_style( 'ioio-ikswp', plugins_url('/styles/ioio.ikswp-public.css',__FILE__), null, false, false );
	wp_enqueue_style( 'ioio-ikswp' );
}

//call register settings function
add_action('admin_init', 'wlp_register_settings');
add_action('wp_print_styles', 'ikswp_print_styles');

// check if debug is activated
function wlp_debug() {
	# only run debug on localhost
	if ($_SERVER["HTTP_HOST"] == "localhost" && defined('WLP_DEBUG') && WLP_DEBUG == true)
	return true;
}

?>
