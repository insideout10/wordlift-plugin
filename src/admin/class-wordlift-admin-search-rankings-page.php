<?php
/**
 * Pages: Search Rankings admin page.
 *
 * Handles the WordLift Search Rankings admin page.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Search_Rankings} class.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
class Wordlift_Search_Rankings_Page extends Wordlift_Admin_Page {

	/**
	 * @inheritdoc
	 */
	function get_capability() {

		return 'manage_options';
	}

	/**
	 * @inheritdoc
	 */
	function get_page_title() {

		return __( 'Search Rankings', 'wordlift' );
	}

	/**
	 * @inheritdoc
	 */
	function get_menu_title() {

		return __( 'Search Rankings', 'wordlift' );
	}

	/**
	 * @inheritdoc
	 */
	function get_menu_slug() {

		return 'wl_search_rankings_menu';
	}

	/**
	 * @inheritdoc
	 */
	function get_partial_name() {

		return 'wordlift-admin-search-rankings-page.php';
	}

	/**
	 * @inheritdoc
	 */
	public function enqueue_scripts() {

		// JavaScript required for the keywords page.
		wp_enqueue_script( 'wordlift-admin-keywords-page', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/1/keywords.js', array( 'wp-util' ), false, true );
		wp_enqueue_style( 'wordlift-admin-keywords-page', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/1/keywords.css' );

	}

}
