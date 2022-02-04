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

		wp_enqueue_script(
			'wl-gaddon-import-page',
			plugin_dir_url( __FILE__ ) . 'assets/gaddon-import-page.js',
			array(),
			\Wordlift::get_instance()->get_version()
		);

		wp_localize_script( 'wl-gaddon-import-page', '_wlGaddonImportSettings', array(
			'restUrl'     => get_rest_url(),
			'nonce'       => wp_create_nonce( 'wp_rest' ),
			'entityUrls' => $this->get_entity_urls()
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

	private function get_entity_urls() {
		$entities = $_GET['e'];

		if ( empty( $entities ) ) {
			return array();
		}
		if ( ! is_array( $entities ) ) {
			return array();
		}

		return array_map( function ( $item ) {
			return (string) $item;
		}, $entities );
	}

}