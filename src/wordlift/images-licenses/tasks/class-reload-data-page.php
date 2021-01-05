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

namespace Wordlift\Images_Licenses\Tasks;

use Wordlift\Tasks\Admin\Tasks_Page_Base;
use Wordlift\Tasks\Task_Ajax_Adapters_Registry;

class Reload_Data_Page extends Tasks_Page_Base {

	/**
	 * The ID of this admin page.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $menu_slug The ID of this page.
	 */
	private $menu_slug = 'wl_images_licenses__reload_data';

	/**
	 * Define the {@link Wordlift_Admin_Page} constructor.
	 *
	 * @param Task_Ajax_Adapters_Registry $task_ajax_adapters_registry
	 * @param string $version
	 *
	 * @since 1.0.0
	 */
	public function __construct( $task_ajax_adapters_registry, $version ) {
		parent::__construct(
			$task_ajax_adapters_registry,
			$version,
			$this->menu_slug,
			__( 'License Compliance', 'wordlift-framework' ),
			'manage_options',
			null,
			__( 'License Compliance', 'wordlift-framework' )
		);
	}

}
