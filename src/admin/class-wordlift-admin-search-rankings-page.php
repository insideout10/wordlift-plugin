<?php
/**
 * Pages: Search Rankings.
 *
 * Display the Search Rankings page.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Admin_Search_Rankings_Page} class.
 *
 * @since 3.20.0
 */
class Wordlift_Admin_Search_Rankings_Page extends Wordlift_Admin_Page {

	/**
	 * We decide whether to load this page from {@link Wordlift} if the user subscription is editorial or business.
	 *
	 * @since 3.20.0
	 */
	public function __construct() {
		// Do nothing. We need this because the parent class calls the `admin_menu` hook.
	}

	/**
	 * @inheritdoc
	 */
	function get_page_title() {

		return _x( 'Search Rankings', 'Search Rankings', 'wordlift' );
	}

	/**
	 * @inheritdoc
	 */
	function get_menu_title() {

		return _x( 'Search Rankings', 'Search Rankings', 'wordlift' );
	}

	/**
	 * @inheritdoc
	 */
	function get_menu_slug() {

		return 'wl_search_rankings';
	}

	/**
	 * @inheritdoc
	 */
	function get_partial_name() {

		return 'wordlift-admin-search-rankings.php';
	}

	/**
	 * @inheritdoc
	 */
	public function enqueue_scripts() {
		parent::enqueue_scripts();

		wp_enqueue_script( 'wl-admin-search-rankings', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/1/search-rankings.js', array( 'wp-util' ), Wordlift::get_instance()->get_version() );
		wp_enqueue_style( 'wl-admin-search-rankings', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/1/search-rankings.css', array(), Wordlift::get_instance()->get_version() );

	}

}
