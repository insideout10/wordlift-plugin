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
		return 'wl_google_addon_import';
	}

	public function render() {

		wp_enqueue_script(
			'wl-gaddon-import-page',
			plugin_dir_url( __FILE__ ) . 'assets/gaddon-import-page.js',
			array(),
			WORDLIFT_VERSION,
			false
		);

		wp_localize_script(
			'wl-gaddon-import-page',
			'_wlGaddonImportSettings',
			array(
				'restUrl'    => get_rest_url(),
				'nonce'      => wp_create_nonce( 'wp_rest' ),
				'entityUrls' => $this->get_entity_urls(),
			)
		);

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
		if ( ! isset( $_GET['e'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return array();
		}

		$entities = esc_url_raw( wp_unslash( $_GET['e'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( empty( $entities ) ) {
			return array();
		}
		if ( ! is_string( $entities ) ) {
			return array();
		}

		return explode( ',', $entities );
	}

}
