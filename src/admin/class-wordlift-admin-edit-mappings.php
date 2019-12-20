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
 * Define the Wordlift_Admin_Edit_Mappings class.
 *
 * @since 3.24.0
 */
class Wordlift_Admin_Edit_Mappings extends Wordlift_Admin_Page {
	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		// Register scripts needed to be loaded for that page.
		wp_register_script(
			'wl-edit-mappings-script',
			plugin_dir_url( dirname( __FILE__ ) ) . 'js/dist/edit_mappings.js',
			false
		);
		add_action( 'init', 'Wordlift_Admin_Edit_Mappings::load_ui_dependancies' );
	}
	/**
	 * Load Dependancies required for js client.
	 */
	public static function load_ui_dependancies() {
		// Create ui settings array to be used by js client.
		$edit_mapping_settings                     = array();
		$edit_mapping_settings['rest_url']         = get_rest_url(
			null,
			WL_REST_ROUTE_DEFAULT_NAMESPACE . Wordlift_Mapping_REST_Controller::MAPPINGS_NAMESPACE
		);
		$edit_mapping_settings['wl_edit_mapping_rest_nonce'] = wp_create_nonce( 'wp_rest' );
		if ( wp_verify_nonce( $_REQUEST['_wl_edit_mapping_nonce'], 'wl-edit-mapping-nonce' ) ) {
			$edit_mapping_settings['wl_edit_mapping_id'] = $_REQUEST['wl_edit_mapping_id'];
		}
		$edit_mapping_settings['wl_add_mapping_text']  = __( 'Add Mapping', 'wordlift' );
		$edit_mapping_settings['wl_edit_mapping_text'] = __( 'Edit Mapping', 'wordlift' );
		$edit_mapping_settings['wl_edit_mapping_no_item']      = __( 'Unable to find the mapping item', 'wordlift' );
		wp_localize_script( 'wl-edit-mappings-script', 'wlEditMappingsConfig', $edit_mapping_settings );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_page_title() {

		return __( 'Edit Mappings', 'wordlift' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_menu_title() {

		return __( 'Edit Mappings', 'wordlift' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_menu_slug() {

		return 'wl_edit_mapping';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_partial_name() {

		return 'wordlift-admin-edit-mappings.php';
	}

}
