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

class Tasks_Page extends Submenu_Page_Base {

	/**
	 * The ID of this admin page.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $menu_slug The ID of this page.
	 */
	private $menu_slug = 'wl_tasks_page';

	/**
	 * Used when enqueueing styles or scripts as the version string.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string
	 */
	private $asset_version = '1.0.0';

	/**
	 * @var Task_Ajax_Adapters_Registry
	 */
	private $task_ajax_adapters_registry;

	/**
	 * Define the {@link Wordlift_Admin_Page} constructor.
	 *
	 * @param Task_Ajax_Adapters_Registry $task_ajax_adapters_registry
	 *
	 * @since 1.0.0
	 */
	public function __construct( $task_ajax_adapters_registry ) {
		parent::__construct( $this->menu_slug, __( 'Tasks', 'wordlift' ), 'manage_options', 'wl_admin_menu', __( 'Tasks', 'wordlift' ) );

		$this->task_ajax_adapters_registry = $task_ajax_adapters_registry;

	}

	/**
	 * Register the stylesheets and scripts for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( $this->menu_slug, plugin_dir_url( __FILE__ ) . 'assets/tasks-page.css', array(), $this->asset_version, 'all' );
		wp_enqueue_script(
			$this->menu_slug,
			plugin_dir_url( __FILE__ ) . 'assets/tasks-page.js',
			array(
				'jquery',
				'wp-util',
			),
			$this->asset_version,
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
