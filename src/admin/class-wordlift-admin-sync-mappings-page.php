<?php
/**
 * Pages: Sync Mappings page.
 *
 * Display the list of Mappings Mockup.
 *
 * @since 3.24.0
 * @package Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the Wordlift_Admin_Sync_Mappings_Page class.
 *
 * @since 3.24.0
 */
class Wordlift_Admin_Sync_Mappings_Page extends Wordlift_Admin_Page {
	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		// do nothing, if this constructor is not overridden, duplicate menu entry
		// created.
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_page_title() {

		return __( 'Sync Mappings', 'wordlift' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_menu_title() {

		return __( 'Sync Mappings', 'wordlift' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_menu_slug() {

		return 'wl_mappings_sync';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_partial_name() {

		return 'wordlift-admin-sync-mappings.php';
	}

}
