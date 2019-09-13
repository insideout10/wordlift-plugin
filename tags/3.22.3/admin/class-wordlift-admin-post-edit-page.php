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
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since 3.15.4
	 *
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * Create the {@link Wordlift_Admin_Post_Edit_Page} instance.
	 *
	 * @param \Wordlift $plugin The {@link Wordlift} plugin instance.
	 *
	 * @since 3.11.0
	 *
	 */
	function __construct( $plugin ) {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		// Bail out if we're in the UX Builder editor.
		if ( $this->is_ux_builder_editor() ) {
			$this->log->info( 'WordLift will not show, since we are in UX Builder editor.' );

			return;
		}

		// Define the callbacks.
		$callback                  = array( $this, 'enqueue_scripts', );
		$callback_gutenberg        = array( $this, 'enqueue_scripts_gutenberg', );
		$callback_block_categories = array( $this, 'block_categories' );

		// Set a hook to enqueue scripts only when the edit page is displayed.
		add_action( 'admin_print_scripts-post.php', $callback );
		add_action( 'admin_print_scripts-post-new.php', $callback );

		add_action( 'enqueue_block_editor_assets', $callback_gutenberg );

		$this->plugin = $plugin;
	}

	/**
	 * Check whether the current post opens with G'berg or not.
	 *
	 * @return bool True if G'berg is used otherwise false.
	 * @since 3.22.3
	 */
	function is_gutenberg_page() {
		if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
			// The Gutenberg plugin is on.
			return true;
		}

		$current_screen = get_current_screen();
		if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			// Gutenberg page on 5+.
			return true;
		}

		return false;
	}

	/**
	 * Check if we're in UX builder.
	 *
	 * @see   https://github.com/insideout10/wordlift-plugin/issues/691
	 *
	 * @since 3.15.4
	 *
	 * @return bool True if we're in UX builder, otherwise false.
	 */
	private function is_ux_builder_editor() {

		return function_exists( 'ux_builder_is_editor' )
		       && ux_builder_is_editor();
	}

	/**
	 * Enqueue scripts and styles for the edit page.
	 *
	 * @since 3.11.0
	 */
	public function enqueue_scripts() {

		// Bail out if this is G'berg.
		if ( $this->is_gutenberg_page() ) {
			return;
		}

		// Dequeue potentially conflicting ontrapages angular scripts which any *are not* used on the edit screen.
		//
		// @see https://github.com/insideout10/wordlift-plugin/issues/832
		wp_dequeue_script( 'ontrapagesAngular' );
		wp_dequeue_script( 'ontrapagesApp' );
		wp_dequeue_script( 'ontrapagesController' );

		/*
		 * Enqueue the edit screen JavaScript. The `wordlift-admin.bundle.js` file
		 * is scheduled to replace the older `wordlift-admin.min.js` once client-side
		 * code is properly refactored.
		 *
		 * @link https://github.com/insideout10/wordlift-plugin/issues/761
		 *
		 * @since 3.20.0 edit.js has been migrated to the new webpack configuration.
		 */
		// plugin_dir_url( __FILE__ ) . 'js/1/edit.js'
		$script_name = plugin_dir_url( dirname( __FILE__ ) ) . 'js/dist/edit';
		wp_enqueue_script(
			'wordlift-admin-edit-page', "$script_name.js",
			array(
				$this->plugin->get_plugin_name(),
				'jquery',
				// Require wp.ajax.
				'wp-util',
				/*
				 * Angular isn't loaded anymore remotely, but it is loaded within wordlift-reloaded.js.
				 *
				 * See https://github.com/insideout10/wordlift-plugin/issues/865.
				 *
				 * @since 3.19.6
				 */
				//				// Require Angular.
				//				'wl-angular',
				//				'wl-angular-geolocation',
				//				'wl-angular-touch',
				//				'wl-angular-animate',
			),
			$this->plugin->get_version(),
			false
		);
		wp_enqueue_style( 'wordlift-admin-edit-page', "$script_name.css", array(), $this->plugin->get_version() );

	}

	/**
	 * Enqueue scripts and styles for the gutenberg edit page.
	 *
	 * @since 3.21.0
	 */
	public function enqueue_scripts_gutenberg() {
		wp_enqueue_script(
			'wordlift-admin-edit-gutenberg',
			plugin_dir_url( dirname( __FILE__ ) ) . 'js/dist/gutenberg.js',
			array(
				$this->plugin->get_plugin_name(),
				'jquery',
				'wp-blocks',
				'wp-util',
				'wp-element',
				'wp-components',
				'wp-compose',
				'wp-edit-post',
				'wp-plugins',
				'wp-data',
				'wp-annotations',
			),
			$this->plugin->get_version(),
			false
		);
		wp_enqueue_style(
			'style-gutenberg',
			plugin_dir_url( dirname( __FILE__ ) ) . 'js/dist/gutenberg.css',
			array(),
			$this->plugin->get_version()
		);
	}

}
