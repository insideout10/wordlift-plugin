<?php
/**
 * Pages: Sync Mappings page.
 *
 * Display the sync mappings page.
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
		wp_register_script(
			'wl-sync-mappings-script',
			plugin_dir_url( dirname( __FILE__ ) ) . 'js/dist/mappings.js',
			false
		);
		add_action(
			'init',
			'Wordlift_Admin_Sync_Mappings_Page::provide_ui_dependancies'
		);

	}

	/**
	 * Provides script and js global values used by react component.
	 */
	public static function provide_ui_dependancies() {
		// Create ui settings array to be used by js client.
		$mapping_settings             = array();
		$mapping_settings['rest_url'] = get_rest_url(
			null,
			WL_REST_ROUTE_DEFAULT_NAMESPACE . Wordlift_Mapping_REST_Controller::MAPPINGS_NAMESPACE
		);
		$mapping_settings['wl_mapping_nonce']             = wp_create_nonce( 'wp_rest' );
		$mapping_settings['wl_edit_mapping_nonce']        = wp_create_nonce( 'wl-edit-mapping-nonce' );
		wp_localize_script( 'wl-sync-mappings-script', 'wlMappingsConfig', $mapping_settings );
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
