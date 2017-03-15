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

	/**
	 * Create the {@link Wordlift_Admin_Post_Edit_Page} instance.
	 *
	 * @since 3.11.0
	 *
	 * @param \Wordlift $plugin The {@link Wordlift} plugin instance.
	 */
	function __construct( $plugin ) {

		// Define the callback.
		$callback = array( $this, 'enqueue_scripts', );

		// Set a hook to enqueue scripts only when the edit page is displayed.
		add_action( 'admin_print_scripts-post.php', $callback );
		add_action( 'admin_print_scripts-post-new.php', $callback );

		$this->plugin = $plugin;
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
			array( $this->plugin->get_plugin_name(), ),
			$this->plugin->get_version(),
			false
		);

	}

}