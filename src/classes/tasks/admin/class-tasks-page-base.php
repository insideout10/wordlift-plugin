<?php

/**
 * Sets up the admin page for the plaats enhance features where the updater
 * process can be triggered.
 *
 * @link       https://wordlift.io
 * @since      1.0.0
 *
 * @package    Wordlift_Framework\Tasks\Admin
 */

namespace Wordlift\Tasks\Admin;

use Wordlift\Tasks\Task_Ajax_Adapters_Registry;
use Wordlift\Wordpress\Submenu_Page_Base;

abstract class Tasks_Page_Base extends Submenu_Page_Base {

	/**
	 * The ID of this admin page.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $menu_slug The ID of this page.
	 */
	private $menu_slug;

	/**
	 * @var Task_Ajax_Adapters_Registry
	 */
	private $task_ajax_adapters_registry;

	/**
	 * @var string
	 */
	private $version;

	/**
	 * Define the {@link Wordlift_Admin_Page} constructor.
	 *
	 * @param Task_Ajax_Adapters_Registry $task_ajax_adapters_registry
	 *
	 * @param string                      $version
	 * @param string                      $menu_slug
	 * @param string                      $page_title
	 * @param string                      $capability
	 * @param string|null                 $parent_slug
	 * @param string|null                 $menu_title
	 *
	 * @since 1.0.0
	 */
	public function __construct( $task_ajax_adapters_registry, $version, $menu_slug, $page_title, $capability = 'manage_options', $parent_slug = null, $menu_title = null ) {
		parent::__construct( $menu_slug, $page_title, $capability, $parent_slug, $menu_title );

		$this->task_ajax_adapters_registry = $task_ajax_adapters_registry;
		$this->version                     = $version;
		$this->menu_slug                   = $menu_slug;

	}

	/**
	 * Register the stylesheets and scripts for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( $this->menu_slug, plugin_dir_url( __FILE__ ) . 'assets/tasks-page.css', array(), $this->version, 'all' );
		wp_enqueue_script(
			$this->menu_slug,
			plugin_dir_url( __FILE__ ) . 'assets/tasks-page.js',
			array(
				'jquery',
				'wp-util',
			),
			$this->version,
			true
		);
	}

	/**
	 * Render the page.
	 *
	 * @since 1.0.0
	 */
	public function render() {

		// Include the partial.
		include plugin_dir_path( __FILE__ ) . 'assets/tasks-page.php';

	}

}
