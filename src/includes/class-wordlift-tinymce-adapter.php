<?php
/**
 * Adapters: TinyMCE Editor Adapter.
 *
 * @since   3.12.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Tinymce_Adapter} class.
 *
 * @since   3.12.0
 * @package Wordlift
 */
class Wordlift_Tinymce_Adapter {

	/**
	 * The {@link Wordlift} plugin instance.
	 *
	 * @since  3.12.0
	 * @access private
	 * @var \Wordlift $plugin The {@link Wordlift} plugin instance.
	 */
	private $plugin;

	/**
	 * Wordlift_Tinymce_Adapter constructor.
	 *
	 * @param \Wordlift $plugin The {@link Wordlift} plugin instance.
	 */
	public function __construct( $plugin ) {

		$this->plugin = $plugin;

	}

	/**
	 * Load the TinyMCE plugin. This method is called by the WP mce_external_plugins hook.
	 *
	 * @param array $plugins The existing plugins array.
	 *
	 * @return array The modified plugins array.
	 * @since 3.12.0
	 */
	public function mce_external_plugins( $plugins ) {

		/**
		 * Bail out if you are on Media Library
		 *
		 * @since 3.27.1
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/1122
		 */
		if ( isset( get_current_screen()->base ) && get_current_screen()->base === 'upload' ) {
			return $plugins;
		}

		/*
		 * Call the `wl_can_see_classification_box` filter to determine whether we can display the classification box.
		 *
		 * @since 3.20.3
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/914
		 */
		if ( ! apply_filters( 'wl_can_see_classification_box', true ) ) {
			return $plugins;
		}

		// Get WordLift's version as a cache killer.
		$version = $this->plugin->get_version();

		// User can edit?
		$can_edit = current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' );

		// If user can't edit or rich editing isn't enabled, bail out.
		if ( ! $can_edit || ! get_user_option( 'rich_editing' ) ) {
			return $plugins;
		}

		// Add our own JavaScript file to TinyMCE's extensions.
		// DO NOT use the minified version, it'll yield errors with AngularJS.
		$plugins['wordlift']      = plugin_dir_url( __DIR__ ) . 'js/wordlift-reloaded.js?ver=' . $version;
		$plugins['wl_shortcodes'] = plugin_dir_url( __DIR__ ) . 'admin/js/wordlift_shortcode_tinymce_plugin.js?ver=' . $version;
		$plugins['wl_tinymce']    = plugin_dir_url( __DIR__ ) . 'admin/js/1/tinymce.js?ver=' . $version;
		$plugins['wl_tinymce_2']  = plugin_dir_url( __DIR__ ) . 'js/dist/tiny-mce.js?ver=' . $version;

		return $plugins;
	}

}
