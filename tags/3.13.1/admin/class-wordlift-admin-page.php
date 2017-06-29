<?php
/**
 * Pages: Abstract Admin Page.
 *
 * An abstract class meant to be extended by admin pages.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Admin_Page} class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
abstract class Wordlift_Admin_Page {

	/**
	 * Get the parent slug.
	 *
	 * @since 3.11.0
	 *
	 * @return string The parent slug (default 'wl_admin_menu').
	 */
	protected function get_parent_slug() {

		return 'wl_admin_menu';
	}

	/**
	 * Get the required capability.
	 *
	 * @since 3.11.0
	 *
	 * @return string The capability (default 'manage_options').
	 */
	protected function get_capability() {

		return 'manage_options';
	}

	/**
	 * Get the page title. Will be translated.
	 *
	 * @since 3.11.0
	 *
	 * @return string The page title.
	 */
	abstract function get_page_title();

	/**
	 * Get the menu title. Will be translated.
	 *
	 * @since 3.11.0
	 *
	 * @return string The menu title.
	 */
	abstract function get_menu_title();

	/**
	 * Get the menu slug.
	 *
	 * @since 3.11.0
	 *
	 * @return string The menu slug.
	 */
	abstract function get_menu_slug();

	/**
	 * Get the partial file name, used in the {@link render} function.
	 *
	 * @since 3.11.0
	 *
	 * @return string The partial file name.
	 */
	abstract function get_partial_name();

	/**
	 * The `admin_menu` callback. Will call {@link add_submenu_page} to add the
	 * page to the admin menu.
	 *
	 * @since 3.11.0
	 *
	 * @return false|string The resulting page's hook_suffix, or false if the user does not have the capability required.
	 */
	public function admin_menu() {

		// Add the sub-menu page.
		//
		// See http://codex.wordpress.org/Function_Reference/add_submenu_page
		$page = add_submenu_page(
			$this->get_parent_slug(),
			$this->get_page_title(),
			$this->get_menu_title(),
			$this->get_capability(),                   // The required capability, provided by the calling hook.
			$this->get_menu_slug(),
			array( $this, 'render' )
		);

		// Set a hook to enqueue scripts only when the settings page is displayed.
		add_action( 'admin_print_scripts-' . $page, array(
			$this,
			'enqueue_scripts',
		) );

		// Finally return the page hook_suffix.
		return $page;
	}

	/**
	 * Enqueue scripts for the specific page. Subclasses can override this function
	 * to provide their own styles/scripts.
	 *
	 * @since 3.11.0
	 */
	public function enqueue_scripts() {
	}

	/**
	 * Render the page.
	 */
	public function render() {

		// Include the partial.
		include( plugin_dir_path( __FILE__ ) . 'partials/' . $this->get_partial_name() );

	}

}
