<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wordpress.org
 * @since             1.0.0
 * @package           Wordlift
 *
 * @wordpress-plugin
 * Plugin Name:       Disable Deprecated messages
 * Description:       this plugin to disable all deprecated messages on screen
 * Version:           1.0.0
 * Author:            Ahmed ElMeligy
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// Disable deprecation notices so we can get a better idea of what's going on in our log.
// These hooks are all in wp-includes/functions.php.
// Note that these hooks don't stop WooCommerce from logging deprecation notices on AJAX 
// or REST API calls as it makes its own calls to `error_log()` from within
// woocommerce/includes/wc-deprecated-functions.php.
add_filter( 'deprecated_constructor_trigger_error', '__return_false' );
add_filter( 'deprecated_function_trigger_error', '__return_false' );
add_filter( 'deprecated_file_trigger_error', '__return_false' );
add_filter( 'deprecated_argument_trigger_error', '__return_false' );
add_filter( 'deprecated_hook_trigger_error', '__return_false' );
