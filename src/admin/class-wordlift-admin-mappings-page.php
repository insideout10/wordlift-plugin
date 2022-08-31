<?php
/**
 * Pages: Mappings page.
 *
 * Display the list of mappings.
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
class Wordlift_Admin_Mappings_Page extends Wordlift_Admin_Page {

	/**
	 * {@inheritdoc}
	 */
	public function get_page_title() {

		return __( 'Schema.org Types', 'wordlift' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_menu_title() {

		return __( 'Schema.org Types', 'wordlift' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_menu_slug() {

		return 'wl_mappings';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_partial_name() {

		return 'wordlift-admin-mappings-page.php';
	}

}
