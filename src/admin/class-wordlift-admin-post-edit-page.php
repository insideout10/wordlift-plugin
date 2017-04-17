<?php
/**
 * Pages: Post Edit Page.
 *
 * A 'ghost' page which loads additional scripts and style for the post edit page.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Admin_Post_Edit_Page} page.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
class Wordlift_Admin_Post_Edit_Page {

	/**
	 * The {@link Wordlift} plugin instance.
	 *
	 * @since 3.11.0
	 *
	 * @var \Wordlift $plugin The {@link Wordlift} plugin instance.
	 */
	private $plugin;

	private $shortcodes;

	/**
	 * Create the {@link Wordlift_Admin_Post_Edit_Page} instance.
	 *
	 * @since 3.11.0
	 *
	 * @param \Wordlift $plugin The {@link Wordlift} plugin instance.
	 */
	function __construct( $plugin, $shortcodes ) {

		$this->plugin     = $plugin;
		$this->shortcodes = $shortcodes;

		// Define the callback.
		$callback = array( $this, 'enqueue_scripts', );

		// Set a hook to enqueue scripts only when the edit page is displayed.
		add_action( 'admin_print_scripts-post.php', $callback );
		add_action( 'admin_print_scripts-post-new.php', $callback );

	}

	/**
	 * Enqueue scripts and styles for the edit page.
	 *
	 * @since 3.11.0
	 */
	public function enqueue_scripts() {

		// Enqueue the edit screen JavaScript. The `wordlift-admin.bundle.js` file
		// is scheduled to replace the older `wordlift-admin.min.js` once client-side
		// code is properly refactored.
		wp_enqueue_script(
			'wordlift-admin-edit-page', plugin_dir_url( __FILE__ ) . 'js/wordlift-admin-edit-page.bundle.js',
			array(
				'wordlift-admin-vendor',
				$this->plugin->get_plugin_name(),
				'mce-view',
			),
			$this->plugin->get_version(),
			false
		);

		wp_localize_script( 'wordlift-admin-edit-page', '_wlAdminEditPage', array(
			'tinymce' => array(
				'scripts' => array(
					plugin_dir_url( dirname( __FILE__ ) ) . 'public/js/wordlift-vendor.bundle.js',
					plugin_dir_url( dirname( __FILE__ ) ) . 'public/js/wordlift-navigator.bundle.js',
					plugin_dir_url( __FILE__ ) . 'js/tinymce/wordlift-tinymce-views.bundle.js',
				),
			),
		) );

		/** @var Wordlift_Shortcode $shortcode */
		foreach ( $this->shortcodes as $shortcode ) {
			$shortcode->enqueue_scripts();
		}

	}

}