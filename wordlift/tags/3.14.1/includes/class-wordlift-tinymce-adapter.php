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
	 * @since 3.12.0
	 *
	 * @param array $plugins The existing plugins array.
	 *
	 * @return array The modified plugins array.
	 */
	function mce_external_plugins( $plugins ) {

		// Get WordLift's version as a cache killer.
		$version = $this->plugin->get_version();

		// User can edit?
		$can_edit = current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' );

		// If user can't edit or rich editing isn't enabled, bail out.
		if ( ! $can_edit || ! get_user_option( 'rich_editing' ) ) {
			return $plugins;
		}

		// Add our own JavaScript file to TinyMCE's extensions.
		$plugins['wordlift']      = plugin_dir_url( dirname( __FILE__ ) ) . 'js/wordlift-reloaded.js?ver=' . $version;
		$plugins['wl_shortcodes'] = plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/wordlift_shortcode_tinymce_plugin.js?ver=' . $version;
		$plugins['wl_tinymce']    = plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/wordlift-admin-tinymce.bundle.js?ver=' . $version;

		return $plugins;
	}

}
