<?php

class Wordlift_Admin_Search_Rankings_Page extends Wordlift_Admin_Page {

	/**
	 * @inheritdoc
	 */
	function get_page_title() {

		return _x( 'Search Rankings', 'Page title', 'wordlift' );
	}

	/**
	 * @inheritdoc
	 */
	function get_menu_title() {

		return _x( 'Search Rankings', 'Menu title', 'wordlift' );
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

		wp_enqueue_script( 'wl-admin-search-rankings', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/1/search-rankings.js', array(), Wordlift::get_instance()->get_version() );
		wp_enqueue_style( 'wl-admin-search-rankings', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/1/search-rankings.css', array(), Wordlift::get_instance()->get_version() );

	}

}