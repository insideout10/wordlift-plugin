<?php
namespace Wordlift\Google_Addon_Integration\Pages;

use Wordlift_Admin_Page;

class Import_Page extends Wordlift_Admin_Page {


	/**
	 * {@inheritdoc}
	 */
	public function get_page_title() {

		return __( 'Google Addon Import', 'wordlift' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_menu_title() {

		return __( 'Google Addon Import', 'wordlift' );
	}

	protected function get_parent_slug() {
		return null;
	}

	public function render() {

		wp_localize_script( 'wl-gaddon-import-page', '_wlGaddonImportSettings', array(
			'restUrl' => get_rest_url(),
			'nonce'   => wp_create_nonce( 'wp_rest' )
		) );

		parent::render();
	}


	/**
	 * {@inheritdoc}
	 */
	public function get_menu_slug() {

		return 'wl_google_addon_import';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_partial_name() {
		return 'wordlift-admin-google-addon-import.php';
	}

}