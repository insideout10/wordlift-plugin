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
	 * Define the {@link Wordlift_Admin_Page} constructor.
	 *
	 * @since 3.20.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 10, 0 );
	}

	/**
	 * Get the parent slug.
	 *
	 * @return string The parent slug (default 'wl_admin_menu').
	 * @since 3.11.0
	 */
	protected function get_parent_slug() {

		return 'wl_admin_menu';
	}

	/**
	 * Get the required capability.
	 *
	 * @return string The capability (default 'manage_options').
	 * @since 3.11.0
	 */
	protected function get_capability() {

		return 'manage_options';
	}

	/**
	 * Get the page title. Will be translated.
	 *
	 * @return string The page title.
	 * @since 3.11.0
	 */
	abstract public function get_page_title();

	/**
	 * Get the menu title. Will be translated.
	 *
	 * @return string The menu title.
	 * @since 3.11.0
	 */
	abstract public function get_menu_title();

	/**
	 * Get the menu slug.
	 *
	 * @return string The menu slug.
	 * @since 3.11.0
	 */
	abstract public function get_menu_slug();

	/**
	 * Get the page url.
	 *
	 * @return string The escaped url of the admin page
	 * @since 3.14.0
	 */
	public function get_url() {

		// ideally should have used menu_page_url, but it is loaded later than some usages.
		$url = admin_url( 'admin.php?page=' . $this->get_menu_slug() );

		return esc_url( $url );
	}

	/**
	 * Get the partial file name, used in the {@link render} function.
	 *
	 * @return string The partial file name.
	 * @since 3.11.0
	 */
	abstract public function get_partial_name();

	/**
	 * The `admin_menu` callback. Will call {@link add_submenu_page} to add the
	 * page to the admin menu.
	 *
	 * @return false|string The resulting page's hook_suffix, or false if the user does not have the capability required.
	 * @since 3.11.0
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
		add_action(
			'admin_print_scripts-' . $page,
			array(
				$this,
				'enqueue_scripts',
			)
		);

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
		include plugin_dir_path( __FILE__ ) . 'partials/' . $this->get_partial_name();

	}

}
