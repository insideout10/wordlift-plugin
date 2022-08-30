<?php
/**
 * This file provides a helper class to enqueue scripts based on WordPress version.
 *
 * This is necessary because at the time of writing we still support WordPress 4.4+ which doesn't provide React
 * libraries. In order to provide support for React in WordPress 4.4+ we configured Webpack in src/src/js to build two
 * versions of each JS: one without React and one with React (with the .full suffix).
 *
 * This helper includes the correct version according to WordPress version, i.e. full for 4.4-5.0 (excluded) and not
 * full for 5.0+.
 *
 * @since 3.25.0
 * @authod David Riccitelli <david@wordlift.io>
 * @package Wordlift
 * @subpackage Wordlift\Scripts
 */

namespace Wordlift\Scripts;

class Scripts_Helper {

	/**
	 * This function loads the javascript file according to the WordPress version.
	 *
	 * For WordPress < 5.0 it'll load the javascript file using the `.full` suffix i.e. the file that embeds all the
	 * dependencies.
	 *
	 * For WordPress >= 5.0 it'll load the stripped down js.
	 *
	 * @param string $handle The handle name.
	 * @param string $script_name The full script URL without the `.js` extension.
	 * @param array  $dependencies An array of dependencies to be added only in WordPress > 5.0.
	 */
	public static function enqueue_based_on_wordpress_version( $handle, $script_name, $dependencies, $in_footer = false ) {
		global $wp_version;

		if ( version_compare( $wp_version, '5.0', '<' ) ) {
			$actual_script_name  = "$script_name.full.js";
			$actual_dependencies = array();
		} else {
			$actual_script_name  = "$script_name.js";
			$actual_dependencies = $dependencies;
		}

		$wordlift = \Wordlift::get_instance();
		wp_enqueue_script( $handle, $actual_script_name, $actual_dependencies, $wordlift->get_version(), $in_footer );

	}
	/**
	 * This function registers the javascript file according to the WordPress version.
	 *
	 * For WordPress < 5.0 it'll register the javascript file using the `.full` suffix i.e. the file that embeds all the
	 * dependencies.
	 *
	 * For WordPress >= 5.0 it'll register the stripped down js.
	 *
	 * @param string $handle The handle name.
	 * @param string $script_name The full script URL without the `.js` extension.
	 * @param array  $dependencies An array of dependencies to be added only in WordPress > 5.0.
	 */
	public static function register_based_on_wordpress_version(
			$handle,
			$script_name,
			$dependencies,
			$action = 'wp_enqueue_scripts',
			$in_footer = false
		) {
		global $wp_version;

		if ( version_compare( $wp_version, '5.0', '<' ) ) {
			$actual_script_name  = "$script_name.full.js";
			$actual_dependencies = array();
		} else {
			$actual_script_name  = "$script_name.js";
			$actual_dependencies = $dependencies;
		}

		$wordlift = \Wordlift::get_instance();
		add_action(
			$action,
			function () use ( $handle, $actual_script_name, $actual_dependencies, $wordlift, $in_footer ) {
				wp_register_script( $handle, $actual_script_name, $actual_dependencies, $wordlift->get_version(), $in_footer );
			}
		);

	}

}
