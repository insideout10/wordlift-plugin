<?php
/**
 * Pages: Mappings Mockup page.
 *
 * Display the list of Mappings Mockup.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the Wordlift_Admin_Mappings_Page class.
 *
 * @since 3.20.0
 */
class Wordlift_Admin_Mappings_Mockup_Page extends Wordlift_Admin_Page {

	/**
	 * {@inheritdoc}
	 */
	function get_page_title() {

		return __( 'Sync Mappings Mockup', 'wordlift' );
	}

	/**
	 * {@inheritdoc}
	 */
	function get_menu_title() {

		return __( 'Sync Mappings Mockup', 'wordlift' );
	}

	/**
	 * {@inheritdoc}
	 */
	function get_menu_slug() {

		return 'wl_mappings_mockup';
	}

	/**
	 * {@inheritdoc}
	 */

	function get_partial_name() {

		return 'wordlift-admin-mappings-mockup.php';
	}

}
