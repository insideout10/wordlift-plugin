<?php

namespace Wordlift\Wordpress;

abstract class Submenu_Page_Base implements Page {
	/**
	 * @var string
	 */
	private $menu_slug;
	/**
	 * @var string
	 */
	private $page_title;
	/**
	 * @var string
	 */
	private $capability;
	/**
	 * @var string|null
	 */
	private $parent_slug;
	/**
	 * @var string|null
	 */
	private $menu_title;

	/**
	 * Abstract_Submenu_Page constructor.
	 *
	 * @param string      $menu_slug
	 * @param string      $page_title
	 * @param string      $capability
	 * @param string|null $parent_slug
	 * @param string|null $menu_title
	 */
	public function __construct( $menu_slug, $page_title, $capability = 'manage_options', $parent_slug = null, $menu_title = null ) {

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		$this->menu_slug   = $menu_slug;
		$this->page_title  = $page_title;
		$this->capability  = $capability;
		$this->parent_slug = $parent_slug;
		$this->menu_title  = isset( $menu_title ) ? $menu_title : $page_title;
	}

	public function get_menu_slug() {

		return $this->menu_slug;
	}

	/**
	 * The `admin_menu` callback. Will call {@link add_submenu_page} to add the
	 * page to the admin menu.
	 *
	 * @return false|string The resulting page's hook_suffix, or false if the user does not have the capability required.
	 * @since 1.0.0
	 */
	public function admin_menu() {

		// Add the sub-menu page.
		//
		// See http://codex.wordpress.org/Function_Reference/add_submenu_page
		$page = add_submenu_page(
			$this->parent_slug,
			$this->page_title,
			$this->menu_title,
			$this->capability,
			$this->menu_slug,
			array( $this, 'render' )
		);

		// Set a hook to enqueue scripts only when the settings page is displayed.
		add_action( 'admin_print_scripts-' . $page, array( $this, 'enqueue_scripts' ) );

		// Finally return the page hook_suffix.
		return $page;
	}

	abstract public function enqueue_scripts();

	abstract public function render();

}
